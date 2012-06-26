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
	if( !isset( $_POST['website_id'] ) or !is_numeric( $_POST['website_id'] ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//locate our website
	$website = $mod_memcache->get( 'mod_website', array( array(
		'id' => $_POST['website_id']
	) ) );
	if( !$website or count( $website ) != 1 ):
		$mod_message->add( 'NoWebsite' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//test if already unsubscribed
	$unsubscribed = count( $mod_memcache->get( 'mod_user_websites', array( array(
		'website_id' => $_POST['website_id'],
		'user_id' => $mod_user->get_userid()
	) ) ) ) == 0;
	//already subscribed? end here
	if( $unsubscribed ):
		$mod_message->add( 'WebsiteUnsubscribed' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//delete from user sources
	$delete = $mod_memcache->delete( 'mod_user_websites', array(
		array(
			'website_id' => $_POST['website_id'],
			'user_id' => $mod_user->get_userid()
		)
	) );
	
	//affected?
	if( $delete ):
		$mod_memcache->set( 'mod_website', array( array(
			'id' => $_POST['website_id'],
			'subscribers' => $website[0]['subscribers'] - 1
		) ), false );

		//delete user_articles
		$mod_db->query( '
			DELETE FROM mod_user_articles
			WHERE source_type = "website"
			AND user_id = ' . $mod_user->get_userid() . '
			AND source_id = ' . $_POST['website_id']
		);
	endif;

	//done!
	$mod_message->add( 'WebsiteUnsubscribed' );
	header( 'Location: ' . $redir );
?>