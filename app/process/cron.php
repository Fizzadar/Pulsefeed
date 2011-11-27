<?php
	//command line only
	//if( !defined( 'STDIN' ) )
		//die( 'Command line only' );

	//no time limits
	set_time_limit( 0 );
	//ignore abort
	ignore_user_abort( true );

	//load modules
	global $mod_db;


	$test = new mod_source();
	$t = $test->load( 'http://rss1.smashingmagazine.com/feed/' );
	foreach( $t as $tt ):
		$article = $tt->get_article();
		echo '<img src="' . $tt->get_thumb() . '" />';
		echo '<hr />';
	endforeach;
	//var_dump( $t );
?>