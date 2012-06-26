<?php
	/*
		file: app/proess/user/follow.php
		desc: follow a user
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config, $mod_memcache;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/users';

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
	
	//locate our user
	$user = $mod_memcache->get( 'core_user', array( array(
		'id' => $_POST['user_id']
	) ) );
	if( !$user or count( $user ) != 1 ):
		$mod_message->add( 'NoSource' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//test
	$test = $mod_memcache->get( 'mod_user_follows', array( array(
		'user_id' => $mod_user->get_userid(),
		'following_id' => $_POST['user_id']
	) ) );

	//insert the follow
	$insert = $mod_memcache->set( 'mod_user_follows', array( array(
		'user_id' => $mod_user->get_userid(),
		'following_id' => $_POST['user_id']
	) ) );

	//redirect
	if( $insert ):
		if( is_array( $test ) and count( $test ) == 0 ):
			//increase follower count
			$mod_db->query( '
				UPDATE core_user
				SET followers = followers + 1
				WHERE id = ' . $_POST['user_id'] . '
				LIMIT 1
			' );
		endif;

		//message, redirect
		$mod_message->add( 'UserFollowed' );
		header( 'Location: ' . $redir );
	else:
		//still here?
		$mod_message->add( 'UnknownError' );
		header( 'Location: ' . $redir );
	endif;
?>