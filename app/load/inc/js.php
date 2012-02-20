<?php
	//modules
	global $mod_token, $c_debug;

	//disable debug output
	$c_debug->disable();
	
	//javascript
	header( 'Content-type: text/javascript' );
?>
var mod_token = '<?php echo $mod_token; ?>';
var mod_root = '<?php echo $c_config['root']; ?>';