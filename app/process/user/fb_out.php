<?php
	//modules
	global $mod_user, $mod_message;

	//make url
	$return_url = $c_config['root'] . '/process/login-facebook';

	//and go
	if( $url = $mod_user->fb_out( $return_url, 'publish_actions,offline_access' ) ):
		header( 'Location: ' . $url );
	else:
		$mod_message->add( 'LoginServerError' );
		header( 'Location: ' . $c_config['root'] );
	endif;
?>