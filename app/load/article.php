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

	//start template
	$mod_template = new mod_template();

	//load the article
	$article = $mod_db->query( '
		SELECT mod_article.*, mod_source.site_title, mod_source.site_url
		FROM mod_article, mod_source
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
	$article[0]['not_full'] = $article[0]['content'] == $article[0]['description'];
	$article[0]['site_domain'] = $mod_data->domain_url( $article[0]['site_url'] );
	//add to template
	$mod_template->add( 'article', $article[0] );
	$mod_template->add( 'pageTitle', $article[0]['title'] );
	
	//external site?
	$orig = false;
	if( ( isset( $_GET['original'] ) and $_GET['original'] == 1 ) or $article[0]['not_full'] ):
		$orig = true;
		$mod_template->add( 'external', array(
			'title' => $article[0]['title'],
			'id' => $article[0]['id']
		) );
	endif;

	//delete unread
	if( $mod_user->check_login() ):
		$mod_db->query( '
			DELETE FROM mod_user_unread
			WHERE user_id = ' . $mod_user->get_userid() . '
			AND article_id = ' . $article[0]['id'] . '
			LIMIT 1
		' );
	endif;

	//load header
	$mod_template->load( 'core/header' );

	//original
	if( $orig )
		$mod_template->load( 'article/frame' );
	else
		$mod_template->load( 'article/main' );

	//footer
	$mod_template->load( 'core/footer' );
?>