<?php
	/*
		file: app/process/user/load.php
		desc: load users unread count, etc
	*/

	//modules
	global $mod_db, $mod_cookie, $mod_user, $mod_message;

	//no login?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//select our unread
	$unread = $mod_db->query( '
		SELECT article_id
		FROM mod_user_unread
		WHERE user_id = ' . $mod_user->get_userid() . '
	' );
	//unread?
	if( $unread )
		$mod_cookie->set( 'Unread', count( $unread ) );
?>