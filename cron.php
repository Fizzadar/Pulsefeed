<?php
	//set cron
	$_GET['iscron'] = true;

	//set some server vars
	$_SERVER['HTTP_HOST'] = '';

	//get index, which returns early
	require( 'index.php' );

	//no cron time set?
	if( !isset( $argv[1] ) )
		die();

	//now switch our time value
	switch( $argv[1] ):
		case '5min':
			$mod_app->load( 'process/cron/popularity' );
			break;
		case '10min':
			$mod_app->load( 'process/cron/update' );
			break;
		case '30min':
			$mod_app->load( 'process/cron/popcalc' );
			break;
		case 'day':
			$mod_app->load( 'process/cron/cleanup' );
			break;
	endswitch;
?>