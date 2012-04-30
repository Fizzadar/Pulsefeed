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
		//$mod_app->load( 'load/stream/public' );
		//die();

		//start template
		$mod_template = new mod_template();

		//random intro texts / Pulsefeed is...
		$intro = $mod_config['is'][mt_rand( 0, count( $mod_config['is'] ) - 1)];

		//load header
		$mod_template->add( 'stream', true );
		$mod_template->add( 'pageTitle', 'Pulsefeed is ' . $intro );
		$mod_template->load( 'core/header' );

		//home
		$mod_template->add( 'introText', $intro );
		$mod_template->load( 'home' );
		
		//footer
		$mod_template->load( 'core/footer' );
	endif;
?>