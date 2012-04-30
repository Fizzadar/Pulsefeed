<?php
	/*
		file: app/process/user/account/sync.php
		desc: turns on/off syncing for a users oauth
	*/

	//modules
	global $mod_db, $mod_message, $mod_session, $mod_user;

	//redir
	$redir = $c_config['root'] . '/settings/accounts';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['o_id'] ) or !is_numeric( $_POST['o_id'] ) or !isset( $_POST['provider'] ) or !isset( $_POST['sync'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//enable the source
	$update = $mod_db->query( '
		UPDATE mod_account
		SET disabled = ' . ( $_POST['sync'] ? 0 : 1 ) . '
		WHERE user_id = ' . $mod_user->get_userid() . '
		AND type = "' . $_POST['provider'] . '"
		AND o_id = ' . $_POST['o_id'] . '
		LIMIT 1
	' );

	//redirect
	if( $update ):
		$mod_message->add( 'AccountSync' . ( $_POST['sync'] ? 'On' : 'Off' ) );
		header( 'Location: ' . $redir );
	else:
		$mod_message->add( 'UnknownError' );
		header( 'Location: ' . $redir );
	endif;
?>