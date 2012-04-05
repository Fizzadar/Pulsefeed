<?php
	//modules
	global $mod_token, $c_debug, $mod_user;

	//uid
	$uid = $mod_user->session_userid();
	
	//javascript
	header( 'Content-type: text/javascript' );
?>
var mod_token = '<?php echo $mod_token; ?>';
var mod_root = 'http://<?php echo $c_config['host']; ?>';
var mod_userid = '<?php echo empty( $uid ) ? 0 : $uid; ?>';