<?php
	global $mod_db, $mod_user, $mod_app, $mod_config;

	//are we logged in? load hybrid feed
	if( $mod_user->check_login() ):
		if( !$mod_config['api'] ):
			$_GET['id'] = $mod_user->get_userid();
			$mod_app->load( 'load/stream/user' );
		else:
			$mod_app->load( 'load/user/settings' );
		endif;
	else:
		//start template
		$template = new mod_template();

		//load header
		$template->load( 'core/header' );

		//home
		$template->load( 'home' );
		
		//footer
		$template->load( 'core/footer' );
	endif;
?>