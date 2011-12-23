<?php
	/*
		file: app/process/user/load.php
		desc: load users settings
	*/

	//modules
	global $mod_db, $mod_cookie, $mod_user, $mod_message;

	//no login?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//load our settings
	$settings = $mod_db->query( '
		SELECT setting_color
		FROM core_user
		WHERE id = ' . $mod_user->get_userid() . '
		LIMIT 1
	' );
	if( isset( $settings ) and count( $settings ) == 1 ):
		$mod_cookie->set( 'SettingColor', $settings[0]['setting_color'] );
	endif;
?>