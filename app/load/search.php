<?php
	/*
		file: app/load/search.php
		desc: search for stuff (sources, articles, users & collections)
	*/

	//modules
	global $mod_db, $mod_message;

	//no query?
	if( !isset( $_GET['q'] ) or empty( $_GET['q'] ) ):
		$mod_message->add( 'InvalidGet' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) )
		$offset = $_GET['offset'] * 10;

	//* queries
	$boolq = '*' . $_GET['q'] . '*';

	//search sources
	$sources = $mod_db->query( '
		SELECT id, site_title AS title, MATCH( site_title ) AGAINST( "' . $_GET['q'] . '" ) AS score, "source" AS type
		FROM mod_source
		WHERE id > 0
		AND type = "source"
		AND MATCH( site_title ) AGAINST( "' . $boolq . '" IN BOOLEAN MODE )
		ORDER BY score DESC
		LIMIT ' . $offset . ', 10
	', true, 1440 );

	//search users
	$users = $mod_db->query( '
		SELECT id, name AS title, MATCH( name ) AGAINST( "' . $_GET['q'] . '" ) AS score, "user" AS type
		FROM core_user
		WHERE MATCH( name ) AGAINST( "' . $boolq . '" IN BOOLEAN MODE )
		ORDER BY score DESC
		LIMIT ' . $offset . ', 10
	', true, 1440 );

	//search articles
	$articles = $mod_db->query( '
		SELECT id, title, MATCH( title ) AGAINST( "' . $_GET['q'] . '" ) AS score, "article" AS type
		FROM mod_article
		WHERE MATCH( title ) AGAINST( "' . $boolq . '" IN BOOLEAN MODE )
		ORDER BY score DESC
		LIMIT ' . $offset . ', 10
	', true, 1440 );

	//quick sort func
	function scoreSort( $a, $b ) {
		if( $a['score'] == $b['score'] )
			return 1;

		return $a['score'] < $b['score'];
	}

	//sort arrays
	usort( $sources, 'scoreSort' );
	usort( $users, 'scoreSort' );
	usort( $articles, 'scoreSort' );

	//combine arrays in order (sources, users, articles)
	$results = array();
	foreach( $sources as $source )
		$results[] = $source;
	foreach( $users as $user )
		$results[] = $user;
	foreach( $articles as $article )
		$results[] = $article;

	//start template
	$mod_template = new mod_template();

	//add results to template
	$mod_template->add( 'results', $results );
	//title
	$mod_template->add( 'pageTitle', 'Search results: ' . $_GET['q'] );
	//next offset
	$mod_template->add( 'nextOffset', ( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) ) ? $_GET['offset'] + 1 : 1 );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'search' );
	$mod_template->load( 'core/footer' );
?>