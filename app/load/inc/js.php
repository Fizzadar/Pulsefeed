<?php
	//modules
	global $mod_token;

	//javascript
	header( 'Content-type: text/javascript' );
?>
var mod_token = '<?php echo $mod_token; ?>';
var mod_root = '<?php echo $c_config['root']; ?>';