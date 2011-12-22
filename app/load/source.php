<?php
	/*
		file: app/load/user.php
		desc: display & load user streams
	*/

	//modules
	global $mod_user, $mod_db, $mod_message, $c_debug;

	//work out userid
	$user_id = 0;
	if( isset( $_GET['id'] ) and is_numeric( $_GET['id'] ) )
		$user_id = $_GET['id'];
	else
		$user_id = $mod_user->get_userid();

	//check the user exists
	$exists = $mod_db->query( '
		SELECT id
		FROM mod_source
		WHERE id = ' . $user_id . '
		LIMIT 1
	' );
	if( !$exists or count( $exists ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	
	//work out stream type
	$stream_type = 'hybrid';
	if( isset( $_GET['stream'] ) )
		$stream_type = $_GET['stream'];

	//stream_id as stream_type?
	$stream_id = 0;
	if( is_numeric( $stream_type ) ):
		$stream_id = $stream_type;
		$stream_type = 'user';
	endif;

	//start our stream
	$mod_stream = new mod_stream( $mod_db, $stream_type );
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

	//now we got the data, lets go!
	$data = $mod_stream->get_data();

	//start template
	$mod_template = new mod_template();

	//add data
	$mod_template->add( 'stream', $data );
	$mod_template->add( 'title', $stream_type );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stream' );
	$mod_template->load( 'core/footer' );
?>