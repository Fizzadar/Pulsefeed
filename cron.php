<?php
	/*
		file: cron.php
		desc: runs the repeat-jobs (fetches articles, etc)
	*/

	//set the process
	$_GET['process'] = 'cron';

	//now get/load index
	require( 'index.php' );
?>