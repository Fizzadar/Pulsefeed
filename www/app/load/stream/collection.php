<?php
	/*
		file: app/load/stream/collection.php
		desc: load a collection stream
	*/

//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data, $mod_load, $mod_memcache;

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) and $_GET['offset'] > 0 )
		$offset = $_GET['offset'];

	//collection id
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template();

	//check if collection exists
	$collection = $mod_memcache->get( 'mod_collection', array(
		array(
			'id' => $_GET['id']
		)
	) );
	if( !$collection or count( $collection ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	$collection = $collection[0];

	//our collection?
	$mod_template->add( 'owner', $collection['user_id'] == $mod_user->session_userid() );

	//not our collection?
	if( $collection['user_id'] != $mod_user->session_userid() ):
		$uname = $mod_memcache->get( 'core_user', array(
			array(
				'id' => $collection['user_id']
			)
		) );
		//user ok?
		if( count( $uname ) == 1 ):
			$mod_template->add( 'username', $uname[0]['name'] . '\'s' );
			$mod_template->add( 'userid', $uname[0]['id'] );
		else:
			$mod_message->add( 'NotFound' );
			die( header( 'Location: ' . $c_config['root'] ) );
		endif;
	else:
		$mod_template->add( 'username', 'Your' );
		$mod_template->add( 'userid', $mod_user->session_userid() );
	endif;

	//api & logged in?
	if( !$mod_config['api'] and $mod_template->get( 'userid' ) ):
		//load the users sources
		$sources = $mod_load->load_sources( $mod_template->get( 'userid' ) );
		$mod_template->add( 'sources', $sources );

		//accounts
		if( $mod_template->get( 'userid' ) == $mod_user->get_userid() ):
			$accounts = $mod_load->load_accounts( $mod_user->get_userid() );
			$mod_template->add( 'accounts', $accounts );
		endif;
		
		//load the users followings
		$followings = $mod_load->load_users( $mod_template->get( 'userid' ) );
		$mod_template->add( 'followings', $followings );

		//load users collections
		$collections = $mod_load->load_collections( $mod_template->get( 'userid' ) );
		$mod_template->add( 'collections', $collections );
	endif;

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, 'collection' ) : new mod_stream_site( $mod_db, 'collection' );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set user & stream id 
	$mod_stream->set_collectionid( $_GET['id'] );
	$mod_stream->set_offset( $offset );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get the data
	$stream_data = $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build();
	
	//add data
	$mod_template->add( 'stream', $stream_data['items'] );
	$mod_template->add( 'title', 'collection' );
	$mod_template->add( 'pageTitle', $mod_template->get( 'username' ) . ' Collection: ' . $collection['name'] );
	$mod_template->add( 'collection_id', $collection['id'] );
	$mod_template->add( 'nextOffset', $offset + 1 );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>