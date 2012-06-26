<?php
	global $mod_user, $mod_message, $mod_cookie, $mod_token;
	$this->load( 'core/head' );
?>

<body>
	<div id="top" class="<?php echo $this->get( 'externalHeader' ) ? 'external' : ''; ?>">
		<div class="wrap">
			<div id="devmessage"><a target="_blank" href="http://blog.pulsefeed.com/category/updates">[!] dev status &#187;</a></div>

			<?php if( $this->get( 'externalHeader' ) ): ?>
				<!--specific css -->
				<link rel="stylesheet" type="text/css" href="<?php echo $c_config['root']; ?>/inc/css/external.css" media="all" />
				<h3 class="external back">
					<span><small>&#171;</small> Pulsefeed</span>
					<a href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) : $c_config['root']; ?>"><small>&#171;</small> Pulsefeed</a>
				</h3>

				<?php if( !$this->content['article']['xframe'] or $this->content['article']['type'] != 'text' ): ?>
					<img class="loading_icon" src="<?php echo $c_config['root']; ?>/inc/img/icons/loader_top.gif" alt="" />
				<?php endif; ?>

				<ul id="external">
					<li>
						<form action="<?php echo $c_config['root']; ?>/process/article-share" method="post" class="share_form_external">
							<input type="hidden" name="article_id" value="<?php echo $this->content['article']['id']; ?>" />
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="twitter_links" value="<?php echo number_format( $this->content['article']['twitter_links'] ); ?>" />
							<input type="hidden" name="facebook_shares" value="<?php echo number_format( $this->content['article']['facebook_shares'] ); ?>" />
							<input type="hidden" name="article_title" value="<?php echo urlencode( $this->content['article']['title'] ); ?>" />
							<input type="hidden" name="article_url" value="<?php echo urlencode( $this->content['article']['end_url'] ); ?>" />
							<button type="submit">
								<img src="<?php echo $c_config['root']; ?>/inc/img/icons/header/share.png" alt="" />
								<span>Share</span>
							</button>
							<span class="share"></span>
						</form>
					</li>

					<?php if( $mod_user->session_permission( 'Collect' ) ): ?>
					<!--collect-->
					<li>
						<a href="<?php echo $c_config['root']; ?>/article/<?php echo $this->content['article']['id']; ?>/collect" class="collect_button_external" data-articleid="<?php echo $this->content['article']['id']; ?>">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/header/collect.png" alt="" />
							Collect
						</a>
					</li>
					<?php endif; ?>

					<?php if( $mod_user->session_permission( 'AddTag' ) ): ?>
					<!--tag-->
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/header/tag.png" alt="" />
							Tag
						</a>
					</li>
					<?php endif; ?>

					<?php if( !$mod_user->session_login() ): ?>
					<!--login-->
					<li>
						<a href="<?php echo $c_config['root']; ?>/login">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/header/login.png" alt="" />
							Login
						</a>
					</li>
					<?php endif; ?>

					<!--original-->
					<li>
						<a target="_blank" href="<?php echo $this->content['article']['end_url']; ?>">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/header/original.png" alt="" />
							Original &rarr;
						</a>
					</li>
				</ul>

			<?php else: ?>
				<h3<?php echo !$this->get( 'stream' ) ? ' class="back"' : ''; ?>>
					<span><?php echo !$this->get( 'stream' ) ? '<small>&#171;</small> ' : ''; ?>Pulsefeed</span>
					<a href="<?php echo $c_config['root']; ?>"><?php echo !$this->get( 'stream' ) ? '<small>&#171;</small> ' : ''; ?>Pulsefeed</a>
				</h3>

				<form id="search" action="<?php echo $c_config['root']; ?>/search" method="GET">
					<input type="text" id="q" name="q" placeholder="Search Pulsefeed..." />
					<input type="submit" id="submit" value="Search &rarr;" />

					<ul id="search_results">
					</ul><!--end search_results-->
				</form>
			<?php endif; ?>

			<ul id="account"<?php echo $mod_user->session_login() ? '' : ' class="nologin"'; ?>>
			<?php if( $mod_user->session_login() ): ?>
				<ul>
					<li class="title">Settings</li>
					<li><a class="ajax" href="<?php echo $c_config['root']; ?>/settings">Profile Setup</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/settings/accounts">Accounts</a></li>

					<li class="title">Subscriptions</li>
					<li><a href="<?php echo $c_config['root']; ?>/topics/me">Topics</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/collections/me">Collections</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/websites/me">Websites</a></li>

					<li class="title">Other Bits</li>
					<li><a href="<?php echo $c_config['root']; ?>/help">Help</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/stats">Stats</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/process/logout">Logout</a></li>

					<?php if( $mod_user->session_permission( 'Admin' ) ): ?>
						<li class="title">Top Secret Zone</li>
						<li><a href="<?php echo $c_config['root']; ?>/admin">Home</a></li>
						<li><a href="<?php echo $c_config['root']; ?>/admin/permissions">Permissions</a></li>
						<li><a href="<?php echo $c_config['root']; ?>/admin/topics">Topics</a></li>
						<li><a href="<?php echo $c_config['root']; ?>/admin/memcache">Memcaches</a></li>
						<li><a href="<?php echo $c_config['root']; ?>/admin/users">Users</a></li>
					<?php endif; ?>
				</ul>
				<li class="top">
					<a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>"><strong>latest updates</strong></a>
					<span><?php echo $mod_user->session_username(); ?> &darr;</span>
				</li>
			<?php else : ?>
				<li class="top">
					<span><a href="<?php echo $c_config['root']; ?>/login"><strong>Login / Register</strong></a></span>
				</li>
			<?php endif; ?>
			</ul>
		</div><!--end wrap-->
	</div><!--end top-->

	<div id="messages">
	<?php foreach( $mod_message->get() as $message ): ?>
		<div class="message <?php echo $message[1]; ?>">
			<div class="wrap">
				<?php echo $message[0]; ?>
			</div>
		</div>
	<?php endforeach; ?>
	</div><!--end messages-->

	<div id="ajaxbox">