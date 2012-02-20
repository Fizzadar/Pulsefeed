<?php
	/*
		file: app/load/user/invite.php
		desc: invite code form
	*/

	//modules
	global $mod_db, $mod_user, $mod_message;

	//logged in?
	if( !$mod_user->session_login() ):
		$mod_message->add( 'NeedToLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//already in alpha?
	if( $mod_user->session_permission( 'Subscribe' ) ):
		$mod_message->add( 'AlreadyInvited' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template();

	//load current invite codes if logged in
	if( $mod_user->check_login() ):
		$invites = $mod_db->query( '
			SELECT invite_code
			FROM mod_invites
			WHERE user_id = 0
		' );
		$mod_template->add( 'InviteCodes', $invites );
	endif;

	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'user/invite' );
	$mod_template->load( 'core/footer' );
?>