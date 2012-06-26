<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config, $mod_memcache, $mod_data;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/websites';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['website_id'] ) or !is_numeric( $_POST['website_id'] ) or $_POST['website_id'] <= 0 ):
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
	
	//locate our website
	$website = $mod_memcache->get( 'mod_website', array( array(
		'id' => $_POST['website_id']
	) ) );
	if( !$website or count( $website ) != 1 ):
		$mod_message->add( 'NoWebsite' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//test subscription
	$subscribed = count( $mod_memcache->get( 'mod_user_websites', array( array(
		'website_id' => $_POST['website_id'],
		'user_id' => $mod_user->get_userid()
	) ) ) ) == 1;

	//already subscribed? end here
	if( $subscribed ):
		$mod_message->add( 'WebsiteSubscribed' );
		die( header( 'Location:' . $redir ) );
	endif;

	//insert the subscription
	$insert = $mod_memcache->set( 'mod_user_websites', array( array(
		'website_id' => $_POST['website_id'],
		'user_id' => $mod_user->get_userid(),
		'time' => time()
	) ) );

	//redirect
	if( $insert ):
		//add subscriber (if affected = 1)
		$mod_memcache->set( 'mod_website', array( array(
			'id' => $_POST['website_id'],
			'subscribers' => $website[0]['subscribers'] + 1
		) ), false );

		//add last 10 articles to mod_user_sources
		$articles = $mod_db->query( '
			SELECT article_id
			FROM mod_website_articles
			WHERE website_id = ' . $_POST['website_id'] . '
			ORDER BY article_time DESC
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
				( user_id, article_id, source_type, source_id, source_title, source_data, article_time ) VALUES';
			foreach( $articles as $article ):
				$sql .= '( ' . $mod_user->get_userid() . ', ' . $article['id'] . ', "website", ' . $_POST['website_id'] . ', "' . $website[0]['site_title'] . '", \'' . json_encode( array( 'domain' => $mod_data->domain_url( $website[0]['site_url'] ) ), true ) . '\', ' . $article['time'] . ' ),';
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
		$mod_message->add( 'WebsiteSubscribed' );
		header( 'Location: ' . $redir );
	endif;
?>