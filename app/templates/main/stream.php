<?php 
	/*
		file: app/templates/main/stream.php
		desc: stream template for main design
	*/
	
	global $mod_data, $mod_user, $mod_cookie, $mod_token;

	//work out if even cols or not
	$evencols = false;
	if( in_array( $this->get( 'title' ), array( 'unread', 'source', 'newest', 'discover' ) ) )	$evencols = true;
?>

<?php if( !$this->get( 'mainOnly' ) ): ?>
	<div id="header">
		<div class="wrap">
			<div class="left">
				<?php if( in_array( $this->get( 'title' ), array( 'hybrid', 'unread', 'popular', 'newest' ) ) and $mod_user->session_login() and $mod_user->session_userid() == $this->get( 'userid' ) ): ?>
					<a href="<?php echo $c_config['root']; ?>/sources" class="button" onclick="$( '#add_source' ).slideToggle(); return false;">+ add sources</a>
				<?php endif; ?>

				<?php if( $this->get( 'title' ) == 'public' and !$mod_user->session_login() ): ?>
					<a href="#" class="button" onclick="$( '#tour' ).fadeIn( 500 ); return false;">Pulsefeed Guide &rarr;</a>
				<?php endif; ?>

				<?php if( $this->content['title'] == 'source' and $mod_user->session_login() and $mod_user->session_permission( 'Subscribe' ) ): ?>
					<?php if( $this->get( 'subscribed' ) ): ?>
						<form action="<?php echo $c_config['root']; ?>/process/unsubscribe" method="post" id="subunsub">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
							<input type="submit" value="Unsubscibe" class="button" />
						</form>
					<?php else: ?>
						<form action="<?php echo $c_config['root']; ?>/process/subscribe" method="post" id="subunsub">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
							<input type="submit" value="+ Subscribe" class="button" />
						</form>
					<?php endif; ?>
				<?php elseif( $this->get( 'userid' ) != $mod_user->session_userid() and $mod_user->session_permission( 'Follow' ) ): ?>
					<?php if( $this->get( 'following' ) ): ?>
						<form action="<?php echo $c_config['root']; ?>/process/unfollow" method="post" id="subunsub">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="user_id" value="<?php echo $this->get( 'userid' ); ?>" />
							<input type="submit" value="UnFollow <?php echo $this->get( 'username' ); ?>" class="button" />
						</form>
					<?php else: ?>
						<form action="<?php echo $c_config['root']; ?>/process/follow" method="post" id="subunsub">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="user_id" value="<?php echo $this->get( 'userid' ); ?>" />
							<input type="submit" value="+ Follow <?php echo $this->get( 'username' ); ?>" class="button" />
						</form>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<h1><?php echo $this->get( 'pageTitle' ); ?></h1>
		</div><!--end wrap-->
	</div><!--end header-->
