<?php
	/*
		file: system.php
		desc: runs daemons
	*/

	//are we cli?
	if( !isset( $argv ) )
		die( header( 'Location: http://pulsefeed.com' ) );

	//set cron
	$_GET['iscron'] = true;

	//set some server vars
	$_SERVER['HTTP_HOST'] = '';

	//ignore user abort
	ignore_user_abort( true );
	//time limit
	set_time_limit( 0 );
	
	//get index, which returns early
	require( 'www/index.php' );

	//special cron func
	function get_memcache() {
		global $mod_config;
		
		//start maintenance memcache
		$mod_mcache = new Memcache;
		//add servers
		foreach( $mod_config['memcache']['maintenance'] as $host => $port )
			$mod_mcache->addServer( $host, $port );

		//return the object
		return $mod_mcache;
	}

	//special cron func
	function get_db( $mod_mcache = false ) {
		global $mod_config;

		//db
		if( $mod_mcache )
			$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'], $mod_mcache );
		else
			$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );

		//connect
		$mod_db->connect();

		//return
		return $mod_db;
	}

	//no cron time set?
	if( !isset( $argv[1] ) )
		die( 'invalid cron' . PHP_EOL );

	//now switch our time value
	switch( $argv[1] ):
		case 'popularity':
			$mod_app->load( 'daemon/popularity' );
			break;
		case 'update':
			$mod_app->load( 'daemon/update' );
			break;
		case 'popcalc':
			$mod_app->load( 'daemon/popcalc' );
			break;
		case 'cleanup':
			$mod_app->load( 'daemon/cleanup' );
			break;
		case 'recommend':
			$mod_app->load( 'daemon/recommend' );
			break;
		default:
			die( 'invalid cron' . PHP_EOL );
	endswitch;
?>