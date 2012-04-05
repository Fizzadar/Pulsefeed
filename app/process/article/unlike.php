<?php
	/*
		file: app/process/article/recommend.php
		desc: recommend an article
	*/

	//modules
	global $mod_db, $mod_user, $mod_session, $mod_message, $mod_app, $mod_memcache;

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
	if( !$mod_user->check_permission( 'Like' ) ):
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

	//delete our recommendation
	$mod_memcache->delete( 'mod_user_likes', array(
		array(
			'user_id' => $mod_user->get_userid(),
			'article_id' => $_POST['article_id']
		)
	) );

	//did we add a new record?
	if( $mod_db->affected_rows() == 1 ):
		$article = $mod_memcache->get( 'mod_article', array(
			array(
				'id' => $_POST['article_id']
			)
		) );
	
		$mod_memcache->set( 'mod_article', array(
			array(
				'id' => $_POST['article_id'],
				'likes' => $article[0]['likes'] - 1
			)
		) );

		//remove from mod_user_articles
		$mod_db->query( '
			DELETE FROM
			mod_user_articles
			WHERE source_id = ' . $mod_user->get_userid() . '
			AND article_id = ' . $_POST['article_id'] . '
			AND source_type = "like"
		' );
	endif;

	//& finally, redirect
	$mod_message->add( 'ArticleUnLiked' );
	header( 'Location: ' . $redir );
?>