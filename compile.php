<?php
	/*
		file: compile.php
		desc: compiles css & js into compiled.css & compiled.js
	*/

	//set cron
	$_GET['iscron'] = true;

	//set some server vars
	$_SERVER['HTTP_HOST'] = '';

	//get index, which returns early
	require( 'www/index.php' );

	//file lists (after www/inc/)
	$css = array(
		'basics',
		'core',
		'main',
	);

	//js
	$jss = array(
		'pulsefeed',
		'message',
		'api',
		'api.stream',
		'api.search',
		'api.frame',
		'api.page',
		'template',
		'design',
		'queue',
		'cookie',
		'lib/html_decode'
	);

	//inc dir
	$dir = 'www/inc/';

	//loop each css file
	$compiled_css = '';

	//minify
	foreach( $css as $css ):
		$tmp = file_get_contents( $dir . 'css/' . $css . '.css' );
		$compiled_css .= str_replace( array(
				"\n",
				"\r",
				"\t"
			), '', $tmp );
	endforeach;

	//save
	file_put_contents( $dir . 'css/compiled.css', $compiled_css );
	echo 'css compiled' . PHP_EOL;




	//loop each js file
	$compiled_js = '';
	foreach( $jss as $js ):
		$tmp = file_get_contents( $dir . 'js/' . $js . '.js' );
		$compiled_js .= $tmp;
	endforeach;

	//minify
	$min = new Minifier;
	$compiled_js = $min->minify( $compiled_js );

	//save
	file_put_contents( $dir . 'js/compiled.js', $compiled_js );
	echo 'js compiled' . PHP_EOL;
?>