<?php
	/*
		file: app/load/user/invite.php
		desc: invite code form
	*/

	//modules
	global $mod_user, $mod_message;

	//already in alpha?
	if( $mod_user->check_permission( 'Subscribe' ) ):
		$mod_message->add( 'AlreadyInvited' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template();

	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'user/invite' );
	$mod_template->load( 'core/footer' );
?>