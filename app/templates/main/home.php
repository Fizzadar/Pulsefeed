<?php
	global $mod_user;

	$welcomeTexts = array(
		'cut the crap',
		'trim the fat',
		'read the best',
	);
?>

<div class="wrap" id="home">
	<div class="main">
		<div id="home-info">
			<?php if( $mod_user->session_login() ): ?>
			<h3>Hi, <?php echo $mod_user->session_username(); ?></h3>
			<h1><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>">view your news stream &rarr;</a></h1>
			<?php else: ?>
			<h3><?php echo $welcomeTexts[mt_rand( 0, count( $welcomeTexts ) - 1 )]; ?></h3>
			<h1>Pulsefeed brings together all your favorite news-sites, blogs &amp; feeds into one simple stream <a href="<?php echo $c_config['root']; ?>/public?show">show me &rarr;</a></h1>
			<?php endif; ?>
		</div><!--end home-info-->

		<div class="home-boxes">
			<div>
				<h2>Combine</h2>
				<p>Bring all your news sources into one stream</p>
			</div>
			<div>
				<h2>Read</h2>
				<p>Find &amp; read the best articles, collect the best</p>
			</div>
			<div>
				<h2>Filter</h2>
				<p>Organize articles by popularity; cut out the 'crap'</p>
			</div>
		</div><!--end home-boxes-->

		<div class="home-boxes">
			<div>
				<h2>Collect</h2>
				<p>Group your favorite articles into collections</p>
			</div>
			<div>
				<h2>Stream</h2>
				<p>Build custom streams out of your sources</p>
			</div>
			<div>
				<h2>Recommend</h2>
				<p>Recommend articles to users who follow you</p>
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
				<a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>" class="biglink">
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