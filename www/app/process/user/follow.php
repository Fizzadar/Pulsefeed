<?php
	/*
		file: app/proess/user/follow.php
		desc: follow a user
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/sources';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['user_id'] ) or !is_numeric( $_POST['user_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//permission
	if( !$mod_user->check_permission( 'Follow' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;
	
	//locate our source
	$user = $mod_db->query( '
		SELECT id
		FROM core_user
		WHERE id = ' . $_POST['user_id'] . '
		LIMIT 1
	' );
	if( !$user or count( $user ) != 1 ):
		$mod_message->add( 'NoSource' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert the follow
	$insert = $mod_db->query( '
		REPLACE INTO mod_user_follows
		( user_id, following_id )
		VALUES( ' . $mod_user->get_userid() . ', ' . $_POST['user_id'] . ' )
	' );

	//redirect
	if( $insert ):
		//message, redirect
		$mod_message->add( 'UserFollowed' );
		header( 'Location: ' . $redir );
	else:
		//still here?
		$mod_message->add( 'UnknownError' );
		header( 'Location: ' . $redir );
	endif;
?>