<?php
	/*
		file: app/load/user/settings.php
		desc: display user settings
	*/

	global $mod_db, $mod_user, $mod_message;

	//no login?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template;

	//get user data
	$user = $mod_user->get_data();
	//hide auth key
	unset( $user['auth_key'] );
	$mod_template->add( 'settings', $user );

	//get oauths
	$oauths = $mod_user->get_oauths();
	//remove unwanted data
	foreach( $oauths as $key => $oauth ):
		unset( $oauths[$key]['token'] );
		unset( $oauths[$key]['secret'] );
	endforeach;
	$mod_template->add( 'oauths', $oauths );

	//get oids
	$oids = $mod_user->get_openids();
	$mod_template->add( 'oids', $oids );

	//display templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'user/settings' );
	$mod_template->load( 'core/footer' );
?>