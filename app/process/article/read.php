<?php
	/*
		file: app/process/article/read.php
		desc: mark an article as read
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_memcache;

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

	//mark unread
	$mod_db->query( '
		UPDATE mod_user_articles
		SET unread = 0
		WHERE user_id = ' . $mod_user->get_userid() . '
		AND article_id = ' . $_POST['article_id'] . '
	' );
	$mod_memcache->set( 'mod_user_hides', array(
		array(
			'user_id' => $mod_user->get_userid(),
			'article_id' => $_POST['article_id']
		)
	) );

	//redirect
	$mod_message->add( 'ArticleRead' );
	header( 'Location: ' . $redir );
?>