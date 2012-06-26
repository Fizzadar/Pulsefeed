<?php
	/*
		file: app/load/share.php
		desc: load share article
	*/
	
	//modules
	global $mod_user, $mod_load, $mod_message, $mod_memcache;

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

	//load article
	$article = $mod_memcache->get( 'mod_article', array( array(
		'id' => $_GET['id']
	) ) );
	if( !$article or count( $article ) != 1 ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;
	$mod_template->add( 'article', $article[0] );

	//shared?
	$shared = count( $mod_memcache->get( 'mod_user_shares', array( array(
		'user_id' => $mod_user->session_userid(),
		'article_id' => $_GET['id']
	) ) ) ) == 1;
	$mod_template->add( 'shared', $shared );
	
	//load templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'article/share' );
	$mod_template->load( 'core/footer' );
?>