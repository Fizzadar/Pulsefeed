<?php
	//no openid?
	if( !isset( $_GET['openid'] ) )
		header( 'Location: ' . $c_config['root'] );

	//modules
	global $mod_user, $mod_message;

	//make url
	$return_url = $c_config['root'] . '/process/login-openid';
	
	//and go
	if( $url = $mod_user->oid_out( $_GET['openid'], $return_url ) ):
		header( 'Location: ' . $url );
	else:
		$mod_message->add( 'LoginServerError' );
		header( 'Location: ' . $c_config['root'] );
	endif;
?>