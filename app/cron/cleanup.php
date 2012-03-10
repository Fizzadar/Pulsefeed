<?php
	/*
		file: app/process/cron/cleanup.php
		desc: expires stream articles
	*/

	//load modules
	global $mod_db, $mod_config;

	//72 hours ago
	$expire_stream = time() - ( 3600 * 72 );

	//expire 48 hour articles
	$mod_db->query( '
		UPDATE mod_article
		SET expired = 1
		WHERE expired = 0
		AND time < ' . $expire_stream . '
	' );
	echo $mod_db->affected_rows() . ' articles set to expired' . PHP_EOL;

	$mod_db->query( '
		UPDATE mod_user_articles
		SET expired = 1
		WHERE expired = 0
		AND article_time < ' . $expire_stream . '
	' );
	echo $mod_db->affected_rows() . ' user_articles set to expired' . PHP_EOL;
?>