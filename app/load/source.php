<?php
	/*
		file: app/load/user.php
		desc: display & load user streams
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data;

	//start template
	$mod_template = new mod_template();

	//load our source (and check)
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	$source = $mod_db->query( '
		SELECT site_title
		FROM mod_source
		WHERE id = ' . $_GET['id'] . '
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
	$mod_stream->set_offset( ( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) ) ? $_GET['offset'] : 0 );

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

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>