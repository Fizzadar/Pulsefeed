<?php
	/*
		file: app/load/source/add.php
		desc: add source form
	*/

	//modules
	global $mod_db;

	//start template
	$mod_template = new mod_template();

	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'source/add' );
	$mod_template->load( 'core/footer' );
?>