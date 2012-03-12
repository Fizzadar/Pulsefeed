<?php
	/*
		  _____        _           __              _ 
		 |  __ \      | |         / _|            | |
		 | |__) |_   _| |___  ___| |_ ___  ___  __| |
		 |  ___/| | | | / __|/ _ \  _/ _ \/ _ \/ _` |
		 | |    | |_| | \__ \  __/ ||  __/  __/ (_| |
		 |_|     \__,_|_|___/\___|_| \___|\___|\__,_|
                          
		file: index.php
		desc: class loading, app routing
	*/
	
	//disable error reporting unless localhost
	if( $_SERVER['HTTP_HOST'] != 'pulsefeed.dev' )
		ini_set( 'display_errors', 0 );


	//get the core
	require( 'core/core.php' );

	//get the config
	require( 'app/config.php' );
	require( 'app/config.ext.php' );


	//set our header if api
	if( $mod_config['api'] )
		header( 'Content-Type: application/json' );


	//start the app
	$mod_app = new c_app( $mod_config['libs'] );

	//start query memcache
	$mod_querycache = new Memcache;
	//add servers
	foreach( $mod_config['memcache']['query'] as $host => $port )
		$mod_querycache->addServer( $host, $port );

	//start our db
	$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'], $mod_querycache );

	//memcache <=> db
	$mod_memcache = new mod_memcache( $mod_db );
	
	//start stream memcache
	$mod_streamcache = new Memcache;
	//add servers
	foreach( $mod_config['memcache']['stream'] as $host => $port )
		$mod_streamcache->addServer( $host, $port );


	//if cron, return here
	if( isset( $_GET['iscron'] ) and $_GET['iscron'] ) return;
	

	//user & setup
	$mod_user = new c_user( $mod_db, 'pulsefeed_' );
	$mod_user->set_facebook( $mod_config['apps']['facebook']['id'], $mod_config['apps']['facebook']['token'] );
	$mod_user->set_twitter( $mod_config['apps']['twitter']['id'], $mod_config['apps']['twitter']['token'] );

	//enable debug if allowed (and allow error display, even if not localhost)
	if( !$mod_config['api'] and !$mod_config['ajax'] and $mod_user->check_permission( 'Debug' ) ):
		$c_debug->enable();
		ini_set( 'display_errors', E_ALL );
	endif;

	//session
	$mod_session = new c_session;
	$mod_token = $mod_session->generate();

	//cookie management
	$mod_cookie = new mod_cookie( 'pulsefeed_' );

	//message (after session to get that started)
	$mod_message = new mod_message( $mod_config['messages'] );

	//data
	$mod_data = new mod_data;
	
	//load
	$mod_load = new mod_load( $mod_db, $mod_data );


	//process(must be posted)
	if( isset( $_GET['process'] ) and isset( $mod_config['process'][$_GET['process']] ) ):
		$mod_app->load( 'process/' . $mod_config['process'][$_GET['process']] );
	//load
	elseif( isset( $_GET['load'] ) and isset( $mod_config['load'][$_GET['load']] ) ):
		$mod_app->load( 'load/' . $mod_config['load'][$_GET['load']] );
	//default
	else:
		$mod_app->load( 'load/' . $mod_config['load']['default'] );
	endif;


	//debug (only works if enabled above)
	$c_debug->display();
?>