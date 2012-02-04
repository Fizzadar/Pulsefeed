<?php
	global $mod_user;
?>

<div class="wrap" id="home">
	<div class="main">
		<div id="home-info">
			<?php if( $mod_user->session_login() ): ?>
				<h3>Hi, <?php echo $mod_user->session_username(); ?></h3>
				<h1><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid() . '/' . $mod_user->session_username(); ?>">view your news stream &rarr;</a></h1>
			<?php else: ?>
				<h3>better reading</h3>
				<h1>Pulsefeed brings together all your favorite news-sites, blogs &amp; feeds into one simple stream <a href="<?php echo $c_config['root']; ?>/public?show">show me &rarr;</a></h1>
			<?php endif; ?>
		</div><!--end home-info-->

		<div class="home-boxes">
			<div>
				<h2>Combine</h2>
				<p>Bring all your news sources into one, easy to read stream</p>
			</div>
			<div>
				<h2>Filter</h2>
				<p>Pulsefeed learns what you like and suggests new sources</p>
			</div>
			<div>
				<h2>Read</h2>
				<p>Read, collect &amp; recommend articles with your friends</p>
			</div>
		</div><!--end home-boxes-->
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left noborder">
			
		</div><!--end left-->

		<div class="right">
			<div class="biglinks home">
				<?php if( $mod_user->session_login() ): ?>
				<a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid() . '/' . $mod_user->session_username(); ?>" class="biglink">
					<span>View your news stream &rarr;</span>
					all the latest updates
				</a>
				<?php else: ?>
				<a href="<?php echo $c_config['root']; ?>/login" class="biglink">
					<span>Sign in to Pulsefeed &rarr;</span>
					using facebook, twitter, google or any other openid
				</a>
				<?php endif; ?>
			</div>
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->