<?php
	/*
		file: app/process/cron/popularity.php
		desc: updates popularity of articles in the last 24 hours (every 5 min)
	*/

	//config
	$children = 20;
	$childarticles = 30;

	//time!
	$s_time = time();
	echo '
###############
############### ===> Pulsefeed Popularity Updater
###############
';
	echo 'Starting @: ' . time() . "\n";

	//load modules
	global $mod_db, $mod_config;

	//min 30 minute between updates
	$utime = time() - 1800;

	//select articles to update (last article_expire hours, 60 max, lowest update time first)
	$articles = $mod_db->query( '
		SELECT id, url, end_url, time, recommendations, twitter_last_id
		FROM mod_article
		WHERE expired_stream = 0
		AND update_time < ' . $utime . '
		ORDER BY update_time ASC
		LIMIT ' . ( $children * $childarticles ) . '
	' );
	//no longer needed (without causes major fail )
	$mod_db->__destruct();
	unset( $mod_db );

	//build childdata array
	$childdata = array();
	for( $i = 0; $i < $children; $i++ ):
		$childdata[$i] = array();
	endfor;

	//distribute our children
	$childcount = 0;
	foreach( $articles as $article ):
		//reset?
		if( $childcount > $children ) $childcount = 0;
		//insert
		$childdata[$childcount][] = $article;
		$childcount++;
	endforeach;

	//file_get_contents context
	$context = stream_context_create( array(
	    'http' => array(
	        'timeout' => 15
	    )
	));

	//spawn the children!
	for( $i = 0; $i < $children; $i++ ):
		//fork
		$pid = pcntl_fork();

		//child?
		if( !$pid ):
			//child db conn
			$child_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
			$child_db->connect();

			$articles = $childdata[$i];
			echo '[Child ' . $i . '] got ' . count( $articles ) . ' articles' . "\n";

			//loop articles
			foreach( $articles as $article ):
				//build url
				$bits = parse_url( $article['end_url'] );

				//do we have a host?
				if( isset( $bits['host'] ) and isset( $bits['scheme'] ) and isset( $bits['path'] ) )
					$url = $bits['scheme'] . '://' . $bits['host'] . $bits['path'];
				else
					$url = empty( $article['end_url'] ) ? $article['url'] : $article['end_url'];

				//finally, urlencode the url
				$url = urlencode( $url );

				//get facebook data
				if( $fb = @file_get_contents( 'http://graph.facebook.com/' . $url, 0, $context ) ):
					$fb = json_decode( $fb );
					$fb_shares = isset( $fb->shares ) ? $fb->shares : 0;
					$fb_comments = isset( $fb->comments ) ? $fb->comments : 0;
				else:
					$fb_shares = 0;
					$fb_comments = 0;
				endif;

				//get twitter data (currently tweetmeme)
				/*
				if( $tw = @file_get_contents( 'http://api.tweetmeme.com/url_info.json?url=' . $url, 0, $context ) ):
					$tw = json_decode( $tw );
					$tw_links = isset( $tw->story->url_count ) ? $tw->story->url_count : 0;
				else:
					$tw_links = 0;
				endif;
				*/
				if( $tw = @file_get_contents( 'http://search.twitter.com/search.json?rpp=100&result_type=recent&since_id=' . $article['twitter_last_id'] . '&q=' . $url, 0, $context ) ):
					$tw = json_decode( $tw );
					$tw_last_id = isset( $tw->max_id ) ? $tw->max_id : 0;
					$tw_tweets = ( isset( $tw->results ) and is_array( $tw->results ) ) ? count( $tw->results ) : 0;
				else:
					$tw_last_id = 0;
					$tw_tweets = 0;
				endif;

				//get delicious data
				if( $dl = @file_get_contents( 'http://feeds.delicious.com/v2/json/urlinfo/' . md5( $url ), 0, $context ) ):
					$dl = json_decode( $dl );
					$dl_saves = ( is_array( $dl ) and isset( $dl[0] ) ) ? $dl[0]->total_posts : 0;
				else:
					$dl_saves = 0;
				endif;

				//get digg data
				if( $dg = @file_get_contents( 'http://services.digg.com/2.0/story.getInfo?links=' . $url, 0, $context ) ):
					$dg = json_decode( $dg );
					$dg_diggs = ( is_array( $dg->stories ) and isset( $dg->stories[0] ) ) ? $dg->stories[0]->diggs : 0;
				else:
					$dg_diggs = 0;
				endif;

				//linked in
				if( $ln = @file_get_contents( 'http://www.linkedin.com/cws/share-count?url=' . $url, 0, $context ) ):
					$ln = str_replace( 'IN.Tags.Share.handleCount(', '', $ln );
					$ln = json_decode( $ln );
					$ln_shares = isset( $ln->count ) ? $ln->count : 0;
				else:
					$ln_shares = 0;
				endif;

				//google plus
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, 'https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ' );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . urldecode( $url ) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
				$curl_results = @curl_exec( $ch );
				curl_close( $ch );
				$gl = json_decode( $curl_results );
				$gl_pluses = isset( $gl[0]->result->metadata->globalCounts->count ) ? $gl[0]->result->metadata->globalCounts->count : 0;

				//hacker news <= api too shit

				//reddit
				if( $rd = @file_get_contents( 'http://www.reddit.com/api/info.json?url=' . $url, 0, $context ) ):
					$rd = json_decode( $rd );
					$rd_score = isset( $rd->data->children[0] ) ? $rd->data->children[0]->data->score : 0;
				else:
					$rd_score = 0;
				endif;

				//calculate popularity
				//facebook
				$pop = $fb_shares * $mod_config['popularity']['facebook_shares'];
				$pop += $fb_comments * $mod_config['popularity']['facebook_comments'];
				//twitter
				$pop += $tw_tweets * $mod_config['popularity']['twitter_links'];
				//delicious
				$pop += $dl_saves * $mod_config['popularity']['delicious_saves'];
				//diggs
				$pop += $dg_diggs * $mod_config['popularity']['digg_diggs'];
				//reddit
				$pop += round( $rd_score * $mod_config['popularity']['reddit_score'] );
				//linkedin
				$pop += $ln_shares * $mod_config['popularity']['linked_shares'];
				//google plus
				$pop += $gl_pluses * $mod_config['popularity']['google_pluses'];
				//pulsefeed likes
				$pop += $article['recommendations'] * $mod_config['popularity']['recommend'];

				//update the article
				$update = $child_db->query( '
					UPDATE mod_article
					SET
						facebook_shares = ' . $fb_shares . ',
						facebook_comments = ' . $fb_comments . ',
						twitter_links = ' . $tw_tweets . ',
						twitter_last_id = ' . $tw_last_id . ',
						delicious_saves = ' . $dl_saves . ',
						digg_diggs = ' . $dg_diggs . ',
						reddit_score = ' . $rd_score . ',
						linkedin_shares = ' . $ln_shares . ',
						google_shares = ' . $gl_pluses . ',
						popularity = ' . $pop . ',
						update_time = ' . time() . '
					WHERE id = ' . $article['id'] . '
					LIMIT 1
				' );

				//updated?
				if( $update )
					echo '[Child ' . $i . '] Article updated: id#' . $article['id'] . ': ' . urldecode( $url ) . "\n";
				else
					echo '[Child ' . $i . '] Article update failed: id#' . $article['id'] . ': ' . urldecode( $url ) . ' //: ' . mysql_error() . "\n";
			endforeach;

			//we're a child, so exit
			exit();
		endif;
	endfor;

	//wait for our children
	while( pcntl_waitpid( 0, $status, WNOHANG OR WUNTRACED ) != -1 ):
		$status = pcntl_wexitstatus( $status );
	endwhile;

	$e_time = time() - $s_time;
	echo 'total time: ' . $e_time . 's' . "\n";
?>