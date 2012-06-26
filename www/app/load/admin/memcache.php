<?php
	/*
		file: app/load/admin/topics.php
		desc: admin topics page
	*/

	//modules
	global $mod_user, $mod_message, $mod_config;

	//permission?
	if( !$mod_user->check_permission( 'Admin' ) or $mod_config['api'] ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template;

	//host:port => name
	$memcache_names = array();

	//memcache
	$memcache = new memcache;
	foreach( $mod_config['memcache'] as $key => $memcaches ):
		foreach( $memcaches as $host => $port ):
			$memcache->addServer( $host, $port );

			//name
			if( !isset( $memcache_names[$host . ':' . $port] ) )
				$memcache_names[$host . ':' . $port] = array();

			//names
			$memcache_names[$host . ':' . $port][] = $key;
		endforeach;
	endforeach;

	//get stats, add to template
	$mod_template->add( 'memcaches', $memcache->getExtendedStats() );
	$mod_template->add( 'memcache_names', $memcache_names );

	//template
	$mod_template->load( 'core/header' );
	$mod_template->load( 'admin/memcache' );
	$mod_template->load( 'core/footer' );
?>