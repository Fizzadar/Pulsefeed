<?php
	/*
		file: app/load/collect.php
		desc: load collect an article (ie load collections)
	*/
	
	//modules
	global $mod_user, $mod_load, $mod_message;

	//no id?
	if( !isset( $_GET['id'] ) or !is_numeric( $_GET['id'] ) ):
		$mod_message->add( 'InvalidGet' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//start template
	$mod_template = new mod_template();

	//load user collections
	$collections = $mod_load->load_collections( $mod_user->get_userid() );
	$mod_template->add( 'collections', $collections );

	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'article/collect' );
	$mod_template->load( 'core/footer' );
?>