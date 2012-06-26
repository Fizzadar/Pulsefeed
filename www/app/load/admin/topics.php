<?php
	/*
		file: app/load/admin/topics.php
		desc: admin topics page
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

	//get all topics
	$data = $mod_db->query( '
		SELECT id, parent_id, title, type, auto_tag
		FROM mod_topic
		ORDER BY title
	' );

	//build topics
	$topics = array();
	$topics_parent_no_parent = array();
	$topics_specific_no_parent = array();
	$parent_to_general = array();

	//loop general topics
	foreach( $data as $topic ):
		if( $topic['type'] != 'general' )
			continue;

		//add topic
		$topics[$topic['id']] = $topic;
		$topics[$topic['id']]['children'] = array();
	endforeach;

	//loop parent topics
	foreach( $data as $topic ):
		if( $topic['type'] != 'parent' )
			continue;

		//got parent?
		if( $topic['parent_id'] > 0 ):
			$topics[$topic['parent_id']]['children'][$topic['id']] = $topic;
			$topics[$topic['parent_id']]['children'][$topic['id']]['children'] = array();
			$parent_to_general[$topic['id']] = $topic['parent_id'];
		else:
			$topics_parent_no_parent[$topic['id']] = $topic;
			$topics_parent_no_parent[$topic['id']]['children'] = array();
		endif;
	endforeach;

	//loop specific topics
	foreach( $data as $topic ):
		if( $topic['type'] != 'specific' )
			continue;

		//no parent?
		if( $topic['parent_id'] == 0 ):
			$topics_specific_no_parent[$topic['id']] = $topic;
			continue;
		endif;

		//direct specific under a global?
		if( isset( $topics[$topic['parent_id']] ) ):
			$topics[$topic['parent_id']]['children'][$topic['id']] = $topic;
			$topics[$topic['parent_id']]['children'][$topic['id']]['children'] = array();
			continue;
		endif;

		//parent has parent?
		if( isset( $parent_to_general[$topic['parent_id']] ) ):
			$topics[$parent_to_general[$topic['parent_id']]]['children'][$topic['parent_id']]['children'][] = $topic;
		else:
			$topics_parent_no_parent[$topic['parent_id']]['children'][] = $topic;
		endif;
	endforeach;

	//add to template
	$mod_template->add( 'topics', $topics );
	$mod_template->add( 'topics_parent_no_parent', $topics_parent_no_parent );
	$mod_template->add( 'topics_specific_no_parent', $topics_specific_no_parent );
	
	//template
	$mod_template->load( 'core/header' );
	$mod_template->load( 'admin/topics' );
	$mod_template->load( 'core/footer' );
?>