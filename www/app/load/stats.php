<?php
	/*
		file: app/load/stats.php
		desc: stats for pulsefeed
	*/

	//modules
	global $mod_db;

	//template
	$mod_template = new mod_template;

	//load stats. cached
	$articles = $mod_db->query( '
		SELECT COUNT( id ) AS count FROM mod_article
	', true, 8600 );
	$websites = $mod_db->query( '
		SELECT COUNT( id ) AS count FROM mod_website
	', true, 8600 );
	$topics = $mod_db->query( '
		SELECT COUNT( id ) AS count FROM mod_topic
	', true, 8600 );
	/*$users = $mod_db->query( '
		SELECT COUNT( id ) AS count FROM core_user
	', true, 8600 );*/

	//add to template
	$mod_template->add( 'stats', array(
		'articles' => $articles[0]['count'],
		'websites' => $websites[0]['count'],
		'topics' => $topics[0]['count'],
		//'users' => $users[0]['count']
	) );

	//load
	$mod_template->load( 'core/header' );
	$mod_template->load( 'stats' );
	$mod_template->load( 'core/footer' );
?>