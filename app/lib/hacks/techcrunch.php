<?php
	$content = $this->get_content();
	$content = str_replace( '?w=100&amp;h=70&amp;crop=1', '', $content );
	return $content;
?>