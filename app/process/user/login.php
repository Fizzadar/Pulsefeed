<?php
	//modules
	global $mod_user, $mod_app, $mod_message, $mod_cookie;

	//login
	$login = 0;
	$redir = $mod_cookie->get( 'redirectUrl' ) ? $mod_cookie->get( 'redirectUrl' ) : $c_config['root'];
	//facebook
	if( $_GET['process'] == 'login-facebook' ):
		$login = $mod_user->fb_login();
	//twitter
	elseif( $_GET['process'] == 'login-twitter' ):
		$login = $mod_user->tw_login();
	//openid
	elseif( $_GET['process'] == 'login-openid' ):
		$login = $mod_user->openid_login();
	endif;

	//load user
	$mod_app->load( 'process/user/load' );

	//redirect on new/fail
	if( $login == 2 ):
		$mod_app->load( 'process/user/new' );
		$mod_message->add( 'NewUser' );
		header( 'Location: ' . $c_config['root'] . '/user/' . $mod_user->get_userid() . '?welcome' );
	elseif( $login == 1 ):
		$mod_message->add( 'LoggedIn' );
		header( 'Location: ' . $redir );
	elseif( $login == 4 ):
		$mod_message->add( 'ReLoggedIn' );
		header( 'Location: ' . $redir );
	elseif( $login == 3 ):
		$mod_message->add( 'AccountAdded' );
		header( 'Location: ' . $redir );
	else:
		$mod_message->add( 'FailedLogin' );
		header( 'Location: ' . $redir );
	endif;
?>