<?php endif; ?>

	<div class="wrap" id="content">
		<div class="main<?php echo $evencols ? ' evencol' : ''; ?>" id="stream">
			<?php if( $this->get( 'userid' ) == $mod_user->session_userid() and $this->get( 'title' ) != 'public' ): ?>
				<?php if( $mod_user->session_permission( 'Subscribe' ) and count( $this->content['stream']['col1'] ) == 0 ): ?>
				<div class="block">
					<h2>To fill your stream, you need to add some sources!</h2>
					<a class="greenbutton" href="<?php echo $c_config['root']; ?>/sources">Add Sources &rarr;</a>
				</div>
				<?php elseif( !$mod_user->session_permission( 'Subscribe' ) ): ?>
				<div class="block">
					<h2>You need to be in the Alpha to start filling your stream!</h2>
					<a class="greenbutton" href="<?php echo $c_config['root']; ?>/invite">Enter Invite Code &rarr;</a><br />
					<p>In the meantime, check out the <a href="<?php echo $c_config['root']; ?>/public">public stream</a>.</p>
				</div>
				<?php endif; ?>
			<?php endif; ?>

			<div id="add_source" class="hidden">
				<span class="edit">add sources / <a href="#" onclick="$( '#add_source' ).slideToggle(); return false;">close</a></span>
				<a href="<?php echo $c_config['root']; ?>/sources/me" class="morelink">Manage Sources</a>
				<a href="<?php echo $c_config['root']; ?>/sources" class="widelink">Browse Sources<span>Browse the directory of sources</span></a>
				<a href="<?php echo $c_config['root']; ?>/sources/add" class="widelink right">Add Directly<span>Enter a website / feed url</span></a>
			</div><!--end add_source-->
			
			<div class="col col3">
				<?php if( !$evencols ): ?>
					<span class="edit">upcoming articles<?php echo ( $this->get( 'nextOffset' ) > 1 ) ? ', page ' . ( $this->get( 'nextOffset' ) ) : ''; ?></span>
				<?php endif; ?>
				<?php
					foreach( $this->content['stream']['col3'] as $k => $item ):
						item_template( $item );
					endforeach;
				?>
			</div><!--end col3-->


			<div class="feature" style="display:none;">
				<span class="edit">trending topics</span>
				<h1><a href="#">Facebook</a> + <a href="#">Timeline</a></h1>
				<div class="left">
					<img class="thumb" src="http://pulsefeed.com/data/thumbs/half/7b074145a56ec3ff9108159636a8fd219f2d3dab.jpg" />
					<h2><a href="#">Facebook timeline roll out to all users</a></h2>
					<p>Image via Wikipedia Forget the Marlboro Reds and whiskey, what you’re developing the biggest dependency on might not be chemical at all: it’s social media. According to a study from Chicago University, texting and checking Facebook and Twitter come in just below sex and sleep on impossible to resist urges. Subjects 18-85 were given blackberries and sent out into the...</p>
					<div class="meta">
						<a href="#"><img src="http://www.google.com/s2/favicons?domain=fastcompany.com" /> Fast Company</a> &rarr; 2 hours ago
					</div>
				</div>
				<ul>
					<li>
						<span class="title"><a href="#">Facebook timeline roll out to all users</a></span>
						<span class="desc">Image via Wikipedia Forget the Marlboro Reds and whiskey</span>
						<span class="meta"><a href="#"><img src="http://www.google.com/s2/favicons?domain=fastcompany.com" /> Fast Company</a> &rarr; 1 hour ago</span>
					</li>
					<li>
						<span class="title"><a href="#">Facebook timeline roll out to all users</a></span>
						<span class="desc">Image via Wikipedia Forget the Marlboro Reds and whiskey, what you’re</span>
						<span class="meta"><a href="#"><img src="http://www.google.com/s2/favicons?domain=google.com" /> Fast Company</a> &rarr; 24 seconds ago</span>
					</li>
					<li>
						<span class="title"><a href="#">Facebook timeline roll out to all users, riot ensues</a></span>
						<span class="desc">Image via Wikipedia Forget the Marlboro Reds and whiskey, what you’re</span>
						<span class="meta"><a href="#"><img src="http://www.google.com/s2/favicons?domain=techcrunch.com" /> Fast Company</a> &rarr; 2 days ago</span>
					</li>
				</ul>
			</div>


			<div class="feature edit"><span class="edit">
			<?php
				if( count( $this->content['stream'] ) < 1 ):
					echo 'Oh no!';
				else:
					switch( $this->content['title'] ):
						case 'hybrid':
						case 'public':
						case 'popular':
							echo 'popular articles';
							break;
						case 'user':
						case 'newest':
						case 'source':
						case 'unread':
							echo 'latest articles';
							break;
					endswitch;
					echo ( $this->get( 'nextOffset' ) > 1 ) ? ', page ' . ( $this->get( 'nextOffset' ) ) : '';
				endif;
			?>
			</span></div>

			<div class="col col1">
				<?php
					foreach( $this->content['stream']['col1'] as $k => $item ):
						if( $evencols )
							item_template( $item );
						elseif( $k < 2 )
							item_template( $item, 'h1' );
						elseif( $k < 5 )
							item_template( $item, 'h2' );
						else
							item_template( $item );
					endforeach;
				?>
			</div><!--end col1-->


			<div class="col col2">
				<?php
					foreach( $this->content['stream']['col2'] as $k => $item ):
						if( $evencols )
							item_template( $item );
						elseif( $k < 1 )
							item_template( $item, 'h1' );
						elseif( $k < 4 )
							item_template( $item, 'h2' );
						else
							item_template( $item );
					endforeach;
				?>
			</div><!--end col2-->

			<?php
				//loop our items (layers within the stream)
				foreach( $this->content['stream'] as $key => $item ):
				endforeach;

				//empty stream
				if( count( $this->content['stream'] ) < 1 and $mod_user->session_login() and !$this->get( 'sources' ) ):
			?>
				<div class="item article level_1">
					<span class="content">
					<h2>You have no sources!</h2>
					<p>Add some sources and articles will start to appear hear from each of them. The more sources you subscribe to, the better content you'll see!</p>
					<p><a href="<?php echo $c_config['root']; ?>/sources" class="greenbutton">Add sources &#187;</a></p>
					</span>
				</div>
			<?php elseif( count( $this->content['stream'] ) < 1 ): ?>
				<div class="item article level_1">
					<span class="content">
					<h2>There are no more articles :(</h2>
					<p>We couldn't find any more articles for this stream.</p>
					</span>
				</div>
			<?php endif; ?>

			<a class="morelink" href="?offset=<?php echo $this->get( 'nextOffset' ); ?>">load more articles &darr;</a>
		</div><!--end main-->
	</div><!--end content-->

<?php if( !$this->get( 'mainOnly' ) ): ?>
	<div id="sidebars">
		<div class="wrap">
			<div class="left" id="leftbar">
				<?php if( $mod_user->session_login() and $this->get( 'userid' ) != $mod_user->session_userid() ): ?>
					<ul>
						<li><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>">&larr; my streams</a></li>
					</ul>
				<?php endif; ?>

				<ul>
					<li class="title">Streams <a href="#" class="edit">&larr; what?</a></li>
					<?php if( is_numeric( $this->get( 'userid' ) ) ): ?>
						<li>
							<?php if( $this->content['title'] == 'hybrid' ): ?>
							Hybrid &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>">Hybrid</a>
							<?php endif; ?>
						</li>
						<li>
							<?php if( $this->content['title'] == 'unread' ): ?>
							Unread &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/unread">Unread</a>
							<?php endif; ?>
						</li>
						<li>Discover <span class="type">coming soon</span></li>
						<li>
							<?php if( $this->content['title'] == 'popular' ): ?>
							Popular &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/popular">Popular</a>
							<?php endif; ?>
						</li>
						<li>
							<?php if( $this->content['title'] == 'newest' ): ?>
							Newest &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/newest">Newest</a>
							<?php endif; ?>
						</li>
					<?php endif; ?>

					<li>
						<?php if( $this->get( 'title' ) == 'public' ): ?>
							All/Public &rarr;
						<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/public">All/Public</a>
						<?php endif; ?>
					</li>
				</ul>

				<?php if( $mod_user->session_login() ): ?>
					<ul>
						<li class="title">Collections <a href="#" class="edit">edit</a></li>
						<li>
							<a href="#">Queenstown</a> <span class="type">1 article</span>
						</li>
						<li><a href="#">Web Design</a> <span class="type">5 articles</span></li>
					</ul>
				<?php endif; ?>

				<?php if( $this->get( 'title' ) != 'public' and is_numeric( $this->get( 'userid' ) ) ): ?>
					<ul class="sources">
						<li class="title">Sources<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="#" class="edit">edit</a>' : ''; ?></li>
						<?php if( $this->get( 'sources' ) ): ?>
						<?php foreach( $this->get( 'sources' ) as $source ): ?>
							<li class="source<?php echo $source['id'] == $this->get( 'source_id' ) ? ' active': ''; ?>">
								<a href="<?php echo $c_config['root']; ?>/source/<?php echo $source['id']; ?>" class="tip">
									<span><?php echo $source['source_title']; ?><span></span></span>
									<img src="http://www.google.com/s2/favicons?domain=<?php echo $source['source_domain']; ?>" />
								</a>
							</li>
						<?php endforeach; ?>
						<?php elseif( $mod_user->session_login() and $this->get( 'userid' ) == $mod_user->session_userid() ): ?>
							<li><a href="#">Add Sources &#187;</a></li>
						<?php else: ?>
							<li><?php echo $this->get( 'username' ); ?> has no sources</li>
						<?php endif; ?>
					</ul>

					<ul>
						<li class="title">Users<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="#" class="edit">edit</a>' : ''; ?></li>
						<?php if( $this->get( 'followings' ) ): ?>
						<?php foreach( $this->get( 'followings' ) as $follow ): ?>
							<li class="source">
								<a href="<?php echo $c_config['root']; ?>/user/<?php echo $follow['id']; ?>" class="tip">
									<span><?php echo $follow['name']; ?><span></span></span>
									<img src="<?php echo !empty( $follow['avatar_url'] ) ? $follow['avatar_url'] : $c_config['root'] . '/inc/img/icons/user.png'; ?>" alt="<?php echo $follow['name']; ?>" />
								</a>
							</li>
						<?php endforeach; ?>
						<?php elseif( $mod_user->session_login() and $this->get( 'userid' ) == $mod_user->session_userid() ): ?>
							<li><a href="#">Add Users &#187;</a></li>
						<?php else: ?>
							<li><?php echo $this->get( 'username' ); ?> follows no users</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div><!--end left-->

			<div class="right">
				<div id="recommendations">
				<?php foreach( $this->get( 'recommends' ) as $recommend ): ?>
					<div>
						<a href="<?php echo $c_config['root']; ?>/source/<?php echo $recommend['source_id']; ?>" class="tip"><span><?php echo $recommend['site_title']; ?><span></span></span><img src="http://www.google.com/s2/favicons?domain=<?php echo $recommend['source_domain']; ?>" alt="" /></a>
						<a href="<?php echo $c_config['root']; ?>/article/<?php echo $recommend['id']; ?>"><strong><?php echo $recommend['title']; ?></strong></a>
						<span class="meta">
							was liked by <a href="<?php echo $c_config['root']; ?>/user/<?php echo $recommend['user_id']; ?>"><?php echo $recommend['user_name']; ?></a>
							<span class="time"> - <?php echo $recommend['time_ago']; ?></span>
						</span>
					</div>
				<?php endforeach; ?>
				</div>

				<img src="<?php echo $c_config['root']; ?>/inc/img/ads/234x60.gif" alt="" />
				
				<div class="biglinks">
					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/settings.png" alt="" /> Your Pulsefeed Settings</span>
						customize your pulsefeed setup
					</a>
					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/phone.png" alt="" /> Pusefeed on your Mobile</span>
						stay updated while on the move
					</a>
					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/browser.png" alt="" /> Browser Addons</span>
						install pulsefeed on your web-browser
					</a>
					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/suggestion.png" alt="" /> Make a Suggestion</span>
						how would you improve pulsefeed?
					</a>
					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/api.png" alt="" /> Pulsefeed API</span>
						develop services using our api
					</a>
				</div><!--end biglinks-->
			</div><!--end right-->

			<div class="fix">
				<a href="#">
					&uarr; top
				</a>
			</div>
		</div><!--end wrap-->
	</div><!--end sidebars-->

<?php endif; ?>

<div id="tour" style="<?php echo isset( $_GET['welcome'] ) ? ' display: block;' : ''; ?>">
	<div class="wrap">
		<h1>A Quick Guide to Pulsefeed...</h1>
		<p>Welcome to Pulsefeed, this is a quick 3-point guide to the layout/interface to get you started :)</p>
		<div class="left block">
			<h2>1: Left Bar</h2>
			&larr; here are all your <strong>streams</strong>, <strong>collections</strong> you create, <strong>sources</strong> you subscribe to &amp; <strong>users</strong> you follow<br /><br />
			When you visit another users page, you will see their streams, collections, sources &amp; users; as well as a link back to your page
		</div>
		<div class="middle block">
			<h2>2: Stream</h2>
			&darr; this is the article stream, where the magic happens and the best articles (for you) are shown
		</div>
		<div class="right block">
			<h2>3: Right Bar</h2>
			&rarr; articles recommended by other users appear here, as well as useful links around the site
		</div>
		<a href="#" class="button" onclick="$( '#tour' ).fadeOut( 500 ); return false;">Continue to Pulsefeed &rarr;</a>
	</div><!--end wrap-->
