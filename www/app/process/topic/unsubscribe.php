<?php
	/*
		file: app/process/topic/unsubscribe.php
		desc: unsubscribe to a topic
	*/

	//modules
	global $mod_session, $mod_user, $mod_db, $mod_message, $mod_memcache;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'] . '/topics';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['topic_id'] ) or !is_numeric( $_POST['topic_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//locate our topic
	$topic = $mod_memcache->get( 'mod_topic', array( array(
		'id' => $_POST['topic_id']
	) ) );
	if( !$topic or count( $topic ) != 1 ):
		$mod_message->add( 'NoTopic' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//test
	$unsubscribed = count( $mod_memcache->get( 'mod_user_topics', array( array(
		'topic_id' => $_POST['topic_id'],
		'user_id' => $mod_user->get_userid()
	) ) ) ) == 0;
	if( $unsubscribed ):
		$mod_message->add( 'TopicUnsubscribed' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//delete from user sources
	$delete = $mod_memcache->delete( 'mod_user_topics', array( array(
		'topic_id' => $_POST['topic_id'],
		'user_id' => $mod_user->get_userid()
	) ) );
	
	//affected?
	if( $delete ):
		$mod_memcache->set( 'mod_topic', array( array(
			'id' => $_POST['topic_id'],
			'subscribers' => $topic[0]['subscribers'] - 1
		) ), false );

		//delete user_articles
		$mod_db->query( '
			DELETE FROM mod_user_articles
			WHERE source_type = "topic"
			AND user_id = ' . $mod_user->get_userid() . '
			AND source_id = ' . $_POST['topic_id']
		);
	else:
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//done!
	$mod_message->add( 'TopicUnsubscribed' );
	header( 'Location: ' . $redir );
?>