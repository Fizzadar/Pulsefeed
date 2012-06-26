<?php
	/*
		file: app/daemon/inc/website.php
		desc: update server (daemon) => website dbupdate function
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

		//min 60 min between website checks
		$update_time = time() - 3600;

		//select articles to update (last article_expire hours, 60 max, lowest update time first)
		$websites = $mod_db->query( '
			SELECT id, feed_url, update_time, owner_id, site_title, site_url, "website" AS type, subscribers
			FROM mod_website
			WHERE update_time < ' . $update_time . '
			AND id > 0
			AND subscribers > 0
			AND disabled = 0
			ORDER BY update_time ASC
			LIMIT 200
		' );

		$mod_db->query( '
			UPDATE mod_website
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
		return $websites;
	}
?>