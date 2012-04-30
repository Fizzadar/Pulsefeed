<?php
	/*
		file: app/load/page.php
		desc: load static page templates
	*/

	//modules
	global $mod_config;

	//no page set?
	if( !isset( $_GET['page'] ) or empty( $_GET['page'] ) or !isset( $mod_config['pages'][$_GET['page']] ) )
		die( header( 'Location: ' . $c_config['root'] . '/404' ) );

	//start template
	$mod_template = new mod_template;

	//header
	$mod_template->load( 'core/header' );
	//page
	$mod_template->load( 'page/' . $mod_config['pages'][$_GET['page']] );
	//footer
	$mod_template->load( 'core/footer' );
?>