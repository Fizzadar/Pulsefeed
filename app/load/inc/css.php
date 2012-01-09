<?php
	//modules
	global $mod_cookie;

	$color = '454E9B';
	if( $mod_cookie->get( 'SettingColor' ) )
		$color = $mod_cookie->get( 'SettingColor' );

	//css
	header( 'Content-type: text/css' );
?>

a {
	color: #<?php echo $color; ?>;
}


div#top {
	background: #<?php echo $color; ?>;
}
	div#top ul#account:hover li.top a {
		color: #<?php echo $color; ?>;
	}


div#header ul li label {
	color: #<?php echo $color; ?>;
}


div#sidebars div.right a.biglink span {
	color: #<?php echo $color; ?>;
}


div.article h2 a:hover, div.article h3 a:hover, div.article h4 a:hover {
	color: #<?php echo $color; ?>;
}


div.article div.meta div.details span a {
	color: #<?php echo $color; ?>;
}