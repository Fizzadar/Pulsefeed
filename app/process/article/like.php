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
	if( !$mod_user->check_permission( 'Recommend' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check the article exists
	$article = $mod_db->query( '
		SELECT id, time, popularity_score
		FROM mod_article
		WHERE id = ' . $_POST['article_id'] . '
		LIMIT 1
	' );
	if( !$article or count( $article ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert our recommendation
	$mod_memcache->set( 'mod_user_likes', array(
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
				'likes' => $article[0]['likes'] + 1
			)
		) );

		//get users following us
		$users = $mod_db->query( '
			SELECT user_id AS id
			FROM mod_user_follows
			WHERE following_id = ' . $mod_user->get_userid()
		);
		$sql = '
			REPLACE INTO mod_user_articles
			( user_id, article_id, source_type, source_title, source_id, article_time, article_popscore ) VALUES';
		foreach( $users as $user ):
			$sql .= ' ( ' . $user['id'] . ', ' . $_POST['article_id'] . ', "like", "' . $mod_user->session_username() . '", ' . $mod_user->get_userid() . ', ' . $article[0]['time'] . ', ' . $article[0]['popularity_score'] . ' ), ';
		endforeach;
		$sql = rtrim( $sql, ', ' );

		$mod_db->query( $sql );
	endif;

	//& finally, redirect
	$mod_message->add( 'ArticleRecommended' );
	header( 'Location: ' . $redir );
?>