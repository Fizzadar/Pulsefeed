<?php
	/*
		file: app/process/article/collect
		desc: collect an article
	*/

	//modules
	global $mod_db, $mod_user, $mod_message, $mod_session, $mod_memcache;

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//no id?
	if( !isset( $_POST['article_id'] ) or !is_numeric( $_POST['article_id'] ) or !isset( $_POST['collection_id'] ) or !is_numeric( $_POST['collection_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//redirect dir
	$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'];

	//permission
	if( !$mod_user->check_permission( 'Collect' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//do we have collection id?
	$collection = $mod_memcache->get( 'mod_collection', array(
		array(
			'id' => $_POST['collection_id']
		)
	) );
	
	//our collection?
	if( !$collection or count( $collection ) != 1 or $collection[0]['user_id'] != $mod_user->get_userid() ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//remove article from collection
	$delete = $mod_db->query( '
		DELETE FROM mod_collection_articles
		WHERE article_id = ' . $_POST['article_id'] . '
		AND collection_id = ' . $_POST['collection_id'] . '
	' );
	$delete_rows = $mod_db->affected_rows();

	//did it work?
	if( $delete ):
		//up article count
		if( $delete_rows == 1 ):
			$mod_db->query( '
				UPDATE mod_collection
				SET articles = articles - 1
				WHERe id = ' . $_POST['collection_id'] . '
				LIMIT 1
			' );
		endif;


		$mod_message->add( 'ArticleUnCollected' );
		header( 'Location: ' . $redir );
	else:
		//baww
		$mod_message->add( 'UnknownError' );
		header( 'Location: ' . $redir );
	endif;
?>