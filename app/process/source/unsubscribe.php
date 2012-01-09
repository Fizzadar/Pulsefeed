<?php
	/*
		file: app/process/source/subscribe.php
		desc: subscribe to a source
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message;

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
	$mod_db->query( '
		DELETE FROM mod_user_sources
		WHERE source_id = ' . $_POST['source_id'] . '
		AND user_id = ' . $mod_user->get_userid() . '
		LIMIT 1
	' );

	//affected?
	if( $mod_db->affected_rows() > 0 ):
		$mod_db->query( '
			UPDATE mod_source
			SET subscribers = subscribers - 1
			WHERE id = ' . $_POST['source_id'] . '
			LIMIT 1
		' );
	endif;

	//delete unreads for this source
	$mod_db->query( '
		DELETE mod_user_unread FROM mod_user_unread
		JOIN mod_article ON mod_article.id = mod_user_unread.article_id AND mod_article.source_id = ' . $_POST['source_id'] . '
		WHERE mod_user_unread.user_id = ' . $mod_user->get_userid() . '
	' );

	//done!
	$mod_message->add( 'SourceUnsubscribed' );
	header( 'Location: ' . $redir );
?>