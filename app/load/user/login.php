<?php
	global $mod_db, $mod_user, $mod_app, $mod_cookie;

	//where are we going after logging in?
	$mod_cookie->set( 'redirectUrl', isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] );

	//start template
	$template = new mod_template();

	//load header
	$template->load( 'core/header' );

	//home
	$template->load( 'login' );
	
	//footer
	$template->load( 'core/footer' );
?>