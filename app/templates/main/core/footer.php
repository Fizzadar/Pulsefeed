	</div><!--end ajaxbox-->
	
	<div id="footer">
		<div class="wrap">
			<div class="inner">
				<div class="bottom">
					<div class="left">
						A <a href="http://fanaticaldev.com/" title="Fanatical Dev">Fanatical Dev</a> production<br /><a href="#"><strike>Copyright Info</strike></a> - <a href="#"><strike>Stats</strike></a> - <a href="#"><strike>Contact</strike></a> - v<?php echo PULSEFEED_VERSION; ?>
					</div><!--end left-->
					<div class="right">
						Icons: <a href="http://pc.de">PC.DE</a> - Colors: <a href="http://www.colourlovers.com/">ColorLovers</a><br />
						PHP: <a href="http://simplepie.org/">SimplePie</a>, <a href="http://net.tutsplus.com/tutorials/php/image-resizing-made-easy-with-php/">Resize</a> - JS: <a href="http://jquery.com/">jQuery</a>, <a href="#"><strike>full list &rarr;</strike></a>
					</div><!--end right-->
				</div><!--end bottom-->
			</div><!--end inner-->
		</div><!--end wrap-->
	</div><!--end footer-->

<?php if( $_SERVER['HTTP_HOST'] == 'pulsefeed.com' ):?>
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