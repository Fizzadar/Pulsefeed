<?php global $mod_user, $mod_cookie; ?>
	</div><!--end ajaxbox-->
	
	<div id="footer"<?php echo $this->get( 'externalHeader' ) ? ' style="display:none;"' : ''; ?>>
		<div class="wrap">
			<div class="inner">
				<div class="bottom">
					<div class="left">
						A <a href="http://fanaticaldev.com/" title="Fanatical Dev">Fanatical Dev</a> production<br />
						<a href="<?php echo $c_config['root']; ?>/about">About</a> - 
						<a href="<?php echo $c_config['root']; ?>/stats">Stats</a> - 
						<a href="<?php echo $c_config['root']; ?>/contact">Contact</a>
					</div><!--end left-->
					<div class="right">
						v<?php echo PULSEFEED_VERSION; ?> - <a href="http://blog.pulsefeed.com/category/updates" target="_blank">Updates</a><br />
						<a href="http://blog.pulsefeed.com" target="_blank">Blog</a> - 
						<a href="http://twitter.com/pulsefeed" target="_blank">Twitter</a> - 
						<strike><a href="#">Legal</a></strike>
					</div><!--end right-->
				</div><!--end bottom-->
			</div><!--end inner-->
		</div><!--end wrap-->
	</div><!--end footer-->

<?php if( !$mod_cookie->get( 'no_js' ) and !isset( $_GET['nojs'] ) ): ?>
	<!--scripts-->
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/lib/jquery.js"></script>
<?php if( $mod_user->session_permission( 'Debug' ) or $_SERVER['HTTP_HOST'] == 'pulsefeed.dev' ): ?>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/pulsefeed.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/message.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.stream.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.search.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.frame.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.page.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/template.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/design.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/queue.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/cookie.js?<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/lib/html_decode.js?<?php echo time(); ?>"></script>
<?php else: ?>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/compiled.js"></script>
<?php endif; ?>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/lib/jquery.cookie.js"></script>
<?php if( $this->get( 'externalHeader' ) ): ?>
	<!--frame funcs/buster-buster-->
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/frame.js?<?php echo $mod_user->session_permission( 'Debug' ) ? time() : ''; ?>"></script>
<?php endif; ?>
<?php if( $mod_user->session_permission( 'Admin' ) ): ?>
	<!--admin-->
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/admin.js?<?php echo time(); ?>"></script>
<?php endif; ?>
<?php endif; ?>

<?php if( $_SERVER['HTTP_HOST'] == 'pulsefeed.com' and !$mod_user->session_permission( 'Debug' ) ):?>
<!--google analytics-->
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-20248346-10']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<?php endif; ?>

<!--
	so long, and thanks for all the fish
-->

</body>
</html>