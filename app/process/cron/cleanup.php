<?php
	/*
		file: app/process/cron/cleanup.php
		desc: cleans up old unread markers ($mod_config['article_expire'] hours old) (every day)
	*/

	//no time limits
	set_time_limit( 300 );
	//ignore abort
	ignore_user_abort( true );

	//load modules
	global $mod_db, $mod_config;

	//a week ago
	$expire_time = time() - ( 3600 * 24 * 7 );
	//week + 24 hours
	$expire_old = $expire_time - ( 3600 * 24 * 8 );

	$articles = $mod_db->query( '
		SELECT id
		FROM mod_article
		WHERE time < ' . $expire_time . '
		AND time > ' . $expire_old . '
	' );

	//remove unreads
	foreach( $articles as $article ):
		//delete unreads
		$mod_db->query( '
			DELETE FROM mod_user_unread
			WHERE article_id = ' . $article['id'] . '
		' );
		//set poptime = 0 on old articles
		$mod_db->query( '
			UPDATE mod_article
			SET popularity_time = 0
			WHERE article_id = ' . $article['id'] . '
		' );
		echo 'Unreads deleted for article #' . $article['id'] . "\n";
	endforeach;
?>