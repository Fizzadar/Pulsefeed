<?php
	/*
		file: app/proess/user/unfollow.php
		desc: unfollow a user
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message;

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

	//delete from user follows
	$mod_db->query( '
		DELETE FROM mod_user_follows
		WHERE following_id = ' . $_POST['user_id'] . '
		AND user_id = ' . $mod_user->get_userid() . '
		LIMIT 1
	' );

	//done!
	$mod_message->add( 'UserUnFollowed' );
	header( 'Location: ' . $redir );
?>