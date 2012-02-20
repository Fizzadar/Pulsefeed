<?php
	/*
		file: app/load/source/add.php
		desc: add source form
	*/

	//modules
	global $mod_user, $mod_message;

	//logged in?
	if( !$mod_user->session_login() ):
		$mod_message->add( 'NeedToLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template();

	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'source/add' );
	$mod_template->load( 'core/footer' );
?>