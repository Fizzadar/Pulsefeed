<?php
	global $mod_db, $mod_user, $mod_app;

	//are we logged in? load hybrid feed
	if( $mod_user->check_login() ):
		$_GET['id'] = $mod_user->get_userid();
		die( $mod_app->load( 'load/user' ) );
	endif;

	//start template
	$template = new mod_template();

	//load header
	$template->load( 'core/header' );

	//home
	$template->load( 'home' );
	
	//footer
	$template->load( 'core/footer' );
?>