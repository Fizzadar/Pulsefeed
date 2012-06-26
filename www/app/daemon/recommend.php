<?php
	/*
		file: app/daemon/recommend.php
		desc: recommend server (daemon)
	*/
	global $mod_db, $argv, $mod_app, $threads, $threadtime, $dbtime;
	
	//remove mod_db
	$mod_db->__destruct();
	unset( $mod_db );

	//inc relevant update file
	if( !isset( $argv[2] ) or !in_array( $argv[2], array( 'user' ) ) )
		die();

	//data
	$threads = 10;
	$threadtime = 300;
	$dbtime = 60;

	//load inc bit
	$mod_app->load( 'daemon/inc/recommend/' . $argv[2] );

	//load daemon (db func, thread func, threads, thread time, db time)
	$daemon = new mod_daemon( 'dbupdate', 'recommend', $threads, $threadtime, $dbtime, 'recommend_' . $argv[2], 1000 );

	//and go!
	$daemon->start();
?>