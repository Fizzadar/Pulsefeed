<?php
	$sections = array(
		'Streams',
		'Accounts',
		'Topics',
		'Collections',
		'Sources'
	);
?>
<div id="sidebars">
	<div class="wrap">
		<div class="left">
			<ul>
				<li class="title">Help</li>
				<li><?php if( !$this->get( 'helpPage' ) ): ?>Home &rarr;<?php else: ?><a href="<?php echo $c_config['root']; ?>/help">Home</a><?php endif; ?></li>
				<?php foreach( $sections as $section ): ?>
				<li>
					<?php if( $this->get( 'helpPage' ) == $section ): ?>
						<?php echo $section; ?> &rarr;
					<?php else: ?>
						<a href="<?php echo $c_config['root']; ?>/help/<?php echo strtolower( $section ); ?>"><?php echo $section; ?></a>
					<?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>

			<ul>
				<li class="title">More</li>
				<li><a href="<?php echo $c_config['root']; ?>/about">About Pulsefeed</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/suggest">Make a Suggestion</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/contact">Contact Us</a></li>
			</ul>
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->