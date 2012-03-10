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
	if( !isset( $_GET['type'] ) or !$mod_user->check_login() ):
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
		$accounts = array();
		$sources = $mod_load->load_sources( $mod_user->get_userid() );
		foreach( $sources as $key => $s ):
			if( $s['type'] != 'source' ):
				unset( $sources[$key] );
				$accounts[] = $s;
			endif;
		endforeach;
		$mod_template->add( 'accounts', $accounts );
		$mod_template->add( 'sources', $sources );

		//load the users followings
		$followings = $mod_load->load_users( $mod_user->get_userid() );
		$mod_template->add( 'followings', $followings );
	endif;

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, 'account' ) : new mod_stream_site( $mod_db, 'account' );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set offset
	$mod_stream->set_offset( $offset * 64 );

	//set userid
	$mod_stream->set_userid( $mod_user->get_userid() );

	//set account type
	$mod_stream->set_accountType( $_GET['type'] );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):die( 'wtf' );
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get the data
	$stream_data = $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build();

	//add data
	$mod_template->add( 'stream', $stream_data['items'] );
	$mod_template->add( 'title', 'account' );
	$mod_template->add( 'pageTitle', 'Your ' . ucfirst( $_GET['type'] ) . ' Stream' );
	$mod_template->add( 'userid', $mod_user->session_userid() );
	$mod_template->add( 'username', $mod_user->session_username() );
	$mod_template->add( 'streamid', 0 );
	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'account_type', $_GET['type'] );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>