<?php
	/*
		file: app/process/user/new.php
		desc: add new user data
	*/

	//modules
	global $mod_db, $mod_cookie, $mod_user, $mod_message;

	//insert following
	$mod_db->query( '
		INSERT IGNORE INTO mod_user_follows
		( user_id, following_id )
		VALUES ( ' . $mod_user->get_userid() . ', 1 )
	' );

	//insert source
	$mod_db->query( '
		INSERT IGNORE INTO mod_user_sources
		( user_id, source_id )
		VALUES ( ' . $mod_user->get_userid() . ', 1 )
	' );

	//insert 'read later' collection
	$mod_db->query( '
		INSERT IGNORE INTO mod_collection
		( user_id, name, time )
		VALUES ( ' . $mod_user->get_userid() . ', "Read later", ' . time() . ' )
	' );

	//temp - promote to alpha user
	$mod_user->set_data( array( 'group' => 3 ) );
	$mod_user->relogin();

	//add cookie for username change message
	$mod_cookie->set( 'ChangeUsernameMessage', '1' );
?>