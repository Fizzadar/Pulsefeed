<?php
	//are we cli?
	if( !isset( $argv ) )
		die( 'Naw. <a href="/">Home &rarr;</a>' );

	//set cron
	$_GET['iscron'] = true;

	//set some server vars
	$_SERVER['HTTP_HOST'] = '';

	//get index, which returns early
	require( 'index.php' );

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
		default:
			die( 'invalid cron' . PHP_EOL );
	endswitch;
?>