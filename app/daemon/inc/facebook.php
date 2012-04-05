<?php
	/*
		file: app/daemon/inc/facebook.php
		desc: update server (daemon) => facebook dbupdate function
	*/
	global $threads, $threadtime, $dbtime;

	//setup update data
	$threads = 20;
	$threadtime = 600;
	$dbtime = 60;

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
			SELECT user_id, auth_data, "facebook" AS type, 0 AS id
			FROM mod_account
			WHERE update_time < ' . $update_time . '
			AND type = "facebook"
			AND disabled = 0
			ORDER BY update_time ASC
			LIMIT 200
		' );

		$mod_db->query( '
			UPDATE mod_account
			SET update_time = ' . time() . '
			WHERE update_time < ' . $update_time . '
			AND type = "facebook"
			AND disabled = 0
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