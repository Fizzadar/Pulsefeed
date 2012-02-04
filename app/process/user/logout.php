<?php
	//load modules
	global $mod_user, $mod_message;
	
	//logout user
	var_dump( $mod_user->logout() );
	global $c_config;
	print_r( $c_config );
	die();

	//message
	$mod_message->add( 'LoggedOut' );
	
	//redirect
	if( !empty( $_SERVER['HTTP_REFERER'] ) ):
		header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
	else:
		header( 'Location: ' . $c_config['root'] );
	endif;
?>