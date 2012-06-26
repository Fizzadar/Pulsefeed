<?php
	/*
		file: app/process/article/share.php
		desc: share an article
	*/

	//modules
	global $mod_db, $mod_user, $mod_session, $mod_message, $mod_app, $mod_memcache;

	//redirect dir
	$redir = $c_config['root'] . '/article/' . $_POST['article_id'] . '/share';

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
		//$mod_message->add( 'ArticleShared' );
		//die( header( 'Location: ' . $redir ) );
	endif;

	//have we shared before? stop here
	$shared = count( $mod_memcache->get( 'mod_user_shares', array( array(
		'user_id' => $mod_user->get_userid(),
		'article_id' => $_POST['article_id']
	) ) ) ) == 1;
	if( $shared ):
		$mod_message->add( 'ArticleShared' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert share
	$insert = $mod_memcache->set( 'mod_user_shares', array( array(
		'user_id' => $mod_user->get_userid(),
		'article_id' => $_POST['article_id'],
		'time' => time()
	) ) );

	if( $insert ):
		//update article
		$mod_memcache->set( 'mod_article', array( array(
			'id' => $_POST['article_id'],
			'shares' => $article[0]['shares'] + 1
		) ), false );

		//get users following us
		$users = $mod_db->query( '
			SELECT user_id AS id
			FROM mod_user_follows
			WHERE following_id = ' . $mod_user->get_userid()
		);
		$sql = '
			INSERT IGNORE INTO mod_user_articles
			( user_id, article_id, source_type, source_title, source_id, article_time ) VALUES';
		foreach( $users as $user ):
			//skip if they have hidden the article
			if( count( $mod_memcache->get( 'mod_user_hides', array(
				array(
					'user_id' => $user['id'],
					'article_id' => $_POST['article_id']
				)
			) ) ) == 1 )
				continue;

			$sql .= ' ( ' . $user['id'] . ', ' . $_POST['article_id'] . ', "share", "' . $mod_user->session_username() . '", ' . $mod_user->get_userid() . ', ' . $article[0]['time'] . ' ), ';
		endforeach;
		$sql = rtrim( $sql, ', ' );

		$mod_db->query( $sql );
	else:
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $redir ) );
	endif;


	//& finally, redirect
	$mod_message->add( 'ArticleShared' );
	header( 'Location: ' . $redir );
?>