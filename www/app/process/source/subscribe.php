<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config, $mod_memcache, $mod_data;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/sources';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['source_id'] ) or !is_numeric( $_POST['source_id'] ) or $_POST['source_id'] <= 0 ):
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
		SELECT id, site_url, site_title
		FROM mod_source
		WHERE id = ' . $_POST['source_id'] . '
		LIMIT 1
	' );
	if( !$source or count( $source ) != 1 ):
		$mod_message->add( 'NoSource' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert the subscription
	$insert = $mod_memcache->set( 'mod_user_sources', array(
		array(
			'source_id' => $_POST['source_id'],
			'user_id' => $mod_user->get_userid(),
			'time' => time()
		)
	) );
	$insert_rows = $mod_db->affected_rows();

	//redirect
	if( $insert ):
		//add subscriber (if affected = 1)
		if( $insert_rows == 1 ):
			$mod_db->query( '
				UPDATE mod_source
				SET subscribers = subscribers + 1
				WHERE id = ' . $_POST['source_id'] . '
				LIMIT 1
			' );

			//add last 10 articles to mod_user_sources
			$articles = $mod_db->query( '
				SELECT article_id
				FROM mod_source_articles
				WHERE source_id = ' . $_POST['source_id'] . '
				ORDER BY article_time DESC
				LIMIT 10
			' );
			//now get the articles
			$list = array();
			foreach( $articles as $article )
				$list[] = array(
					'id' => $article['article_id']
				);
			$articles = $mod_memcache->get( 'mod_article', $list );
			//and build insert
			$sql = '
				INSERT IGNORE INTO mod_user_articles
				( user_id, article_id, source_type, source_id, source_title, source_data, article_time ) VALUES';
			foreach( $articles as $article ):
				$sql .= '( ' . $mod_user->get_userid() . ', ' . $article['id'] . ', "source", ' . $_POST['source_id'] . ', "' . $source[0]['site_title'] . '", \'' . json_encode( array( 'domain' => $mod_data->domain_url( $source[0]['site_url'] ) ), true ) . '\', ' . $article['time'] . ' ),';
			endforeach;
			$sql = rtrim( $sql, ',' );
			$mod_db->query( $sql );
		endif;

		//message, redirect
		if( !isset( $_POST['noredirect'] ) ):
			$mod_message->add( 'SourceSubscribed' );
			header( 'Location: ' . $redir );
		endif;
	else:
		//still here?
		if( !isset( $_POST['noredirect'] ) ):
			$mod_message->add( 'UnknownError' );
			header( 'Location: ' . $redir );
		endif;
	endif;
?>