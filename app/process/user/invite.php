<?php
	/*
		file: app/process/user/invite.php
		desc: enter invite code
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config;

	//redirect location
	$redir = $c_config['root'] . '/invite';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['invite_code'] ) or empty( $_POST['invite_code'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//already in?
	if( $mod_user->check_permission( 'Subscribe' ) ):
		$mod_message->add( 'AlreadyInvited' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check our invite code
	$code = $mod_db->query( '
		DELETE FROM mod_invites
		WHERE invite_code = "' . $_POST['invite_code'] . '"
		LIMIT 1
	' );
	if( $mod_db->affected_rows() != 1 ):
		$mod_message->add( 'InvalidInviteCode' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//ok go, lets do this!
	$mod_user->set_data( array(
		'group' => 3
	) );

	//relogin
	$mod_user->relogin();

	//redirect to home
	$mod_message->add( 'InviteCodeAdded' );
	header( 'Location: ' . $c_config['root'] );
?>