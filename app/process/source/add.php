<?php
	/*
		file: app/process/source/add.php
		desc: add source
	*/

	//modules
	global $mod_db, $mod_user, $mod_session, $mod_message, $mod_app;

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//check post data
	if( !isset( $_POST['source_url'] ) or empty( $_POST['source_url'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//are we logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//try to find the feed
	$feed = false;
	$mod_source = new mod_source();
	$feed = $mod_source->find( $_POST['source_url'] );
	//failed to find it?
	if( !$feed ):
		$mod_message->add( 'NoFeedFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//source not exist already? create it (using feed_url)
	$source_id = 0;
	$exist = $mod_db->query( '
		SELECT id
		FROM mod_sources
		WHERE feed_url = "' . $feed['feed_url'] . '"
		LIMIT 1
	' );
	if( $exist and count( $exist ) == 1 ):
		//set the source id
		$source_id = $exist[0]['id'];
	else:
		//create the source
		$create = $mod_db->query( '
			INSERT INTO mod_sources
			( site_title, site_url, feed_url, user_id, time )
			VALUES ( "' . $feed['site_title'] . '", "' . $feed['site_url'] . '", "' . $feed['feed_url'] . '", ' . $mod_user->get_userid() . ', ' . time() . ' )
		' );
		//failed?
		if( !$create ):
			$mod_message->add( 'DatabaseError' );
			die( header( 'Location: ' . $c_config['root'] ) );
		endif;
		//set the id
		$source_id = $mod_db->insert_id();
	endif;

	//final check
	if( $source_id == 0 ):
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//& now subscribe to it
	$_POST['source_id'] = $source_id;
	$mod_app->load( 'process/source/subscribe' );
?>