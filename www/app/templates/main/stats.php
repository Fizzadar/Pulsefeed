<?php
?>

<div id="header">
	<div class="wrap">
		<div class="left noborder">
			
		</div>

		<h1>Stats</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main">
		<p>Some simple Pulsefeed stats, updated every 24h</p>

		<ul class="stats">
			<li><span>Articles:</span> <?php echo number_format( $this->content['stats']['articles'] ); ?></li>
			<li><span>Websites:</span> <?php echo number_format( $this->content['stats']['websites'] ); ?></li>
			<li><span>Topics:</span> <?php echo number_format( $this->content['stats']['topics'] ); ?></li>
			
		</ul>
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left noborder">
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->