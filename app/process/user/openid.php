<?php
	//no openid?
	if( !isset( $_GET['openid'] ) )
		header( 'Location: ' . $c_config['root'] );

	//openid, lets go
	$openid = new LightOpenID;
	$openid->identity = $_GET['openid'];
	$openid->realm = $c_config['base'];
	$openid->returnUrl = $c_config['root'] . '/?process=login-openid' . ( isset( $_SERVER['HTTP_REFERER'] ) ? '&return_url=' . $_SERVER['HTTP_REFERER'] : '' );
	
	try {
		$location = $openid->authUrl();
		header( 'Location: ' . $location );
	} catch( Exception $e ) {
		header( 'Location: ' . $c_config['root'] . '?notice=openid_server_error' );
	}
?>