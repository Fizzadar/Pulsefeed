<?php
	//load modules
	global $mod_user, $mod_message;
	
	//logout user
	$mod_user->logout();

	//message
	$mod_message->add( 'LoggedOut' );
	
	//redirect
	header( 'Location: ' . $c_config['root'] );
?>