<?php
	/*
		file: app/process/cron/cleanup.php
		desc: expires stream articles
	*/

	//load modules
	global $mod_db, $mod_config, $mod_memcache;

	//72 hours ago
	$expire_stream = time() - ( 3600 * 48 );

	//expire 48 hour articles
	$mod_db->query( '
		UPDATE mod_article
		SET expired = 1
		WHERE expired = 0
		AND time < ' . $expire_stream . '
	' );
	echo $mod_db->affected_rows() . ' articles set to expired' . PHP_EOL;
	//expire on mod_user_articles
	$mod_db->query( '
		UPDATE mod_user_articles
		SET expired = 1
		WHERE expired = 0
		AND article_time < ' . $expire_stream . '
	' );
	echo $mod_db->affected_rows() . ' user_articles set to expired' . PHP_EOL;

	//remove images older than 48 hour
	$oldtime = time() - ( 3600 * 48 );
	$images = glob( $c_config['core_dir'] . '/../data/images/*' );
	foreach( $images as $img ):
		if( filemtime( $img ) < $oldtime ):
			unlink( $img );
			echo 'old image removed : ' . $img . PHP_EOL;
		endif;
	endforeach;

	//sync required tables
	$mod_memcache->sync( 'mod_user_likes' );
	echo 'tables synced with memcache' . PHP_EOL;
?>