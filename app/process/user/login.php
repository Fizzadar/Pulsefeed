<?php
	//modules
	global $mod_user, $mod_app, $mod_message;

	//login
	$login = 0;
	$redir = $c_config['root'];
	if( $_GET['process'] == 'login-facebook' ):
		$login = $mod_user->fb_login();
		$redir = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $c_config['root'];
	elseif( $_GET['process'] == 'login-openid' ):
		$login = $mod_user->openid_login();
		$redir = isset( $_GET['return_url'] ) ? $_GET['return_url'] : $c_config['root'];
	endif;

	//logged in?
	if( $login == 1 )
		$mod_app->load( 'process/user/load' );

	//redirect on new/fail
	if( $login == 2 ):
		$mod_message->add( 'NewUser' );
		header( 'Location: ' . $c_config['root'] . '/user/new' );
	elseif( $login == 1 ):
		$mod_message->add( 'LoggedIn' );
		header( 'Location: ' . $redir );
	elseif( $login == 3 ):
		$mod_message->add( 'OpenidAdded' );
		header( 'Location: ' . $redir );
	else:
		$mod_message->add( 'FailedLogin' );
		header( 'Location: ' . $redir );
	endif;
?>