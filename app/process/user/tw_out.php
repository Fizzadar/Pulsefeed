<?php
	//modules
	global $mod_user, $mod_message;

	//make url
	$return_url = $c_config['root'] . '/?process=login-twitter';
	
	//and go
	if( $url = $mod_user->tw_out( $return_url ) ):
		header( 'Location: ' . $url );
	else:
		$mod_message->add( 'LoginServerError' );
		header( 'Location: ' . $c_config['root'] );
	endif;
?>