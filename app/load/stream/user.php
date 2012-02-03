<?php
	/*
		file: app/load/user.php
		desc: display & load user streams
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data;

	//work out userid
	$user_id = 0;
	if( isset( $_GET['id'] ) and is_numeric( $_GET['id'] ) )
		$user_id = $_GET['id'];
	else
		$user_id = $mod_user->get_userid();

	//since id
	$since_id = 0;
	if( isset( $_GET['since'] ) and is_numeric( $_GET['since'] ) and $_GET['since'] > 0 )
		$since_id = $_GET['since'];

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) and $_GET['offset'] > 0 )
		$offset = $_GET['offset'];

	//work out stream type
	$stream_type = 'hybrid';
	if( isset( $_GET['stream'] ) )
		$stream_type = $_GET['stream'];

	//public stream on user? no,no
	if( $stream_type == 'public' ):
		header( 'HTTP/1.1 301 Moved Permanently' );
		die( header( 'Location: ' . $c_config['root'] . '/public' ) );
	endif;

	//check the user exists
	$exists = $mod_db->query( '
		SELECT name
		FROM core_user
		WHERE id = ' . $user_id . '
		LIMIT 1
	' );
	if( !$exists or count( $exists ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	
	//start template
	$mod_template = new mod_template();

	//set our username
	if( $user_id == $mod_user->get_userid() and $mod_user->session_login() )
		$name = 'Your';
	else
		$name = $exists[0]['name'] . '\'s';

	//set stream to cookie
	$mod_cookie->set( 'RecentStream', $_SERVER['REQUEST_URI'] );

	//load the users sources
	$sources = $mod_db->query( '
		SELECT id, site_title AS source_title, site_url AS source_url
		FROM mod_source, mod_user_sources
		WHERE mod_source.id = mod_user_sources.source_id
		AND mod_user_sources.user_id = ' . $user_id . '
	' );
	foreach( $sources as $k => $s ):
		$sources[$k]['source_domain'] = $mod_data->domain_url( $s['source_url'] );
		$sources[$k]['source_title'] = substr( $sources[$k]['source_title'], 0, 13 ) . ( strlen( $sources[$k]['source_title'] ) > 13 ? '...' : '' );
	endforeach;
	$mod_template->add( 'sources', $sources );

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, $stream_type ) : new mod_stream_site( $mod_db, $stream_type );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set user & stream id 
	$mod_stream->set_userid( $user_id );
	$mod_stream->set_offset( $offset * 64 );
	$mod_stream->set_sinceid( $since_id );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get the data
	$stream_data = $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build();

	//add data
	$mod_template->add( 'stream', $stream_data['items'] );
	$mod_template->add( 'recommends', $stream_data['recommends'] );
	$mod_template->add( 'title', $stream_type );
	$mod_template->add( 'pageTitle', $name . ' ' . ( isset( $stream_name ) ? $stream_name : ucfirst( $stream_type ) ) . ' Stream' );
	$mod_template->add( 'userid', $user_id );
	$mod_template->add( 'username', $exists[0]['name'] );
	$mod_template->add( 'nextOffset', $offset + 1 );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>