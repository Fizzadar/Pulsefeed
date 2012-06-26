<?php
	/*
		file: app/load/admin/home.php
		desc: admin home page
	*/

	//modules
	global $mod_user, $mod_message, $mod_db, $mod_config;

	//permission?
	if( !$mod_user->check_permission( 'Admin' ) or $mod_config['api'] ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template;

	//template
	$mod_template->load( 'core/header' );
	$mod_template->load( 'admin/home' );
	$mod_template->load( 'core/footer' );
?>