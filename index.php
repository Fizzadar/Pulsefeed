<?php
	/*
		 _____           _ _           
		|   __|___ ___ _| | |_ _ _ ___ 
		|   __| -_| -_| . | . | | | . |
		|__|  |___|___|___|___|___|_  |
		                          |___|
                          
		file: index.php
		desc: class loading, app routing
	*/

	//get the config
	require( 'app/config.php' );
	
	//get the core
	require( 'core/core.php' );

	//start the app
	$mod_app = new c_app( $mod_config['libs'] );

	//start our db
	$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );

	//user
	$mod_user = new c_user( $mod_db, 'feedbug_' );

	//session
	$mod_session = new c_session;
	$mod_token = $mod_session->generate();

	//message (after session to get that started)
	$mod_message = new mod_message( $mod_config['messages'] );
	
	//process(must be posted)
	if( isset( $_GET['process'] ) and isset( $mod_config['process'][$_GET['process']] ) ):
		$mod_app->load( 'process/' . $mod_config['process'][$_GET['process']] );
	//load
	elseif( isset( $_GET['load'] ) and isset( $mod_config['load'][$_GET['load']] ) ):
		$mod_app->load( 'load/' . $mod_config['load'][$_GET['load']] );
	//default
	else:
		$mod_app->load( 'load/' . $mod_config['load']['default'] );
	endif;

	$mod_message->display();
	$c_debug->display();
?>

<form action="<?php echo $c_config['root']; ?>/?process=source-add" method="post">
	enter a website or feed address
	<input type="text" name="source_url" id="source_url" value="" />
	<input type="submit" value="Add source &#187;" />
	<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
</form>