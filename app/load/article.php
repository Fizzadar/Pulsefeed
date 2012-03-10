<?php
	/*
		file: app/load/article.php
		desc: load individual article
	*/
	
	//modules
	global $mod_db, $mod_user, $mod_message, $mod_data, $mod_cookie, $mod_memcache;

	//no id?
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) ):
		$mod_message->add( 'InvalidGet' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	$logged_userid = $mod_user->get_userid();
	
	//start template
	$mod_template = new mod_template();

	//load the article
	$article = $mod_memcache->get( 'mod_article', array(
		array( 'id' => $_GET['id']
		)
	) );
	if( !$article or count( $article ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	//setup bits
	$url = empty( $article[0]['end_url'] ) ? $article[0]['url'] : $article[0]['end_url'];

	//recommended?
	$article[0]['liked'] = false;
	if( $mod_user->check_login() )
		if( count( $mod_memcache->get( 'mod_user_likes', array(
			array(
				'user_id' => $mod_user->get_userid(),
				'article_id' => $_GET['id']
			)
		) ) ) == 1 )
			$article[0]['liked'] = true;
	
	//add to template
	$mod_template->add( 'article', $article[0] );
	$mod_template->add( 'pageTitle', $article[0]['title'] );
	
	//facebook recipie?
	if( isset( $_GET['recipe'] ) and $_GET['recipe'] == 1 )
		die( $mod_template->load( 'article/recipe' ) );

	//delete unread
	if( $mod_user->check_login() ):
		$mod_db->query( '
			UPDATE mod_user_articles
			SET unread = 0
			WHERE user_id = ' . $mod_user->get_userid() . '
			AND article_id = ' . $article[0]['id'] . '
		' );
		$mod_memcache->set( 'mod_user_reads', array(
			array(
				'user_id' => $mod_user->get_userid(),
				'article_id' => $article[0]['id']
			)
		) );
	endif;
	
	//load header
	$mod_template->add( 'externalHeader', true );
	$mod_template->load( 'core/header' );

	//article template
	$mod_template->load( 'article/main' );
?>