<?php
	//css
	header( 'Content-type: text/css' );

	//get our css files
	$css_files = glob( 'inc/css/*.css' );

	//build files list
	if( isset( $_GET['type'] ) and !empty( $_GET['type'] ) ):
		$list = explode( ',', $_GET['type'] );
		foreach( $list as $k => $f ):
			if( !in_array( 'inc/css/' . $f . '.css', $css_files ) )
				unset( $list[$k] );
			else
				$list[$k] = $f . '.css';
		endforeach;
		$files = $list;
	else:
		$files = $css_files;
	endif;

	//loop the files, apply css
	foreach( $files as $file ):
		$css = file_get_contents( 'inc/css/' . basename( $file ) );
		$css = str_replace(
			array(
				"\t",
				"\n"
			),
			'',
			$css
		);
		$css = str_replace( 'PULSEFEED_ROOT_DIR', $c_config['root'] . '/inc', $css );
		echo $css;
	endforeach;
?>