<?php
	/*
		file: compile.php
		desc: compiles css & js
	*/

	//are we cli?
	if( !isset( $argv ) )
		die( header( 'Location: http://pulsefeed.com' ) );

	//ignore user abort
	ignore_user_abort( true );
	//time limit
	set_time_limit( 0 );


?>