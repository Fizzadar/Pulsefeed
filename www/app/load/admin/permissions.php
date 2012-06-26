<?php
	/*
		file: app/load/admin/permissions.php
		desc: admin permissions page
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

	//load user groups
	$groups = $mod_db->query( '
		SELECT * FROM core_user_groups
		ORDER BY id ASC
	' );

	//load user permissions
	$permissions = $mod_db->query( '
		SELECT * FROM core_user_permissions
	' );

	//build ranks/groups
	$ranks = array();
	foreach( $groups as $group )
		$ranks[$group['id']] = array(
			'name' => $group['name'],
			'id' => $group['id'],
			'permission' => array(),
			'nopermission' => array()
		);

	//add permissions
	$unique_permissions = array();
	foreach( $permissions as $permission ):
		if( !in_array( $permission['permission'], $unique_permissions ) )
			$unique_permissions[] = $permission['permission'];

		if( isset( $ranks[$permission['group_id']] ) )
			$ranks[$permission['group_id']]['permission'][] = $permission['permission'];
	endforeach;

	//work out which permissions a group does not have
	foreach( $ranks as $key => $rank )
		foreach( $unique_permissions as $permission )
			if( !in_array( $permission, $rank['permission'] ) )
				$ranks[$key]['nopermission'][] = $permission;

	//add to template
	$mod_template->add( 'ranks', $ranks );

	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'admin/permissions' );
	$mod_template->load( 'core/footer' );
?>