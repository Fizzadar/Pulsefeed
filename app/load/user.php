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

	//work out stream type
	$stream_type = 'hybrid';
	if( isset( $_GET['stream'] ) )
		$stream_type = $_GET['stream'];

	//public stream on user? no,no
	if( $stream_type == 'public' )
		die( header( 'Location: ' . $c_config['root'] . '/public' ) );

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

	//load users streams
	$streams = $mod_db->query( '
		SELECT id, name
		FROM mod_stream
		WHERE user_id = ' . $user_id . '
	' );
	$mod_template->add( 'streams', $streams );

	//set stream to cookie
	$mod_cookie->set( 'RecentStream', $_SERVER['REQUEST_URI'] );

	//stream_id as stream_type?
	$stream_id = 0;
	if( is_numeric( $stream_type ) ):
		//does this user own this stream?
		$own = $mod_db->query( '
			SELECT name
			FROM mod_stream
			WHERE id = ' . $stream_type . '
			AND user_id = ' . $user_id . '
			LIMIT 1
		' );
		if( !isset( $own ) or count( $own ) != 1 ):
			$mod_message->add( 'NotFound' );
			die( header( 'Location: ' . $c_config['root'] ) );
		endif;
		$stream_name = $own[0]['name'];
		$stream_id = $stream_type;
		$stream_type = 'user';
	endif;

	//load the users sources
	$sources = $mod_db->query( '
		SELECT id, site_title AS source_title, site_url AS source_url
		FROM mod_source, mod_user_sources' . ( $stream_type == 'user' ? ', mod_stream_sources' : '' ) . '
		WHERE mod_source.id = mod_user_sources.source_id
		AND mod_user_sources.user_id = ' . $user_id . '
		' . ( $stream_type == 'user' ?
			'AND mod_source.id = mod_stream_sources.source_id
			AND mod_stream_sources.stream_id = ' . $stream_id : ''
		) . '
	' );
	foreach( $sources as $k => $source ):
		$sources[$k]['source_domain'] = $mod_data->domain_url( $source['source_url'] );
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
	$mod_stream->set_streamid( $stream_id );
	$mod_stream->set_offset( ( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) ) ? $_GET['offset'] : 0 );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//add data
	$mod_template->add( 'stream', $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build() );
	$mod_template->add( 'title', $stream_type );
	$mod_template->add( 'pageTitle', $name . ' ' . ( isset( $stream_name ) ? $stream_name : ucfirst( $stream_type ) ) . ' Stream' );
	$mod_template->add( 'userid', $user_id );
	$mod_template->add( 'streamid', $stream_id );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>