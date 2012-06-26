<?php
	/*
		file: app/load/admin/users.php
		desc: admin users page
	*/

	//modules
	global $mod_user, $mod_message, $mod_db, $mod_config;

	//permission?
	if( !$mod_user->check_permission( 'Admin' ) or $mod_config['api'] ):
		$mod_message->add( 'NoPermission' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template;

	//get all users
	$users = $mod_db->query( '
		SELECT id, name, `group`, email
		FROM core_user
		ORDER BY id ASC
	' );

	//load user groups
	$groups = $mod_db->query( '
		SELECT id, name FROM core_user_groups
		ORDER BY id ASC
	' );
	$tmp = array();
	foreach( $groups as $group )
		$tmp[$group['id']] = $group['name'];

	//add user group names
	foreach( $users as $key => $user )
		$users[$key]['group'] = $tmp[$user['group']];

	//add to template
	$mod_template->add( 'users', $users );
	
	//template
	$mod_template->load( 'core/header' );
	$mod_template->load( 'admin/users' );
	$mod_template->load( 'core/footer' );
?>