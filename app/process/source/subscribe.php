<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_config, $mod_memcache;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/sources';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['source_id'] ) or !is_numeric( $_POST['source_id'] ) or $_POST['source_id'] <= 0 ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//permission
	if( !$mod_user->check_permission( 'Subscribe' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;
	
	//locate our source
	$source = $mod_db->query( '
		SELECT id
		FROM mod_source
		WHERE id = ' . $_POST['source_id'] . '
		LIMIT 1
	' );
	if( !$source or count( $source ) != 1 ):
		$mod_message->add( 'NoSource' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert the subscription
	$insert = $mod_memcache->set( 'mod_user_sources', array(
		array(
			'source_id' => $_POST['source_id'],
			'user_id' => $mod_user->get_userid()
		)
	) );
	$insert_rows = $mod_db->affected_rows();

	//redirect
	if( $insert ):
		//add subscriber (if affected = 1)
		if( $insert_rows == 1 ):
			$mod_db->query( '
				UPDATE mod_source
				SET subscribers = subscribers + 1
				WHERE id = ' . $_POST['source_id'] . '
				LIMIT 1
			' );
		endif;

		//message, redirect
		if( !isset( $_POST['noredirect'] ) ):
			$mod_message->add( 'SourceSubscribed' );
			header( 'Location: ' . $redir );
		endif;
	else:
		//still here?
		if( !isset( $_POST['noredirect'] ) ):
			$mod_message->add( 'UnknownError' );
			header( 'Location: ' . $redir );
		endif;
	endif;
?>