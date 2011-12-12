<?php
	//invalid cron?
	if( !defined( 'STDIN' ) or !isset( $argv[1] ) or !in_array( $argv[1], array( 'popularity', 'update', 'cleanup' ) ) )
		die( 'Invalid cron' );

	//set some stuff
	$_SERVER['HTTP_HOST'] = '';
	
	//ok lets go
	$_GET['process'] = 'cron-' . $argv[1];
	require( 'index.php' );

	exit( 1 );
?>