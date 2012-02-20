<?php
	/*
		file: app/process/user/new.php
		desc: add new user data
	*/

	//modules
	global $mod_db, $mod_cookie, $mod_user, $mod_message;

	//insert following
	$mod_db->query( '
		INSERT INTO mod_user_follows
		( user_id, following_id )
		VALUES ( ' . $mod_user->get_userid() . ', 1 )
	' );

	//insert source
	$mod_db->query( '
		INSERT INTO mod_user_sources
		( user_id, source_id )
		VALUES ( ' . $mod_user->get_userid() . ', 1 )
	' );
?>