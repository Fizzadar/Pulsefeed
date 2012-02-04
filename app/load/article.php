<?php
	/*
		file: app/load/article.php
		desc: load individual article
	*/
	
	//modules
	global $mod_db, $mod_user, $mod_message, $mod_data, $mod_cookie;

	//no id?
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) ):
		$mod_message->add( 'InvalidGet' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	$logged_userid = $mod_user->get_userid();
	
	//start template
	$mod_template = new mod_template();

	//load the article
	$article = $mod_db->query( '
		SELECT mod_article.*, mod_source.site_title, mod_source.site_url, mod_source.id AS site_id
		' . ( $logged_userid ? ', mod_user_recommends.article_id AS recommended' : '' ) . '
		FROM mod_article
		' . ( $logged_userid ? 'LEFT JOIN mod_user_recommends ON mod_article.id = mod_user_recommends.article_id AND mod_user_recommends.user_id = ' . $logged_userid : '' ) . '
		, mod_source
		WHERE mod_source.id = mod_article.source_id
		AND mod_article.id = ' . $_GET['id'] . '
		LIMIT 1
	' );
	if( !$article or count( $article ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	//setup bits
	$url = empty( $article[0]['end_url'] ) ? $article[0]['url'] : $article[0]['end_url'];
	$article[0]['trim_url'] = substr( $url, 0, 30 ) . ( strlen( $url ) > 30 ? '...' : '' );
	$article[0]['site_domain'] = $mod_data->domain_url( $article[0]['site_url'] );
	
	//add to template
	$mod_template->add( 'article', $article[0] );
	$mod_template->add( 'pageTitle', $article[0]['title'] );
	
	//facebook recipie?
	if( isset( $_GET['recipe'] ) and $_GET['recipe'] == 1 )
		die( $mod_template->load( 'article/recipe' ) );

	//delete unread
	$unread = false;
	if( $mod_user->check_login() ):
		$mod_db->query( '
			DELETE FROM mod_user_unread
			WHERE user_id = ' . $mod_user->get_userid() . '
			AND article_id = ' . $article[0]['id'] . '
			LIMIT 1
		' );
		if( $mod_db->affected_rows() > 0 )
			$unread = true;
	endif;
	$mod_template->add( 'unread', $unread );
	
	//load header
	$mod_template->add( 'externalHeader', true );
	$mod_template->load( 'core/header' );

	//article template
	$mod_template->load( 'article/main' );
?>