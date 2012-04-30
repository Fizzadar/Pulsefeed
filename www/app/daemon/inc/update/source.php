<?php
	/*
		file: app/daemon/inc/source.php
		desc: update server (daemon) => source dbupdate function
	*/
	global $threads, $threadtime, $dbtime;

	//setup update data
	$threads = 30;
	$threadtime = 300;
	$dbtime = 60;

	//function used by deamon to get 'jobs'
	function dbupdate() {
		global $mod_config, $argv, $c_config;

		//new db
		$mod_db = get_db();

		//min 60 min between source checks
		$update_time = time() - 3600;

		//select articles to update (last article_expire hours, 60 max, lowest update time first)
		$sources = $mod_db->query( '
			SELECT id, feed_url, update_time, owner_id, site_title, site_url, "source" AS type
			FROM mod_source
			WHERE update_time < ' . $update_time . '
			AND id > 0
			AND subscribers > 0
			AND disabled = 0
			ORDER BY update_time ASC
			LIMIT 200
		' );

		$mod_db->query( '
			UPDATE mod_source
			SET update_time = ' . time() . '
			WHERE update_time < ' . $update_time . '
			AND id > 0
			AND subscribers > 0
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