<?php
	/*
		file: app/load/sources.php
		desc: browser sources/subscribe to them
	*/

	//modules
	global $mod_db, $mod_user;

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) )
		$offset = $_GET['offset'];

	//order
	$order = 'mod_source.subscribers';
	if( isset( $_GET['new'] ) )
		$order = 'mod_source.time';

	//start template
	$mod_template = new mod_template();

	//get popular sources
	$sources = $mod_db->query( '
		SELECT mod_source.id, mod_source.site_title, mod_source.site_url, mod_source.subscribers, mod_source.articles' . ( $mod_user->check_login() ? ', mod_user_sources.user_id AS subscribed' : '' ) . '
		FROM mod_source
		' . ( $mod_user->check_login() ?
			'LEFT JOIN mod_user_sources ON mod_source.id = mod_user_sources.source_id AND mod_user_sources.user_id = ' . $mod_user->get_userid() : ''
		) . '
		ORDER BY ' . $order . ' DESC
		LIMIT ' . ( $offset * 30 ) . ', 30
	' );
	//manage bits
	foreach( $sources as $key => $source ):
		$sources[$key]['site_url_trim'] = substr( $source['site_url'], 0, 20 ) . ( strlen( $source['site_url'] ) > 20 ? '...' : '' );
	endforeach;
	$mod_template->add( 'sources', $sources );

	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'sourceOrder', $order );
	
	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'source/browse' );
	$mod_template->load( 'core/footer' );
?>