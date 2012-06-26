<?php
	/*
		file: app/load/stream/source.php
		desc: load source stream
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data, $mod_load, $mod_memcache, $user_id, $mod_template, $mod_app, $mod_streamcache;

	//start template
	$mod_template = new mod_template();

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) and $_GET['offset'] > 0 )
		$offset = $_GET['offset'];

	//id?
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) or $_GET['id'] <= 0 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get source & check
	$website = $mod_memcache->get( 'mod_website', array(
		array(
			'id' => $_GET['id']
		)
	) );
	if( !isset( $website ) or count( $website ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//subscribed
	$subscribed = false;
	if( $mod_user->check_login() ):
		$subscribed = count( $mod_memcache->get( 'mod_user_websites', array(
			array(
				'user_id' => $mod_user->get_userid(),
				'website_id' => $_GET['id']
			)
		) ) ) == 1 ? true : false;
	endif;

	//api?
	if( !$mod_config['api'] ):
		$user_id = $mod_user->get_userid();
		$mod_app->load( 'load/stream/userconf' );
	endif;

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, 'website', $mod_streamcache ) : new mod_stream_site( $mod_db, 'website', $mod_streamcache );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set offset
	$mod_stream->set_offset( $offset );

	//set source id
	$mod_stream->set_websiteid( $_GET['id'] );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get the data
	$stream_data = $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build();

	//add data
	$mod_template->add( 'stream', $stream_data['items'] );
	$mod_template->add( 'title', 'website' );
	$mod_template->add( 'pageTitle', $website[0]['site_title'] . ' Stream' );
	$mod_template->add( 'userid', $mod_user->session_userid() );
	$mod_template->add( 'streamid', 0 );
	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'subscribed', $subscribed );
	$mod_template->add( 'website_id', $_GET['id'] );
	$mod_template->add( 'website', $website[0] );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>