<?php
	$article = $this->get( 'article' );
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xmlns:fb="https://www.facebook.com/2008/fbml"> 
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# pulsefeeder: http://ogp.me/ns/fb/pulsefeeder#">
	<meta property="fb:app_id"      content="346508828699100" /> 
	<meta property="og:type"        content="pulsefeeder:article" /> 
	<meta property="og:url"         content="<?php echo $c_config['root']; ?>/article/<?php echo $article['id']; ?>" /> 
	<meta property="og:title"       content="<?php echo $article['title']; ?>" /> 
	<meta property="og:description" content="<?php echo $article['description']; ?>" /> 
	<meta property="og:image"       content="<?php echo empty( $article['image_quarter'] ) ? 'https://s-static.ak.fbcdn.net/images/devsite/attachment_blank.png' : $c_config['root'] . '/' . $article['image_quarter']; ?>" /> 
</head>
</html>