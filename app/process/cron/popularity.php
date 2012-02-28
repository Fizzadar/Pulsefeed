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
	global $mod_db, $mod_config, $argv;
	$debug = false;

	//one child for debug
	if( isset( $argv[2] ) and $argv[2] == 'debug' )
		$children = 1;

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

	//get data
	function get_data( $url, $post = false, $post_data = '' ) {
		$curl = curl_init();

		//post?
		if( $post ):
			curl_setopt( $curl, CURLOPT_POST, $post );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
		endif;

		//options
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 5 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );

		//what do we get
		$data = curl_exec( $curl );

		if( $data )
			return $data;
		else
			return false;
	}

	//popularity function
	function popularity( $i, $articles ) {
		global $mod_config, $argv;

		//child db conn
		$child_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$child_db->connect();

		//echo count
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
			if( $fb = get_data( 'http://graph.facebook.com/' . $url ) ):
				$fb = json_decode( $fb );
				$fb_shares = isset( $fb->shares ) ? $fb->shares : 0;
				$fb_comments = isset( $fb->comments ) ? $fb->comments : 0;
			else:
				$fb_shares = 0;
				$fb_comments = 0;
				echo 'fb failed on #' . $article['id'] . "\n";
			endif;

			//get twitter data
			$tw = get_data( 'http://search.twitter.com/search.json?rpp=100&result_type=recent&since_id=' . $article['twitter_last_id'] . '&q=' . $url );
			if( $tw ):
				$tw = json_decode( $tw );
				if( isset( $tw->max_id ) and isset( $tw->results ) and is_array( $tw->results ) ):
					$tw_last_id = $tw->max_id;
					$tw_tweets = count( $tw->results );
				else:
					$tw_last_id = 0;
					$tw_tweets = 0;
					echo 'twitter failed on #' . $article['id'] . ' : ' . var_dump( $tw ) . "\n";
				endif;
			else:
				$tw_last_id = 0;
				$tw_tweets = 0;
				echo 'twitter failed on #' . $article['id'] . ' : ' . var_dump( $tw ) . "\n";
			endif;

			//get delicious data
			if( $dl = get_data( 'http://feeds.delicious.com/v2/json/urlinfo/' . md5( $url ) ) ):
				$dl = json_decode( $dl );
				$dl_saves = ( is_array( $dl ) and isset( $dl[0] ) ) ? $dl[0]->total_posts : 0;
			else:
				$dl_saves = 0;
				echo 'delicious failed on #' . $article['id'] . "\n";
			endif;

			//get digg data
			if( $dg = get_data( 'http://services.digg.com/2.0/story.getInfo?links=' . $url ) ):
				$dg = json_decode( $dg );
				$dg_diggs = ( is_array( $dg->stories ) and isset( $dg->stories[0] ) ) ? $dg->stories[0]->diggs : 0;
			else:
				$dg_diggs = 0;
				echo 'digg failed on #' . $article['id'] . "\n";
			endif;

			//linked in
			if( $ln = get_data( 'http://www.linkedin.com/cws/share-count?url=' . $url ) ):
				$ln = str_replace( 'IN.Tags.Share.handleCount(', '', $ln );
				$ln = json_decode( $ln );
				$ln_shares = isset( $ln->count ) ? $ln->count : 0;
			else:
				$ln_shares = 0;
				echo 'linkedin failed on #' . $article['id'] . "\n";
			endif;

			//google plus
			$gl = get_data( 'https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ', true, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . urldecode( $url ) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
			if( $gl ):
				$gl = json_decode( $gl );
				if( isset( $gl[0]->result->metadata->globalCounts->count ) ):
					$gl_pluses = $gl[0]->result->metadata->globalCounts->count;
				else:
					$gl_pluses = 0;
					echo 'google failed on #' . $article['id'] . ' : ' . var_dump( $gl ) . "\n";
				endif;
			else:
				$gl_pluses = 0;
				echo 'google failed on #' . $article['id'] . ' : ' . var_dump( $gl ) . "\n";
			endif;

			//hacker news <= api too shit

			//reddit
			$rd = get_data( 'http://www.reddit.com/api/info.json?url=' . $url );
			if( $rd ):
				$rd = json_decode( $rd );
				if( isset( $rd->data->children[0] ) ):
					$rd_score = $rd->data->children[0]->data->score;
				else:
					$rd_score = 0;
				endif;
			else:
				$rd_score = 0;
				echo 'reddit failed on #' . $article['id'] . ' : ' . var_dump( $rd ) . "\n";
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
			if( $update and ( !isset( $argv[2] ) or $argv[2] != 'debug' ) )
				echo '[Child ' . $i . '] Article updated: id#' . $article['id'] . ': ' . urldecode( $url ) . "\n";
			elseif( !$update )
				echo '[Child ' . $i . '] Article update failed: id#' . $article['id'] . ': ' . urldecode( $url ) . ' //: ' . mysql_error() . "\n";
		endforeach;

		//end
		exit( 0 );
	}

	//build our threads
	$threads = array();
	foreach( $childdata as $key => $articles ):
		$threads[$key] = new Thread( 'popularity' );
		$threads[$key]->start( $key, $articles );
	endforeach;

	//wait on them
	$timer = 0;
	while( !empty( $threads ) ):
		//loop threads, check stopped
		foreach( $threads as $key => $thread ):
			//timer check
			if( $timer > 300 ):
				echo 'Warning: timer above 300, killing thread#' . $key . "\n";
				$thread->stop();
			endif;
			//alive?
			if( !$thread->isAlive() ):
				unset( $threads[$key] );
			endif;
		endforeach;
		//sleep
		sleep( 1 );
		//up timer
		$timer++;
	endwhile;

	//debug
	$e_time = time() - $s_time;
	echo 'total time: ' . $e_time . 's' . "\n";
?>