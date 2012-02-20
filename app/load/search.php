<?php
	/*
		file: app/load/search.php
		desc: search for stuff (sources, articles, users & collections)
	*/

	//modules
	global $mod_db;

	//no query?
	if( !isset( $_GET['q'] ) or empty( $_GET['q'] ) ) die();

	//search sources
	$sources = $mod_db->query( '
		SELECT id, site_title, MATCH( site_title ) AGAINST( "' . $_GET['q'] . '" ) AS score
		FROM mod_source
		WHERE MATCH( site_title ) AGAINST( "' . $_GET['q'] . '" IN BOOLEAN MODE )
		ORDER BY score DESC
	' );

	//search users
	$users = $mod_db->query( '
		SELECT id, name, MATCH( name ) AGAINST( "' . $_GET['q'] . '" ) AS score
		FROM core_user
		WHERE MATCH( name ) AGAINST( "' . $_GET['q'] . '" IN BOOLEAN MODE )
		ORDER BY score DESC
	' );

	//search articles
	$articles = $mod_db->query( '
		SELECT id, title, MATCH( title ) AGAINST( "' . $_GET['q'] . '" ) AS score
		FROM mod_article
		WHERE MATCH( title ) AGAINST( "' . $_GET['q'] . '" IN BOOLEAN MODE )
		ORDER BY score DESC
	' );

	print_r( $users );
	print_r( $sources );
	print_r( $articles );
?>