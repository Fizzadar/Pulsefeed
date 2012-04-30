<?php
	global $mod_db, $mod_user, $mod_app, $mod_cookie;

	//start template
	$mod_template = new mod_template();

	//where are we going after logging in?
	if( isset( $_SERVER['HTTP_REFERER'] ) ):
		$url = parse_url( $_SERVER['HTTP_REFERER'] );
		$pfurl = parse_url( $c_config['root'] );
		$redir = $url['host'] == $pfurl['host'] ? $_SERVER['HTTP_REFERER'] : $c_config['root'];
	else:
		$redir = $c_config['root'];
	endif;

	//set redirect dir
	$mod_cookie->set( 'redirectUrl', $redir );
	$mod_template->add( 'redir', $redir );

	//login template (contains header/footer)
	$mod_template->load( 'login' );
?>