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
	if( !isset( $_POST['article_id'] ) or !is_numeric( $_POST['article_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//permission
	if( !$mod_user->check_permission( 'Collect' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//redirect dir
	$redir = $c_config['root'] . '/article/' . $_POST['article_id'] . '/collect';

	//other post data wrong?
	if( !isset( $_POST['collection_id'] ) or !isset( $_POST['collection_name'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//make collection_name 30 chars long
	$_POST['collection_name'] = substr( $_POST['collection_name'], 0, 30 );

	//do we have collection id?
	$id = false;
	if( $_POST['collection_id'] > 0 ):
		//check for collection
		$collection = $mod_memcache->get( 'mod_collection', array(
			array(
				'id' => $_POST['collection_id']
			)
		) );
		if( count( $collection ) == 1 and $collection[0]['user_id'] == $mod_user->get_userid() ):
			$id = $collection[0]['id'];
		endif;
	elseif( !empty( $_POST['collection_name'] ) ):
		//add collection
		$insert = $mod_db->query( '
			INSERT INTO mod_collection
			( user_id, name, time )
			VALUES ( ' . $mod_user->get_userid() . ', "' . $_POST['collection_name'] . '", ' . time() . ' )
		' );
		if( $insert ):
			$id = $mod_db->insert_id();
		endif;
	endif;

	//id?
	if( !$id ):
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check article
	$article = $mod_memcache->get( 'mod_article', array(
		array(
			'id' => $_POST['article_id']
		)
	) );
	if( count( $article ) != 1 ):
		$mod_message->add( 'UnknownError' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//insert the article into the collection
	$insert = $mod_db->query( '
		INSERT IGNORE INTO mod_collection_articles
		( collection_id, article_id, time )
		VALUES( ' . $id . ', ' . $_POST['article_id'] . ', ' . time() . ' )
	' );
	$insert_rows = $mod_db->affected_rows();

	//did it work?
	if( $insert ):
		//up article count
		if( $insert_rows == 1 ):
			$mod_db->query( '
				UPDATE mod_collection
				SET articles = articles + 1
				WHERe id = ' . $id . '
				LIMIT 1
			' );
		endif;

		//redirect
		if( $_POST['collection_id'] <= 0 ):
			$mod_message->add( 'ArticleCollectedNew' );
		else:
			$mod_message->add( 'ArticleCollected' );
		endif;
		header( 'Location: ' . $redir );
	else:
		//baww
		$mod_message->add( 'UnknownError' );
		header( 'Location: ' . $redir );
	endif;
?>