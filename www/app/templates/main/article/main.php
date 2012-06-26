<?php
	global $mod_user, $mod_cookie, $mod_token;

	if( $this->get( 'unread' ) ):
?>
<?php
	endif;
?>

<script type="text/javascript">
	pulsefeed.frameurl = '<?php echo $this->content['article']['end_url']; ?>';
	pulsefeed.xframe = <?php echo $this->content['article']['xframe'] ? 'true' : 'false'; ?>;
	pulsefeed.article_id = <?php echo $this->content['article']['id']; ?>;
	pulsefeed.article_type = '<?php echo $this->content['article']['type']; ?>';
</script>

<div class="iframeborder">
	<?php if( $this->content['article']['xframe'] and $this->content['article']['type'] == 'text' ): ?>
		<script type="text/javascript">
			pulsefeed.xframe = true;
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
	<?php if( $mod_cookie->get( 'no_js' ) ): ?>
		<iframe class="externalarticle" src="<?php echo $this->content['article']['end_url']; ?>"></iframe>
	<?php endif; ?>

	<?php endif; ?>
</div><!--end frameborder-->