<?php
	global $mod_user, $mod_cookie, $mod_token;
	if( !isset( $_GET['f'] ) ) $_GET['f'] = 0;

	if( $this->get( 'unread' ) ):
?>
<?php
	endif;
?>

<div class="iframeborder">
	<div id="loader">
		<div class="wrap">
			<img src="<?php echo $c_config['root']; ?>/inc/img/icons/loader.gif" alt="" /> article loading...
		</div>
	</div>
	<iframe class="externalarticle" onload="$( '.iframeborder #loader' ).slideUp();" src="<?php echo $this->content['article']['url']; ?>"></iframe>
</div><!--end frameborder-->