<?php
	/*
		file: app/load/stream/source.php
		desc: load source stream
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data, $mod_load;

	//start template
	$mod_template = new mod_template();

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) and $_GET['offset'] > 0 )
		$offset = $_GET['offset'];

	//load our source (and check)
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) or $_GET['id'] <= 0 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	$source = $mod_db->query( '
		SELECT mod_source.id, mod_source.site_title, mod_source.site_url' . ( $mod_user->check_login() ? ', mod_user_sources.user_id AS subscribed' : '' ) . '
		FROM mod_source
		' . ( $mod_user->check_login() ?
			'LEFT JOIN mod_user_sources ON mod_source.id = mod_user_sources.source_id AND mod_user_sources.user_id = ' . $mod_user->get_userid() : ''
		) . '
		WHERE mod_source.id = ' . $_GET['id'] . '
		LIMIT 1
	' );
	if( !isset( $source ) or count( $source ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//api?
	if( !$mod_config['api'] ):
		//set stream to cookie
		$mod_cookie->set( 'RecentStream', $_SERVER['REQUEST_URI'] );
	endif;
	
	//api & logged in?
	if( !$mod_config['api'] and $mod_user->check_login() ):
		//load the users sources
		$sources = $mod_load->load_sources( $mod_user->get_userid() );
		$mod_template->add( 'sources', $sources );

		//load the users followings
		$followings = $mod_load->load_users( $mod_user->get_userid() );
		$mod_template->add( 'followings', $followings );
	endif;

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, 'source' ) : new mod_stream_site( $mod_db, 'source' );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set offset
	$mod_stream->set_offset( $offset * 64 );

	//set source id
	$mod_stream->set_sourceid( $_GET['id'] );

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
	$mod_template->add( 'title', 'source' );
	$mod_template->add( 'pageTitle', $source[0]['site_title'] . ' Stream' );
	$mod_template->add( 'userid', $mod_user->session_userid() );
	$mod_template->add( 'username', $mod_user->session_username() );
	$mod_template->add( 'streamid', 0 );
	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'subscribed', isset( $source[0]['subscribed'] ) and $source[0]['subscribed'] != NULL );
	$mod_template->add( 'source_id', $_GET['id'] );
	$mod_template->add( 'source', $source[0] );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>