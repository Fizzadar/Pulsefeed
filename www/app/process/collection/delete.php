<?php
	/*
		file: app/process/collection/delete.php
		desc: delete a collection
	*/

	global $mod_user, $mod_db, $mod_session, $mod_memcache, $mod_message;

	//redirect
	$redir = $c_config['root'] . '/settings/collections';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//no id?
	if( !isset( $_POST['collection_id'] ) or !is_numeric( $_POST['collection_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check for collection (which user owns)
	$collection = $mod_memcache->get( 'mod_collection', array(
		array(
			'id' => $_POST['collection_id']
		)
	) );

	//oh? no collection
	if( count( $collection ) != 1 ):
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $redir ) );
	endif;
	$collection = $collection[0];

	//is the collection ours?
	if( $collection['user_id'] != $mod_user->get_userid() ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//ok, lets do this
	$collection = $mod_memcache->delete( 'mod_collection', array(
		array(
			'id' => $_POST['collection_id']
		)
	) );
	$mod_db->query( '
		DELETE FROM mod_collection_articles
		WHERE collection_id = ' . $_POST['collection_id']
	);

	$mod_message->add( 'CollectionDeleted' );
	header( 'Location: ' . $redir );
?>