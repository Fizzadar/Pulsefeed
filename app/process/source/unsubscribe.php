<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_memcache;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/sources';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['source_id'] ) or !is_numeric( $_POST['source_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//delete from user sources
	$mod_memcache->delete( 'mod_user_sources', array(
		array(
			'source_id' => $_POST['source_id'],
			'user_id' => $mod_user->get_userid()
		)
	) );

	//affected?
	if( $mod_db->affected_rows() > 0 ):
		$mod_db->query( '
			UPDATE mod_source
			SET subscribers = subscribers - 1
			WHERE id = ' . $_POST['source_id'] . '
			LIMIT 1
		' );
	endif;

	//done!
	$mod_message->add( 'SourceUnsubscribed' );
	header( 'Location: ' . $redir );
?>