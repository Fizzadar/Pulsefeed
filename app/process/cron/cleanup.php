<?php
	/*
		file: app/process/cron/cleanup.php
		desc: cleans up old unread markers ($mod_config['article_expire'] hours old)
	*/

	//no time limits
	set_time_limit( 0 );
	//ignore abort
	ignore_user_abort( true );

	//load modules
	global $mod_db, $mod_config;

	//select articles older than our expire
	$expire_time = time() - ( 3600 * $mod_config['article_expire'] );
	$articles = $mod_db->query( '
		SELECT id
		FROM mod_article
		WHERE time < ' . $expire_time . '
	' );

	//remove unreads
	foreach( $articles as $article ):
		$mod_db->query( '
			DELETE FROM mod_user_unread
			WHERE article_id = ' . $article['id'] . '
		' );
		echo 'Unreads deleted for article #' . $article['id'] . '<br />';
	endforeach;
?>