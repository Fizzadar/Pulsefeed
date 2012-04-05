<?php
	global $mod_user, $mod_cookie, $mod_token;

	if( $this->get( 'unread' ) ):
?>
<?php
	endif;
?>

<script type="text/javascript">
	pf_frameurl = '<?php echo $this->content['article']['url']; ?>';
</script>

<div class="iframeborder">
	<div id="loader">
		<div class="wrap">
			<img src="<?php echo $c_config['root']; ?>/inc/img/icons/loader.gif" alt="" /> article loading...
		</div>
	</div>
	<noscript>
		<iframe class="externalarticle" src="<?php echo $this->content['article']['url']; ?>"></iframe>
	</noscript>
</div><!--end frameborder-->