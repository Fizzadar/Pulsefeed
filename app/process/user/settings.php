<?php
	/*
		file: app/process/user/settings.php
		desc: save user settings
	*/

	global $mod_session, $mod_db, $mod_user, $mod_message, $mod_cookie;

	$redir = $c_config['root'] . '/settings';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check post data
	if( !isset( $_POST['username'] ) or empty( $_POST['username'] ) or !isset( $_POST['email'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//hmtl ents on username
	$_POST['username'] = filter_var( $_POST['username'], FILTER_SANITIZE_URL );

	//validate the email (as much as we can)
	if( !empty( $_POST['email'] ) and !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ):
		$mod_message->add( 'InvalidEmail' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check login
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//now, update our data
	$mod_user->set_data( array(
		'name' => $_POST['username'],
		'email' => $_POST['email'],
		'daily_email' => ( isset( $_POST['daily_email'] ) and $_POST['daily_email'] == 'on' ) ? 1 : 0,
		'weekly_email' => ( isset( $_POST['weekly_email'] ) and $_POST['weekly_email'] == 'on' ) ? 1 : 0,
	) );

	//delete username cookie
	$mod_cookie->delete( 'ChangeUsernameMessage' );

	//redirect
	$mod_message->add( 'SettingsUpdated' );
	header( 'Location: ' . $redir );
?>