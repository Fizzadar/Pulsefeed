<?php
	/*
		file: app/daemon/inc/facebook.php
		desc: update server (daemon) => facebook dbupdate function
	*/

	//function used by deamon to get 'jobs'
	function dbupdate() {
		global $mod_config, $argv, $c_config;

		//new db
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

		//min 30 min between source checks
		$update_time = time() - 1800;

		//select articles to update (last article_expire hours, 60 max, lowest update time first)
		$sources = $mod_db->query( '
			SELECT id, feed_url, type, update_time, owner_id, site_title, site_url
			FROM mod_source
			WHERE update_time < ' . $update_time . '
			AND id > 0
			AND type = "facebook"
			ORDER BY update_time ASC
			LIMIT 200
		' );

		$mod_db->query( '
			UPDATE mod_source
			SET update_time = ' . time() . '
			WHERE update_time < ' . $update_time . '
			AND id > 0
			AND type = "facebook"
			ORDER BY update_time ASC
			LIMIT 200
		' );
	
		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//return to daemon
		return $sources;
	}
?>