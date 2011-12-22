<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message;

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//post data
	if( !isset( $_POST['source_id'] ) or !is_numeric( $_POST['source_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//locate our source
	$source = $mod_db->query( '
		SELECT id
		FROM mod_sources
		WHERE id = ' . $_POST['source_id'] . '
		LIMIT 1
	' );
	if( !$source or count( $source ) != 1 ):
		$mod_message->add( 'NoSource' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//insert the subscription
	$insert = $mod_db->query( '
		REPLACE INTO mod_user_sources
		( user_id, source_id )
		VALUES( ' . $mod_user->get_userid() . ', ' . $_POST['source_id'] . ' )
	' );

	//redirect
	if( $insert ):
		$mod_message->add( 'SourceSubscribed' );
		header( 'Location: ' . $c_config['root'] );
	else:
		$mod_message->add( 'UnknownError' );
		header( 'Location: ' . $c_config['root'] );
	endif;
?>