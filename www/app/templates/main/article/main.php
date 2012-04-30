<?php
	global $mod_user, $mod_cookie, $mod_token;

	if( $this->get( 'unread' ) ):
?>
<?php
	endif;
?>

<script type="text/javascript">
	pf_frameurl = '<?php echo $this->content['article']['end_url']; ?>';
	pf_xframe = false;
</script>

<div class="iframeborder">
	<?php if( $this->content['article']['xframe'] and $this->content['article']['type'] == 'text' ): ?>
		<script type="text/javascript">
			pf_xframe = true;
		</script>

		<div id="xframe">
			<div class="wrap">
				<p>Unfortunately this website does not allow us to place their content in-line, please <a target="_blank" href="<?php echo $this->content['article']['end_url']; ?>">click here to open the page in a new tab &rarr;</a></p>
			</div>
		</div>

	<?php else: ?>

		<noscript>
			<iframe class="externalarticle" src="<?php echo $this->content['article']['end_url']; ?>"></iframe>
		</noscript>

	<?php endif; ?>
</div><!--end frameborder-->