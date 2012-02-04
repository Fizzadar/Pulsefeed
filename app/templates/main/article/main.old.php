<?php
	global $mod_user, $mod_cookie, $mod_token;
	if( !isset( $_GET['f'] ) ) $_GET['f'] = 0;

	if( $this->get( 'unread' ) ):
?>
<?php
	endif;
?>
	<iframe style="border: none; position: absolute; top: 35px; left: 0; width: 100%; height: 100%; background: #FFF; z-index: 9;" onload="" src="<?php echo $this->content['article']['url']; ?>"></iframe>
	<div id="header">
		<div class="wrap">
			<div class="left">
				<a href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) . '#item_' . $_GET['f'] : $c_config['root']; ?>">
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
				<?php endif; ?>
			</div>

		</div><!--end main-->
	</div><!--end content-->

	<div id="sidebars">
		<div class="wrap">

			<div class="left" id="leftbar">
				<ul>
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

				<ul>
					<li class="title">Article Tags</li>
					<li><a href="#">#tech</a></li>
					<li><a href="#">#mobile</a></li>
					<li>
						<form action="" method="post">
							<input type="text" value="add tags..." onclick="if( this.value == 'add tags...' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'add tags...'; }" />
						</form>
					</li>
				</ul>
			</div><!--end left-->

			<div class="right">
				<div class="biglinks">
					<a href="<?php echo $this->content['article']['url']; ?>" target="_blank" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/original.png" alt="" /> View original article &rarr;</span>
						<?php echo $this->content['article']['trim_url']; ?>
					</a>

					<form action="<?php echo $c_config['root']; ?>/?process=article-recommend" method="post">
						<input type="hidden" name="article_id" value="<?php echo $this->content['article']['id']; ?>" />
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<button type="submit" class="biglink">
							<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/recommend.png" alt="" /> <?php echo $this->content['article']['recommendations'] == NULL ? 'Recommend this article' : 'You recommend this article'; ?></span>
							<?php echo $this->content['article']['recommended'] == NULL ? $this->content['article']['recommendations'] . ' have recommended this article' : 'You and ' . ( $this->content['article']['recommendations'] - 1 ) . ' others recommend this'; ?>
						</button>
					</form>

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

			<div class="fix">
				<a href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) . '#item_' . $_GET['f'] : $c_config['root']; ?>">
					&larr; <?php echo $mod_user->session_userid() ? 'back to stream' : 'back home'; ?>
				</a>
			</div>
		</div><!--end wrap-->
	</div><!--end sidebars-->