<?php
	/*
		file: app/load/user/settings.php
		desc: display user settings
	*/

	global $mod_db, $mod_user, $mod_message, $mod_load;

	//no login?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template;

	//home on api? ie $_GET['load'] not set
	$_GET['load'] = isset( $_GET['load'] ) ? $_GET['load'] : 'settings';
	
	//switch load type
	switch( $_GET['load'] ):
		case 'settings-accounts':
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
					FROM mod_account
					WHERE user_id = ' . $mod_user->get_userid() . '
					AND type = "' . $oauth['provider'] . '"
					AND o_id = ' . $oauth['o_id'] . '
					LIMIT 1
				' );

				if( $sync and is_array( $sync ) ):
					$oauths[$key]['nosync'] = $sync[0]['disabled'];
				else:
					$oauths[$key]['nosync'] = true;
				endif;
			endforeach;
			$mod_template->add( 'oauths', $oauths );

			//get oids
			$oids = $mod_user->get_openids();
			$mod_template->add( 'oids', $oids );
			$mod_template->add( 'pageTitle', 'Settings: Accounts' );
			$template = 'user/accounts';
			break;

		case 'settings-data':
			if( !$mod_user->session_permission( 'Debug' ) ):
				$mod_message->add( 'NotFound' );
				die( header( 'Location: ' . $c_config['root'] ) );
			endif;

			$mod_template->add( 'pageTitle', 'Settings: Data' );
			$template = 'user/data';
			break;

		default:
			//get user data
			$user = $mod_user->get_data();
			//hide auth key
			unset( $user['auth_key'] );
			$mod_template->add( 'settings', $user );
			$mod_template->add( 'pageTitle', 'Settings' );
			$template = 'user/settings';
	endswitch;

	$mod_template->load( 'core/header' );
	$mod_template->load( $template );

	//footer
	$mod_template->load( 'core/footer' );
?>