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
	
	//get the core
	require( 'core/core.php' );

	//get the config
	require( 'app/config.php' );
	require( 'app/config.ext.php' );
	
	//enable debug
	$c_debug->enable();

	//start the app
	$mod_app = new c_app( $mod_config['libs'] );

	//start our db
	$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );

	//user & setup
	$mod_user = new c_user( $mod_db, 'feedbug_' );
	$mod_user->set_facebook( '346508828699100', '85804588b0a5a0e005bdca184dae17b5' );
	$mod_user->set_twitter( '9CxR2vqndROknYPJ9vlpw', 'bPnQZYzamUsUoqmdsuztxBmNwEqiqDSsg9IVj9WujyA' );

	//session
	$mod_session = new c_session;
	$mod_token = $mod_session->generate();

	//cookie management
	$mod_cookie = new mod_cookie( 'feedbug_' );

	//message (after session to get that started)
	$mod_message = new mod_message( $mod_config['messages'] );

	//data
	$mod_data = new mod_data;

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

	//debug
	if( $mod_user->check_permission( 'Debug' ) )
		$c_debug->display();
?>