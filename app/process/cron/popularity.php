<?php
	/*
		file: app/process/cron/popularity.php
		desc: updates popularity of articles in the last 24 hours
	*/

	//no time limits
	set_time_limit( 0 );
	//ignore abort
	ignore_user_abort( true );

	//load modules
	global $mod_db, $mod_config;

	//48 hour update time
	$update_time = time() - ( 3600 * $mod_config['article_expire'] );

	//select articles to update (last article_expire hours, 30 max, lowest popularity first [0 ones])
	$articles = $mod_db->query( '
		SELECT id, end_url, time, recommendations
		FROM mod_article
		WHERE time > ' . $update_time . '
		ORDER BY popularity ASC
		LIMIT 30
	' );

	//loop articles
	foreach( $articles as $article ):
		//build url
		$bits = parse_url( $article['end_url'] );
		$url = $bits['scheme'] . '://' . $bits['host'] . $bits['path'];

		//get facebook data
		$fb = @file_get_contents( 'http://graph.facebook.com/' . $url );
		$fb = @json_decode( $fb );
		$fb_shares = isset( $fb->shares ) ? $fb->shares : 0;
		$fb_comments = isset( $fb->comments ) ? $fb->comments : 0;

		//get twitter data
		$tw = @file_get_contents( 'http://search.twitter.com/search.json?q=' . $url );
		$tw = @json_decode( $tw );
		$tw_links = is_array( $tw->results ) ? count( $tw->results ) : 0;

		//get delicious data
		$dl = @file_get_contents( 'http://feeds.delicious.com/v2/json/urlinfo/' . md5( $url ) );
		$dl = @json_decode( $dl );
		$dl_saves = ( is_array( $dl ) and isset( $dl[0] ) ) ? $dl[0]->total_posts : 0;

		//get digg data
		$dg = @file_get_contents( 'http://services.digg.com/2.0/story.getInfo?links=' . $url );
		$dg = @json_decode( $dg );
		$dg_diggs = ( is_array( $dg->stories ) and isset( $dg->stories[0] ) ) ? $dg->stories[0]->diggs : 0;

		//calculate popularity
		$pop = $fb_shares * $mod_config['popularity']['facebook_shares'];
		$pop += $fb_comments * $mod_config['popularity']['facebook_comments'];
		$pop += $tw_links * $mod_config['popularity']['twitter_links'];
		$pop += $dl_saves * $mod_config['popularity']['delicious_saves'];
		$pop += $dg_diggs * $mod_config['popularity']['digg_diggs'];
		$pop += $article['recommendations'] * $mod_config['popularity']['recommend'];

		//calculate popularity time (poptime!)
		$time = time() - $article['time'];
		$time = round( $time / 3600 );
		$pop_time = $pop - ( $time * $mod_config['popularity']['hour'] );

		//update the article
		$update = $mod_db->query( '
			UPDATE mod_article
			SET
				facebook_shares = ' . $fb_shares . ',
				facebook_comments = ' . $fb_comments . ',
				twitter_links = ' . $tw_links . ',
				delicious_saves = ' . $dl_saves . ',
				digg_diggs = ' . $dg_diggs . ',
				popularity = ' . $pop . ',
				popularity_time = ' . $pop_time . '
			WHERE id = ' . $article['id'] . '
			LIMIT 1
		' );

		//updated?
		if( $update )
			echo 'Article updated: id#' . $article['id'] . ', ' . $url . '<br />';
		else
			echo 'Article update failed: id#' . $article['id'] . ', ' . $url . '<br />';
	endforeach;
?>