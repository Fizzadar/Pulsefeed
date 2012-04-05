<?php
	/*
		file: process/source/opml.php
		desc: add sources from opml file (& subscribe user)
	*/

	//modules
	global $mod_db, $mod_message, $mod_user, $mod_session, $mod_app;

	//redirect
	$redir = $c_config['root'] . '/sources/add';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//check post data
	if( !isset( $_FILES ) or !isset( $_FILES['opml_file'] ) or empty( $_FILES['opml_file']['tmp_name'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//are we logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//permission
	if( !$mod_user->check_permission( 'AddSource' ) ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//load our xml data from file
	$xml = simplexml_load_file( $_FILES['opml_file']['tmp_name'] );

	//quick recursive function loop through xml data
	function recursive( $xmldata ) {
		$return = array();
		foreach( $xmldata->children() as $child ):
			$attributes = array();
			foreach( $child->attributes() as $k => $v ):
				$attributes[$k] = (string) $v;
			endforeach;
			$return[] = $attributes;
			$data = recursive( $child );
			foreach( $data as $d ):
				$return[] = $d;
			endforeach;
		endforeach;

		return $return;
	}
	
	//build list of feeds
	$feeds = array();
	foreach( recursive( $xml ) as $v )
		if( isset( $v['title'] ) and isset( $v['xmlUrl'] ) and isset( $v['htmlUrl'] ) )
			$feeds[] = array(
				'site_title' => $v['title'],
				'feed_url' => $v['xmlUrl'],
				'site_url' => $v['htmlUrl']
			);

	//loop each feed
	foreach( $feeds as $key => $feed ):
		//check to see if we have the feed
		$exist = $mod_db->query( '
			SELECT id
			FROM mod_source
			WHERE feed_url = "' . $feed['feed_url'] . '"
			OR site_url = "' . $feed['site_url'] . '"
			LIMIT 1
		' );

		//if not, insert
		if( $exist and count( $exist ) == 1 ):
			$feeds[$key]['id'] = $exist[0]['id'];
		else:
			//insert to db
			$insert = $mod_db->query( '
				INSERT INTO mod_source
				( site_title, site_url, feed_url, user_id, time )
				VALUES ( "' . $feed['site_title'] . '", "' . $feed['site_url'] . '", "' . $feed['feed_url'] . '", ' . $mod_user->get_userid() . ', ' . time() . ' )
			' );
			if( $insert ):
				$feeds[$key]['id'] = $mod_db->insert_id();
			endif;
		endif;
	endforeach;

	//re-loop, subscribe to each
	$_POST['noredirect'] = true;
	foreach( $feeds as $feed ):
		if( isset( $feed['id'] ) and is_numeric( $feed['id'] ) ):
			$_POST['source_id'] = $feed['id'];
			$mod_app->load( 'process/source/subscribe' );
		endif;
	endforeach;

	//finally, redirect
	$mod_message->add( 'SourcesSubscribed' );
	header( 'Location: ' . $redir );
?>