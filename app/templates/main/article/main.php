<?php
	global $mod_user, $mod_cookie;

	if( $this->get( 'unread' ) ):
?>
<?php
	endif;
?>
	<div id="header">
		<div class="wrap">
			<div class="left">
				<a href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) : $c_config['root']; ?>">
					&larr; <?php echo $mod_user->session_userid() ? 'back to stream' : 'back home'; ?>
				</a>
			</div>

			<h1><?php echo $this->content['article']['title']; ?></h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main" id="stream">
			<div class="item article articlepage level_1">
				<span class="content">
					<?php echo $this->content['article']['content']; ?>
				</span>
				<?php if( $this->content['article']['not_full'] ): ?>
					<p><strong>Note:</strong> this article is not in full (<a class="edit" href="#">why?</a>), <a href="<?php echo $c_config['root']; ?>/article/<?php echo $this->content['article']['id']; ?>/original"><strong>please read the full article here</strong> &rarr;</a></p>
				<?php else: ?>
					<p><small><strong>Note:</strong> this article <em>may</em> not be complete (<a class="edit" href="#">why?</a>), <a href="<?php echo $c_config['root']; ?>/article/<?php echo $this->content['article']['id']; ?>/original">full article here &rarr;</a></small></p>
				<?php endif; ?>
			</div>

		</div><!--end main-->
	</div><!--end content-->

	<div id="sidebars">
		<div class="wrap">

			<div class="left" id="leftbar">
				<ul>
					<li id="sidebarback" style="display:none;">
						<a href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) : $c_config['root']; ?>">
							&larr; back
						</a>
					</li>

					<li class="title">Article Info</li>
					<li>
						<span class="type shown">date</span><br />
						<?php echo date( 'jS M Y', $this->content['article']['time'] ); ?>
					</li>
					<li>
						<span class="type shown">source</span><br />
						<a href="<?php echo $c_config['root']; ?>/source/<?php echo $this->content['article']['site_id']; ?>">
							<img src="http://www.google.com/s2/favicons?domain=<?php echo $this->content['article']['site_domain']; ?>" class="favicon" />
							<?php echo $this->content['article']['site_title']; ?>
						</a>
					</li>
				</ul>

				<ul>
					<li class="title">Share Article</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/share/facebook.png" alt="" />
							Facebook
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/share/twitter.png" alt="" />
							Twitter
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/share/delicious.png" alt="" />
							Delicious
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/share/stumbleupon.png" alt="" />
							StumbleUpon
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/share/linkedin.png" alt="" />
							LinkedIn
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/share/digg.png" alt="" />
							Digg
						</a>
					</li>
				</ul>
			</div><!--end left-->

			<div class="right">
				<div class="biglinks">
					<a href="<?php echo $c_config['root']; ?>/article/<?php echo $this->content['article']['id']; ?>/original" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/original.png" alt="" /> View original article &rarr;</span>
						<?php echo $this->content['article']['trim_url']; ?>
					</a>

					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/recommend.png" alt="" /> Recommend this article</span>
						<?php echo $this->content['article']['recommendations']; ?> already have
					</a>

					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/collect.png" alt="" /> Add to collection</span>
						Keep this article archived for later
					</a>
				</div><!--end biglinks-->
				
				<h4>Share Stats</h4>
				<ul class="stats">
					<li>Facebook: <span><?php echo $this->content['article']['facebook_shares']; ?></span></li>
					<li>Twitter: <span><?php echo $this->content['article']['twitter_links']; ?></span></li>
					<li>Delicious: <span><?php echo $this->content['article']['delicious_saves']; ?></span></li>
					<li>Diggs: <span><?php echo $this->content['article']['digg_diggs']; ?></span></li>
				</ul>
			</div><!--end right-->

		</div><!--end wrap-->
	</div><!--end sidebars-->