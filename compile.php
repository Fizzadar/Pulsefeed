<?php
	/*
		file: compile.php
		desc: compiles css & js into compiled.css & compiled.js
	*/

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
		'queue'
	);

	//inc dir
	$dir = 'www/inc/';

	//loop each css file
	$compiled_css = '';
	foreach( $css as $css ):
		$tmp = file_get_contents( $dir . 'css/' . $css . '.css' );
		$compiled_css .= str_replace( array(
				"\n",
				"\r",
				"\t"
			), '', $tmp );
	endforeach;
	file_put_contents( $dir . 'css/compiled.css', $compiled_css );
	echo 'css compiled' . PHP_EOL;



	$compiled_js = '';
	foreach( $jss as $js ):
		$tmp = file_get_contents( $dir . 'js/' . $js . '.js' );
		$compiled_js .= str_replace( array(
				"\t"
			), '', $tmp );
	endforeach;
	file_put_contents( $dir . 'js/compiled.js', $compiled_js );
	echo 'js compiled' . PHP_EOL;
?>