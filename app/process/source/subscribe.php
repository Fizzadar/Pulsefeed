<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/sources';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['source_id'] ) or !is_numeric( $_POST['source_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//permission
	if( !$mod_user->check_permission( 'Subscribe' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;
	
	//locate our source
	$source = $mod_db->query( '
		SELECT id
		FROM mod_source
		WHERE id = ' . $_POST['source_id'] . '
		LIMIT 1
	' );
	if( !$source or count( $source ) != 1 ):
		$mod_message->add( 'NoSource' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert the subscription
	$insert = $mod_db->query( '
		REPLACE INTO mod_user_sources
		( user_id, source_id )
		VALUES( ' . $mod_user->get_userid() . ', ' . $_POST['source_id'] . ' )
	' );

	//get the articles from this source in the last x hours
	$articles = $mod_db->query( '
		SELECT id
		FROM mod_article
		WHERE source_id = ' . $_POST['source_id'] . '
		AND time > ' . ( time() - 3600 * $mod_config['article_expire'] ) . '
	' );
	if( $articles and count( $articles ) > 0 ):
		//insert back into unread
		$sql = '
			INSERT INTO mod_user_unread
			( article_id, user_id )
			VALUES';
		foreach( $articles as $article ):
			$sql .= '( ' . $article['id'] . ', ' . $mod_user->get_userid() . ' ),';
		endforeach;
		$sql = rtrim( $sql, ',' );
		//run it
		$mod_db->query( $sql );
		die( mysql_error() );
	endif;

	//redirect
	if( $insert ):
		//add subscriber (if affected = 1)
		if( $mod_db->affected_rows() == 1 ):
			$mod_db->query( '
				UPDATE mod_source
				SET subscribers = subscribers + 1
				WHERE id = ' . $_POST['source_id'] . '
				LIMIT 1
			' );
		endif;

		//message, redirect
		$mod_message->add( 'SourceSubscribed' );
		header( 'Location: ' . $redir );
	else:
		$mod_message->add( 'UnknownError' );
		header( 'Location: ' . $redir );
	endif;
?>