<?php
	/*
		file: app/daemon/inc/popcalc/topic.php
		desc: popcalc server (daemon) => topic update function
	*/
	global $threads, $threadtime, $dbtime;

	$threads = 30;
	$threadtime = 30;
	$dbtime = 60;

	//get user function
	function dbupdate() {
		global $mod_config;

		//new db
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

		//min 30 min between popcalcs
		$update_time = time() - 1800;

		//select users to update
		$users = $mod_db->query( '
			SELECT *, "topic" AS type
			FROM mod_topic
			WHERE update_time < ' . $update_time . '
			ORDER BY update_time ASC
			LIMIT 100
		' );

		//update the same set of users update time
		$mod_db->query( '
			UPDATE mod_topic
			SET update_time = ' . time() . '
			WHERE update_time < ' . $update_time . '
			ORDER BY update_time ASC
			LIMIT 100
		' );

		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//return to daemon
		return $users;
	}
?>