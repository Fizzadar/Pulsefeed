<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config, $mod_memcache, $mod_data;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/topics';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['topic_id'] ) or !is_numeric( $_POST['topic_id'] ) or $_POST['topic_id'] <= 0 ):
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
	
	//locate our topic
	$topic = $mod_memcache->get( 'mod_topic', array( array(
		'id' => $_POST['topic_id']
	) ) );
	if( !$topic or count( $topic ) != 1 ):
		$mod_message->add( 'NoTopic' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//test subscription
	$subscribed = count( $mod_memcache->get( 'mod_user_topics', array( array(
		'topic_id' => $_POST['topic_id'],
		'user_id' => $mod_user->get_userid()
	) ) ) ) == 1;
	if( $subscribed ):
		$mod_message->add( 'TopicSubscribed' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert the subscription
	$insert = $mod_memcache->set( 'mod_user_topics', array( array(
		'topic_id' => $_POST['topic_id'],
		'user_id' => $mod_user->get_userid(),
		'time' => time()
	) ) );

	//redirect
	if( $insert ):
		$mod_memcache->set( 'mod_topic', array( array(
			'id' => $_POST['topic_id'],
			'subscribers' => $topic[0]['subscribers'] + 1
		) ), false );

		//add last 10 articles to mod_user_sources
		$articles = $mod_db->query( '
			SELECT article_id, source_id, source_title, source_data
			FROM mod_topic_articles
			WHERE topic_id = ' . $_POST['topic_id'] . '
			ORDER BY popscore DESC
			LIMIT 10
		' );
		if( is_array( $articles ) and count( $articles ) > 0 ):
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
				( user_id, article_id, source_type, source_id, source_title, source_data, article_time, origin_id, origin_title, origin_data ) VALUES';
			foreach( $articles as $article ):
				$sql .= '(
					' . $mod_user->get_userid() . ',
					' . $article['id'] . ',
					"topic",
					' . $_POST['topic_id'] . ',
					"' . $topic[0]['title'] . '",
					"{}", ' . $article['time'] . ',
					' . $article['source_id'] . ',
					"' . $article['source_title'] . '",
					\'' . $article['source_data'] . '\'
				),';
			endforeach;
			$sql = rtrim( $sql, ',' );
			$mod_db->query( $sql );
		endif;
	else:
		//still here?
		if( !isset( $_POST['noredirect'] ) ):
			$mod_message->add( 'UnknownError' );
			header( 'Location: ' . $redir );
		endif;
	endif;

	//message, redirect
	if( !isset( $_POST['noredirect'] ) ):
		$mod_message->add( 'TopicSubscribed' );
		header( 'Location: ' . $redir );
	endif;
?>