</div><!--end tour-->



<?php
	function item_template( $item, $header = 'h3' ) {
		global $mod_user, $mod_token, $c_config;
		$long = true;
?>
				<div class="item">
					<<?php echo $header; ?>><a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>"><?php echo $item['title']; ?></a></<?php echo $header; ?>>
					<?php if( !empty( $item['image_half'] ) ): $long = false; ?>
						<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>">
							<img class="thumb" src="<?php echo $c_config['root'] . '/' . $item['image_half']; ?>" alt="<?php echo $item['title']; ?>" />
						</a>
					<?php elseif( !empty( $item['image_third'] ) ): $long = false; ?>
						<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>">
							<img class="thumb" src="<?php echo $c_config['root'] . '/' . $item['image_third']; ?>" alt="<?php echo $item['title']; ?>" />
						</a>
					<?php endif; ?>
					<p><?php echo $long ? $item['short_description'] : $item['shorter_description']; ?> <a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>">read article &rarr;</a></p>
					<div class="meta">
						<a href="<?php echo $c_config['root'] . '/source/' . $item['source_id']; ?>" class="tip"><span><?php echo $item['source_title']; ?><span></span></span><img src="http://www.google.com/s2/favicons?domain=<?php echo $item['source_domain']; ?>" /></a>
					<?php if( $mod_user->session_login() ): ?>
						<?php
							if( !$item['expired'] and $item['subscribed'] ):
								echo $item['unread'] ?
									'<form action="' . $c_config['root'] . '/process/article-read" method="post">
										<input type="hidden" name="article_id" value="' . $item['id'] . '" />
										<input type="hidden" name="mod_token" value="' . $mod_token . '" />
										<input type="submit" value="Hide" />
									</form> - ' : 
									'';
							elseif( !$item['expired'] ):
							endif;
						?>
						<?php if( $mod_user->session_permission( 'Collect' ) ): ?>
							<a href="#">Collect</a> - 
						<?php endif; ?>
						<?php if( $mod_user->session_permission( 'Recommend' ) ): ?>
							<form action="<?php echo $c_config['root']; ?>/process/article-<?php echo $item['recommended'] ? 'unrecommend' : 'recommend'; ?>" method="post">
								<input type="hidden" name="article_id" value="<?php echo $item['id']; ?>" />
								<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
								<input type="submit" value="<?php echo $item['recommended'] ? 'Unlike' : 'Like'; ?>" />
							</form> <span class="likes">(<?php echo $item['recommendations']; ?>)</span>
						<?php endif; ?>
					<?php endif; ?>
					<span class="time"> - <?php echo $item['time_ago']; ?></span>
					</div>
				</div><!--end item-->
<?php } ?>