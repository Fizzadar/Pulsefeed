<?php
	/*
		file: app/proess/user/unfollow.php
		desc: unfollow a user
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_memcache;

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

	//test
	$test = $mod_memcache->get( 'mod_user_follows', array( array(
		'user_id' => $mod_user->get_userid(),
		'following_id' => $_POST['user_id']
	) ) );

	//delete from user follows
	$insert = $mod_memcache->delete( 'mod_user_follows', array( array(
		'user_id' => $mod_user->get_userid(),
		'following_id' => $_POST['user_id']
	) ) );

	//affected?
	if( is_array( $test ) and count( $test ) == 1 ):
		$mod_db->query( '
			UPDATE core_user
			SET followers = followers - 1
			WHERE id = ' . $_POST['user_id'] . '
			LIMIT 1
		' );
	endif;

	//done!
	$mod_message->add( 'UserUnFollowed' );
	header( 'Location: ' . $redir );
?>