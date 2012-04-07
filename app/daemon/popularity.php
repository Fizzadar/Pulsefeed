<?php
	/*
		file: app/daemon/popularity.php
		desc: updates popularity of articles in the last 24 hours (daemon)
	*/
	global $mod_db;

	//remove mod_db
	$mod_db->__destruct();
	unset( $mod_db );

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
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 5 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );

		//what do we get
		$data = curl_exec( $curl );

		if( $data and !empty( $data ) ):
			return $data;
		else:
			var_dump( $data );
			echo curl_error( $curl );
			return false;
		endif;
	}

	//function pushed to thread
	function popularity( $article ) {
		global $mod_config;

		//get memcache
		$mod_mcache = get_memcache();

		//child db conn
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

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
			$fb_shares = isset( $fb->shares ) ? $fb->shares : $article['facebook_shares'];
			$fb_comments = isset( $fb->comments ) ? $fb->comments : $article['facebook_comments'];
		else:
			$fb_shares = $article['facebook_shares'];
			$fb_comments = $article['facebook_comments'];
			echo 'fb failed on #' . $article['id'] . "\n";
		endif;

		//get twitter data, stop if over api, and only deal with articles in 24 hours (tweets after that lost)
		if( !@$mod_mcache->get( 'twitter_overload' ) ):
			$tw = get_data( 'http://api.tweetmeme.com/url_info.json?url=' . $url );
			if( $tw ):
				$tw2 = json_decode( $tw );
				if( isset( $tw2->story->url_count ) ):
					$tw_links = $tw2->story->url_count;
				elseif( isset( $tw2->status ) and isset( $tw2->comment ) and $tw2->status == 'failure' and $tw2->comment == 'exceeded rate limit' ):
					echo '#######TWITTER OVERLOAD#######' . PHP_EOL;
					@$mod_mcache->set( 'twitter_overload', true, 0, 1800 );
					$tw_links = $article['twitter_links'];
				else:
					$tw_links = $article['twitter_links'];
					echo 'tw failed on #' . $article['id'] . ' : ' . $article['end_url'] . PHP_EOL;
				endif;
			else:
				$tw_links = $article['twitter_links'];
				echo 'tw failed on #' . $article['id'] . ' : ' . $article['end_url'] . PHP_EOL;
			endif;
		else:
			$tw_links = $article['twitter_links'];
			echo 'twitter skipped, overload set' . PHP_EOL;
		endif;

		//get delicious data
		if( $dl = get_data( 'http://feeds.delicious.com/v2/json/urlinfo/' . md5( $url ) ) ):
			$dl = json_decode( $dl );
			$dl_saves = ( is_array( $dl ) and isset( $dl[0] ) ) ? $dl[0]->total_posts : $article['delicious_saves'];
		else:
			$dl_saves = $article['delicious_saves'];
			echo 'delicious failed on #' . $article['id'] . PHP_EOL;
		endif;

		//get digg data
		if( $dg = get_data( 'http://services.digg.com/2.0/story.getInfo?links=' . $url ) ):
			$dg = json_decode( $dg );
			$dg_diggs = ( isset( $dg->stories ) and is_array( $dg->stories ) and isset( $dg->stories[0] ) ) ? $dg->stories[0]->diggs : $article['digg_diggs'];
		else:
			$dg_diggs = $article['digg_diggs'];
			echo 'digg failed on #' . $article['id'] . PHP_EOL;
		endif;

		//linked in
		if( $ln = get_data( 'http://www.linkedin.com/cws/share-count?url=' . $url ) ):
			$ln = str_replace( 'IN.Tags.Share.handleCount(', '', $ln );
			$ln = json_decode( $ln );
			$ln_shares = isset( $ln->count ) ? $ln->count : $article['linkedin_shares'];
		else:
			$ln_shares = $article['linkedin_shares'];
			echo 'linkedin failed on #' . $article['id'] . PHP_EOL;
		endif;

		//google plus
		$gl = get_data( 'https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ', true, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . urldecode( $url ) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
		if( $gl ):
			$gl = json_decode( $gl );
			if( isset( $gl[0]->result->metadata->globalCounts->count ) ):
				$gl_pluses = $gl[0]->result->metadata->globalCounts->count;
			else:
				$gl_pluses = $article['google_shares'];
				echo 'google failed on #' . $article['id'] . PHP_EOL;
			endif;
		else:
			$gl_pluses = $article['google_shares'];
			echo 'google failed on #' . $article['id'] . PHP_EOL;
		endif;

		//hacker news <= api too shit

		//reddit
		$rd = get_data( 'http://www.reddit.com/api/info.json?url=' . $url );
		if( $rd ):
			$rd = json_decode( $rd );
			if( isset( $rd->data->children[0] ) ):
				$rd_score = $rd->data->children[0]->data->score;
			else:
				$rd_score = $article['reddit_score'];
			endif;
		else:
			$rd_score = $article['reddit_score'];
			echo 'reddit failed on #' . $article['id'] . PHP_EOL;
		endif;

		//calculate popularity
		//facebook
		$pop = $fb_shares * $mod_config['popularity']['facebook_shares'];
		$pop += $fb_comments * $mod_config['popularity']['facebook_comments'];
		//twitter
		$pop += $tw_links * $mod_config['popularity']['twitter_links'];
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
		$pop += $article['likes'] * $mod_config['popularity']['like'];

		//update the article
		$update = $mod_db->query( '
			UPDATE mod_article
			SET
				facebook_shares = ' . $fb_shares . ',
				facebook_comments = ' . $fb_comments . ',
				twitter_links = ' . $tw_links . ',
				delicious_saves = ' . $dl_saves . ',
				digg_diggs = ' . $dg_diggs . ',
				reddit_score = ' . $rd_score . ',
				linkedin_shares = ' . $ln_shares . ',
				google_shares = ' . $gl_pluses . ',
				popularity = ' . $pop . '
			WHERE id = ' . $article['id'] . '
			LIMIT 1
		' );

		//updated?
		if( $update )
			echo 'Article updated: id#' . $article['id'] . ': ' . urldecode( $url ) . PHP_EOL;
		elseif( !$update )
			echo 'Article update failed: id#' . $article['id'] . ': ' . urldecode( $url ) . ' //: ' . mysql_error() . PHP_EOL;

		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//end
		exit( 0 );
	}

	//function used by deamon to get 'jobs'
	function dbupdate() {
		global $mod_config;

		//new db
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

		//min 30 min between pop checks
		$update_time = time() - 1800;

		//select articles to update
		$articles = $mod_db->query( '
			SELECT id, url, end_url, time, likes, facebook_shares, facebook_comments, twitter_links, delicious_saves, digg_diggs, reddit_score, linkedin_shares, google_shares
			FROM mod_article
			WHERE expired = 0
			AND update_time < ' . $update_time . '
			ORDER BY update_time ASC
			LIMIT 500
		' );

		//update the same set of articles update time
		$mod_db->query( '
			UPDATE mod_article
			SET update_time = ' . time() . '
			WHERE expired = 0
			AND update_time < ' . $update_time . '
			ORDER BY update_time ASC
			LIMIT 500
		' );

		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//return to daemon
		return $articles;
	}

	//load daemon (db func, thread func, threads, thread time, db time)
	$daemon = new mod_daemon( 'dbupdate', 'popularity', 30, 15, 60 );

	//and go!
	$daemon->start();
?>