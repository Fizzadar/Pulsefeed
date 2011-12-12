<?php
	/*
		file: app/load/user.php
		desc: display & load user streams
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config;

	//work out userid
	$user_id = 0;
	if( isset( $_GET['id'] ) and is_numeric( $_GET['id'] ) )
		$user_id = $_GET['id'];
	else
		$user_id = $mod_user->get_userid();

	//check the user exists
	$exists = $mod_db->query( '
		SELECT id
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

	//load the users sources
	$sources = $mod_db->query( '
		SELECT id, site_title AS source_title, site_url AS source_url
		FROM mod_source, mod_user_sources
		WHERE mod_source.id = mod_user_sources.source_id
		AND mod_user_sources.user_id = ' . $user_id . '
	' );
	foreach( $sources as $k => $source ):
		$url = parse_url( $source['source_url'] );
		$sources[$k]['source_domain'] = str_replace( 'www.', '', $url['host'] );
		$sources[$k]['source_title'] = substr( $sources[$k]['source_title'], 0, 13 ) . ( strlen( $sources[$k]['source_title'] ) > 13 ? '...' : '' );
	endforeach;
	$mod_template->add( 'sources', $sources );

	//work out stream type
	$stream_type = 'hybrid';
	if( isset( $_GET['stream'] ) )
		$stream_type = $_GET['stream'];

	//set stream to cookie
	$mod_cookie->set( 'RecentStream', $_SERVER['REQUEST_URI'] );

	//stream_id as stream_type?
	$stream_id = 0;
	if( is_numeric( $stream_type ) ):
		$stream_id = $stream_type;
		$stream_type = 'user';
	endif;

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, $stream_type ) : new mod_stream_site( $mod_db, $stream_type );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set user & stream id 
	$mod_stream->set_userid( $user_id );
	$mod_stream->set_streamid( $stream_id );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//add data
	$mod_template->add( 'stream', $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build() );
	$mod_template->add( 'title', $stream_type );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>