<?php
	//modules
	global $mod_db, $mod_user, $mod_message;

	//no id?
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) ):
		$mod_message->add( 'InvalidGet' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template();

	//load the article
	$article = $mod_db->query( '
		SELECT mod_articles.*, mod_sources.site_title, mod_sources.site_url
		FROM mod_articles, mod_sources
		WHERE mod_sources.id = mod_articles.source_id
		AND mod_articles.id = ' . $_GET['id'] . '
		LIMIT 1
	' );
	if( !$article or count( $article ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	$article[0]['trim_url'] = substr( $article[0]['url'], 0, 30 ) . ( strlen( $article[0]['url'] ) > 30 ? '...' : '' );
	$article[0]['not_full'] = $article[0]['content'] == $article[0]['description'];
	$url_bits = parse_url( $article[0]['site_url'] );
	$article[0]['site_domain'] = $url_bits['host'];
	$mod_template->add( 'article', $article[0] );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'article' );
	$mod_template->load( 'core/footer' );
?>