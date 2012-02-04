<?php
	//invalid cron?
	if( !isset( $argv[1] ) or !file_exists( __DIR__ . '/app/process/cron/' . $argv[1] . '.php' ) )
		die( 'Invalid cron: ' . __DIR__ . '/app/process/cron/' . $argv[1] . '.php' );
	
	//ok lets go
	$_GET['process'] = 'cron-' . $argv[1];
	require( 'index.php' );

	exit( 0 );
?>