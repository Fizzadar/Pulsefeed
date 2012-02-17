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

	//no time limits
	set_time_limit( 300 );
	//ignore abort
	ignore_user_abort( true );

	//load modules
	global $mod_db, $mod_config;

	//48 hours of popularity; fast moving internet
	$update_time = time() - ( 3600 * 48 );
	//min 30 minute between updates
	$utime = time() - 1800;

	//select articles to update (last article_expire hours, 60 max, lowest update time first)
	$articles = $mod_db->query( '
		SELECT id, url, end_url, time, recommendations
		FROM mod_article
		WHERE time > ' . $update_time . '
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

				//get twitter data
				if( $tw = @file_get_contents( 'http://api.tweetmeme.com/url_info.json?url=' . $url, 0, $context ) ):
					$tw = json_decode( $tw );
					$tw_links = isset( $tw->story->url_count ) ? $tw->story->url_count : 0;
				else:
					$tw_links = 0;
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

				//calculate popularity
				$pop = $fb_shares * $mod_config['popularity']['facebook_shares'];
				$pop += $fb_comments * $mod_config['popularity']['facebook_comments'];
				$pop += $tw_links * $mod_config['popularity']['twitter_links'];
				$pop += $dl_saves * $mod_config['popularity']['delicious_saves'];
				$pop += $dg_diggs * $mod_config['popularity']['digg_diggs'];
				$pop += $article['recommendations'] * $mod_config['popularity']['recommend'];

				//update the article
				$update = $child_db->query( '
					UPDATE mod_article
					SET
						facebook_shares = ' . $fb_shares . ',
						facebook_comments = ' . $fb_comments . ',
						twitter_links = ' . $tw_links . ',
						delicious_saves = ' . $dl_saves . ',
						digg_diggs = ' . $dg_diggs . ',
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
	while( pcntl_waitpid( 0, $status ) != -1, WNOHANG OR WUNTRACED ):
		$status = pcntl_wexitstatus( $status );
	endwhile;

	$e_time = time() - $s_time;
	echo 'total time: ' . $e_time . 's' . "\n";
?>