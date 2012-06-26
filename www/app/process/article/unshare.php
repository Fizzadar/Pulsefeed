<?php
	/*
		file: app/process/article/unshare.php
		desc: unshare an article
	*/

	//modules
	global $mod_db, $mod_user, $mod_session, $mod_message, $mod_app, $mod_memcache;

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//check post data
	if( !isset( $_POST['article_id'] ) or !is_numeric( $_POST['article_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	
	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/article/' . $_POST['article_id'];

	//are we logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//permission
	if( !$mod_user->check_permission( 'Share' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check the article exists
	$article = $mod_memcache->get( 'mod_article', array( array(
		'id' => $_POST['article_id']
	) ) );
	if( !$article or count( $article ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//expired?
	if( $article[0]['expired'] ):
		$mod_message->add( 'ArticleUnshared' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//already not shared?
	$unshared = count( $mod_memcache->get( 'mod_user_shares', array( array(
		'user_id' => $mod_user->get_userid(),
		'article_id' => $_POST['article_id']
	) ) ) ) == 0;
	if( $unshared ):
		$mod_message->add( 'ArticleUnshared' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//delete share
	$delete = $mod_memcache->delete( 'mod_user_shares', array( array(
		'user_id' => $mod_user->get_userid(),
		'article_id' => $_POST['article_id']
	) ) );

	if( $delete ):
		//update article
		$mod_memcache->set( 'mod_article', array( array(
			'id' => $_POST['article_id'],
			'shares' => $article[0]['shares'] - 1
		) ), false );

		//remove from mod_user_articles
		$mod_db->query( '
			DELETE FROM
			mod_user_articles
			WHERE source_id = ' . $mod_user->get_userid() . '
			AND article_id = ' . $_POST['article_id'] . '
			AND source_type = "share"
		' );
	else:
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//& finally, redirect
	$mod_message->add( 'ArticleUnshared' );
	header( 'Location: ' . $redir );
?>