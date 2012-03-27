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
	//loop each auth
	foreach( $oauths as $key => $oauth ):
		//remove token & secret
		unset( $oauths[$key]['token'] );
		unset( $oauths[$key]['secret'] );

		//search to see if syncing
		$sync = $mod_db->query( '
			SELECT disabled
			FROM mod_source
			WHERE feed_url = "' . $oauth['provider'] . '/' . $mod_user->get_userid() . '/' . $oauth['o_id'] . '"
			LIMIT 1
		' );

		if( $sync and is_array( $sync ) ):
			$oauths[$key]['nosync'] = $sync[0]['disabled'];
		endif;
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