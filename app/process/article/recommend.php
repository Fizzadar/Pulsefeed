<?php
	/*
		file: app/process/article/recommend.php
		desc: recommend an article
	*/

	//modules
	global $mod_db, $mod_user, $mod_session, $mod_message, $mod_app;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/article/' . $_POST['article_id'];

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check post data
	if( !isset( $_POST['article_id'] ) or !is_numeric( $_POST['article_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//are we logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//permission
	if( !$mod_user->check_permission( 'Recommend' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check the article exists
	$article = $mod_db->query( '
		SELECT id
		FROM mod_article
		WHERE id = ' . $_POST['article_id'] . '
		LIMIT 1
	' );
	if( !$article or count( $article ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert our recommendation
	$insert = $mod_db->query( '
		REPLACE INTO
		mod_user_recommends
		( user_id, article_id, time )
		VALUES ( ' . $mod_user->get_userid() . ', ' . $_POST['article_id'] . ', ' . time() . ' )
	' );
	if( !$insert ):
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//did we add a new record?
	if( $mod_db->affected_rows() == 1 ):
		$mod_db->query( '
			UPDATE mod_article
			SET recommendations = recommendations + 1
			WHERE id = ' . $_POST['article_id'] . '
			LIMIT 1
		' );
	endif;

	//& finally, redirect
	$mod_message->add( 'ArticleRecommended' );
	header( 'Location: ' . $redir );
?>