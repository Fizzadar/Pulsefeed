<?php
	/*
		file: app/load/user.php
		desc: display & load user streams
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data, $mod_load, $mod_memcache;

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

	//get tag and check
	$topic = $mod_memcache->get( 'mod_topic', array(
		array(
			'id' => $_GET['id']
		)
	) );
	if( !isset( $topic ) or count( $topic ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//api?
	if( !$mod_config['api'] ):
		//set stream to cookie
		$mod_cookie->set( 'RecentStream', $_SERVER['REQUEST_URI'] );
	endif;

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, 'topic' ) : new mod_stream_site( $mod_db, 'topic' );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set offset
	$mod_stream->set_offset( $offset );

	//set topic id
	$mod_stream->set_topicid( $_GET['id'] );
	
	//api & logged in?
	if( !$mod_config['api'] and $mod_user->check_login() ):
		//load the users sources
		$sources = $mod_load->load_sources( $mod_user->get_userid() );
		$mod_template->add( 'sources', $sources );

		//accounts
		$accounts = $mod_load->load_accounts( $mod_user->get_userid() );
		$mod_template->add( 'accounts', $accounts );
		
		//load the users followings
		$followings = $mod_load->load_users( $mod_user->get_userid() );
		$mod_template->add( 'followings', $followings );

		//load users collections
		$collections = $mod_load->load_collections( $mod_user->get_userid() );
		$mod_template->add( 'collections', $collections );
	endif;

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get the data
	$stream_data = $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build();

	//add data
	$mod_template->add( 'stream', $stream_data['items'] );
	$mod_template->add( 'title', 'topic' );
	$mod_template->add( 'pageTitle', $topic[0]['title'] . ' Stream' );
	$mod_template->add( 'userid', $mod_user->session_userid() );
	$mod_template->add( 'username', $mod_user->session_username() );
	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'topic', $topic[0] );
	$mod_template->add( 'streamid', 0 );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>