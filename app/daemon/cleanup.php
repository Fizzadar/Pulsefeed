<?php
	/*
		file: app/process/cron/cleanup.php
		desc: expires stream articles
	*/

	//load modules
	global $mod_db, $mod_config, $mod_memcache;

	//remove mod_db
	$mod_db->__destruct();
	unset( $mod_db );

	//load daemon (db func, thread func, threads, thread time, db time)
	$daemon = new mod_daemon( 'dbupdate', 'cleanup', 1, 60, 1800, 'cleanup' );

	//and go!
	$daemon->start();

	function cleanup( $nothing ) {
		global $mod_config, $c_config;

		//new db
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

		//memcache
		$mod_memcache = new mod_memcache( $mod_db );

		//remove images older than 24 hour - was 48
		$oldtime = time() - ( 3600 * 24 );
		$images = glob( $c_config['core_dir'] . '/../data/images/*' );
		foreach( $images as $img ):
			if( filemtime( $img ) < $oldtime ):
				unlink( $img );
				echo 'old image removed : ' . $img . PHP_EOL;
			endif;
		endforeach;

		//24 hours ago - was 48
		$expire_stream = time() - ( 3600 * 24 );

		//expire articles
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

		//week ago
		$delete_time = time() - ( 3600 * 24 * 7 );

		//delete old mod_user_articles
		$mod_db->query( '
			DELETE FROM mod_user_articles
			WHERE article_time < ' . $delete_time . '
		' );
		echo $mod_db->affected_rows()  . ' user_articles deleted' . PHP_EOL;

		//delete old mod_user_hides
		$mod_db->query( '
			DELETE FROM mod_user_hides
			WHERE time < ' . $delete_time . '
		' );
		echo $mod_db->affected_rows() . ' user_hides deleted' . PHP_EOL;

		//delete old mod_user_reads
		$mod_db->query( '
			DELETE FROM mod_user_reads
			WHERE time < ' . $delete_time . '
		' );
		echo $mod_db->affected_rows() . ' user_reads deleted' . PHP_EOL;
		
		//sync required tables
		$mod_memcache->sync( 'mod_user_likes' );
		$mod_memcache->sync( 'mod_article', 'expired = 0' );

		echo 'tables synced with memcache' . PHP_EOL;
	}

	//db update
	function dbupdate() {
		return array(
			'test'
		);
	}
?>