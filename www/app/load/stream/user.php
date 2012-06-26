<?php
	/*
		file: app/load/user.php
		desc: display & load user streams
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $mod_cookie, $mod_config, $mod_data, $mod_load, $mod_memcache, $mod_app, $user_id, $mod_template, $mod_streamcache;

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
	if( isset( $_GET['stream'] ) and in_array( $_GET['stream'], array( 'hybrid', 'unread', 'popular', 'newest', 'likes' ) ) )
		$stream_type = $_GET['stream'];

	//not current user? stop hybrid & unread
	if( $user_id != $mod_user->get_userid() and ( $stream_type == 'hybrid' or $stream_type == 'unread' ) )
		$stream_type = 'popular';

	//get user, check if exists
	$user = $mod_memcache->get( 'core_user', array( array(
		'id' => $user_id
	) ) );
	if( !isset( $user ) or count( $user ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//following
	$following = false;
	if( $mod_user->check_login() ):
		$following = count( $mod_memcache->get( 'mod_user_follows', array(
			array(
				'user_id' => $mod_user->get_userid(),
				'following_id' => $user_id
			)
		) ) ) == 1 ? true : false;
	endif;

	//start template
	$mod_template = new mod_template();

	//set our username
	if( $user_id == $mod_user->get_userid() )
		$name = '';
	else
		$name = $user[0]['name'] . '\'s';

	//api?
	if( !$mod_config['api'] ):
		$mod_app->load( 'load/stream/userconf' );
	endif;

	//start our stream
	$mod_stream = $mod_config['api'] ? new mod_stream( $mod_db, $stream_type, $mod_streamcache ) : new mod_stream_site( $mod_db, $stream_type, $mod_streamcache );
	//invalid stream?
	if( !$mod_stream->valid ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//set user & stream id 
	$mod_stream->set_userid( $user_id );
	$mod_stream->set_offset( $offset );
	$mod_stream->set_sinceid( $since_id );

	//prepare, ok to go after this
	if( !$mod_stream->prepare() ):
		$mod_message->add( 'DatabaseError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get the data
	$stream_data = $mod_config['api'] ? $mod_stream->get_data() : $mod_stream->build();

	//features?
	if( !$mod_config['api'] ):
		$mod_template->add( 'features', $stream_data['features'] );
	endif;
	
	//add data
	$mod_template->add( 'canonical', $c_config['root'] . '/user/' . $user_id . ( $stream_type != 'hybrid' ? '/' . $stream_type : '' ) );
	$mod_template->add( 'stream', $stream_data['items'] );
	$mod_template->add( 'title', $stream_type );
	$mod_template->add( 'pageTitle', $name . ' ' . ( isset( $stream_name ) ? $stream_name : ucfirst( $stream_type ) ) . ' Stream' );
	$mod_template->add( 'userid', $user_id );
	$mod_template->add( 'username', $user[0]['name'] );
	$mod_template->add( 'following', $following );
	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'user', $user[0] );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>