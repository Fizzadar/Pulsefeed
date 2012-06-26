<?php
	/*
		file: app/load/stream/userconf.php
		desc: manages user configuration, loaded by main stream files
		note: cookies -> load user data
	*/

	//modules
	global $mod_template, $user_id, $mod_load, $mod_cookie, $mod_user;

	//set stream to cookie
	$mod_cookie->set( 'RecentStream', $_SERVER['REQUEST_URI'] );

	//2/3 col
	if( isset( $_GET['two_col'] ) )
		$mod_cookie->set( 'two_col', true );
	elseif( isset( $_GET['three_col'] ) )
		$mod_cookie->delete( 'two_col' );

	//images on/off
	if( isset( $_GET['images_off'] ) )
		$mod_cookie->set( 'hide_images', true );
	elseif( isset( $_GET['images_on'] ) )
		$mod_cookie->delete( 'hide_images' );

	//hide message
	if( isset( $_GET['hide_message'] ) )
		$mod_cookie->set( 'hide_message', true );
	elseif( isset( $_GET['show_message'] ) )
		$mod_cookie->delete( 'hide_message' );

	//js on/off
	if( isset( $_GET['js_off'] ) )
		$mod_cookie->set( 'no_js', true );
	elseif( isset( $_GET['js_on'] ) )
		$mod_cookie->delete( 'no_js' );

	//userid not 0?
	if( $user_id > 0 ):
		//load the users sources
		$sources = $mod_load->load_sources( $user_id );
		$mod_template->add( 'websites', $sources );

		//load accounts if current user
		if( $mod_user->get_userid() == $user_id ):
			$accounts = $mod_load->load_accounts( $mod_user->get_userid() );
			$mod_template->add( 'accounts', $accounts );
		endif;

		//load the users followings
		$followings = $mod_load->load_users( $user_id );
		$mod_template->add( 'followings', $followings );

		//load users collections
		$collections = $mod_load->load_collections( $user_id );
		$mod_template->add( 'collections', $collections );

		//load users topics
		$topics = $mod_load->load_topics( $user_id );
		$mod_template->add( 'topics', $topics );
	endif;
?>