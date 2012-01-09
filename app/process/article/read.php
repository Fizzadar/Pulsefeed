<?php
	/*
		file: app/process/article/read.php
		desc: mark an article as read
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'];

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['article_id'] ) or !is_numeric( $_POST['article_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//delete our read
	$mod_db->query( '
		DELETE FROM mod_user_unread
		WHERE article_id = ' . $_POST['article_id'] . '
		AND user_id = ' . $mod_user->get_userid() . '
		LIMIT 1
	' );

	//redirect
	$mod_message->add( 'ArticleRead' );
	header( 'Location: ' . $redir );
?>