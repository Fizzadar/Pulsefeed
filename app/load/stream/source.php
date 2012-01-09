<?php
	/*
		file: app/load/user.php
		desc: display & load user streams
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data;

	//start template
	$mod_template = new mod_template();

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) and $_GET['offset'] > 0 )
		$offset = $_GET['offset'];

	//load our source (and check)
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	$source = $mod_db->query( '
		SELECT mod_source.id, mod_source.site_title' . ( $mod_user->check_login() ? ', mod_user_sources.user_id AS subscribed' : '' ) . '
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

	if( $mod_user->check_login() ):
		//load users streams
		$streams = $mod_db->query( '
			SELECT id, name
			FROM mod_stream
			WHERE user_id = ' . $mod_user->get_userid() . '
		' );
		$mod_template->add( 'streams', $streams );
	endif;

	//set stream to cookie
	$mod_cookie->set( 'RecentStream', $_SERVER['REQUEST_URI'] );

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, 'source' ) : new mod_stream_site( $mod_db, 'source' );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set user & stream id 
	$mod_stream->set_offset( $offset * 64 );

	//set source id
	$mod_stream->set_sourceid( $_GET['id'] );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//add data
	$mod_template->add( 'stream', $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build() );
	$mod_template->add( 'title', 'source' );
	$mod_template->add( 'pageTitle', $source[0]['site_title'] . ' Stream' );
	$mod_template->add( 'userid', $mod_user->session_userid() );
	$mod_template->add( 'streamid', 0 );
	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'subscribed', isset( $source[0]['subscribed'] ) and $source[0]['subscribed'] != NULL );
	$mod_template->add( 'source_id', $_GET['id'] );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>