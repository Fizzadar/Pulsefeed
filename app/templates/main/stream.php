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

<script type="text/javascript">
	pulsefeed.stream = true;
	pulsefeed.streamType = '<?php echo $this->get( 'title' ); ?>';
	pulsefeed.streamOffset = <?php echo $this->get( 'nextOffset' ); ?>;
<?php if( $this->get( 'title' ) == 'source' ): ?>
	pulsefeed.streamSource = <?php echo $this->get( 'source_id' ); ?>;
<?php else: ?>
	pulsefeed.streamUser = <?php echo $this->get( 'userid' ) ? $this->get( 'userid' ) : -1; ?>;
<?php endif; ?>
</script> 

<?php if( !$this->get( 'mainOnly' ) ): ?>
	<div id="header">
		<div class="wrap">
			<div class="left">
				<?php if( in_array( $this->get( 'title' ), array( 'hybrid', 'unread', 'popular', 'newest' ) ) and $mod_user->session_login() and $mod_user->session_userid() == $this->get( 'userid' ) ): ?>
					<a href="<?php echo $c_config['root']; ?>/sources" class="button" onclick="$( '#add_source' ).slideToggle( 150 ); return false;">+ add sources</a>
				<?php endif; ?>

				<?php if( $this->get( 'title' ) == 'public' and !$mod_user->session_login() ): ?>
					<a href="#" class="button" onclick="$( '#tour' ).fadeIn( 500 ); return false;">Pulsefeed Guide &rarr;</a>
				<?php endif; ?>

				<?php if( $this->content['title'] == 'source' and $mod_user->session_login() and $mod_user->session_permission( 'Subscribe' ) ): ?>
					<?php if( $this->get( 'subscribed' ) ): ?>
						<form action="<?php echo $c_config['root']; ?>/process/unsubscribe" method="post" id="subunsub" class="source_subscribe">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
							<input type="submit" value="Unsubscibe" class="button" />
						</form>
					<?php else: ?>
						<form action="<?php echo $c_config['root']; ?>/process/subscribe" method="post" id="subunsub" class="source_subscribe">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
							<input type="submit" value="+ Subscribe" class="button" />
						</form>
					<?php endif; ?>
				<?php elseif( $this->get( 'userid' ) != $mod_user->session_userid() and $mod_user->session_permission( 'Follow' ) ): ?>
					<?php if( $this->get( 'following' ) ): ?>
						<form action="<?php echo $c_config['root']; ?>/process/unfollow" method="post" id="subunsub" class="user_follow">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="user_id" value="<?php echo $this->get( 'userid' ); ?>" />
							<input type="submit" value="UnFollow <?php echo $this->get( 'username' ); ?>" class="button" />
						</form>
					<?php else: ?>
						<form action="<?php echo $c_config['root']; ?>/process/follow" method="post" id="subunsub" class="user_follow">
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
			<?php if( $mod_cookie->get( 'ChangeUsernameMessage' ) == '1' ): ?>
				<div class="block">
					<p>We noticed you haven't yet changed your username! <a class="button" href="<?php echo $c_config['root']; ?>/settings">Change username &#187;</a></p>
				</div>
			<?php endif; ?>

			<?php if( $this->get( 'userid' ) == $mod_user->session_userid() and $this->get( 'title' ) != 'public' and $this->get( 'title' ) != 'source' ): ?>
				<?php if( $mod_user->session_permission( 'Subscribe' ) and count( $this->content['stream']['col1'] ) == 0 ): ?>
				<div class="block">
					<h2>To fill your stream, you need to add some (more) sources!</h2>
					<p>There's currently no articles in your stream, subscribe to more sources to increase the number of articles in your stream. We'll pick out the best ones for you.</p>
					<a class="greenbutton" href="<?php echo $c_config['root']; ?>/sources">Add Sources &#187;</a>
				</div>
				<?php elseif( !$mod_user->session_permission( 'Subscribe' ) ): ?>
				<div class="block">
					<h2>You need to be in the Alpha to start filling your stream!</h2>
					<a class="greenbutton" href="<?php echo $c_config['root']; ?>/invite">Enter Invite Code &#187;</a><br />
					<p>In the meantime, check out the <a href="<?php echo $c_config['root']; ?>/public">public stream</a>.</p>
				</div>
				<?php endif; ?>
			<?php elseif( count( $this->content['stream']['col1'] ) == 0 ): ?>
				<div class="block">
					<h2>This stream is empty</h2>
					<p>We're doing our best to fill it right now!</p>
				</div>
			<?php endif; ?>

			<div id="add_source" class="hidden">
				<span class="edit">add sources / <a href="#" onclick="$( '#add_source' ).slideToggle( 100 ); return false;">close</a></span>
				<a href="<?php echo $c_config['root']; ?>/sources" class="linkthird">
					<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/browse.png" alt="" />
					Browse Sources
					<span>Browse the directory of sources</span>
				</a>
				<a href="<?php echo $c_config['root']; ?>/sources/add" class="linkthird middle">
					<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/add.png" alt="" />Add Directly
					<span>Enter websites / feed urls</span>
				</a>
				<a href="<?php echo $c_config['root']; ?>/sources/me" class="linkthird">
					<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/manage.png" alt="" />Manage Sources
					<span>Manage your current sources</span>
				</a>
			</div><!--end add_source-->
			
			<div class="col col3">
				<?php if( !$evencols ): ?>
					<span class="edit">upcoming articles<?php echo ( $this->get( 'nextOffset' ) > 1 ) ? ', page ' . ( $this->get( 'nextOffset' ) ) : ''; ?></span>
				<?php endif; ?>
				<?php
					foreach( $this->content['stream']['col3'] as $k => $item ):
						item_template( $item, $this->get( 'userid' ) );
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
							item_template( $item, $this->get( 'userid' ) );
						elseif( $k < 2 )
							item_template( $item, $this->get( 'userid' ), 'h1' );
						elseif( $k < 5 )
							item_template( $item, $this->get( 'userid' ), 'h2' );
						else
							item_template( $item, $this->get( 'userid' ) );
					endforeach;
				?>
			</div><!--end col1-->


			<div class="col col2">
				<?php
					foreach( $this->content['stream']['col2'] as $k => $item ):
						if( $evencols )
							item_template( $item, $this->get( 'userid' ) );
						elseif( $k < 1 )
							item_template( $item, $this->get( 'userid' ), 'h1' );
						elseif( $k < 4 )
							item_template( $item, $this->get( 'userid' ), 'h2' );
						else
							item_template( $item, $this->get( 'userid' ) );
					endforeach;
				?>
			</div><!--end col2-->

			<?php
				//loop our items (layers within the stream)
				foreach( $this->content['stream'] as $key => $item ):
				endforeach;
			?>

			<a class="morelink stream_load_more" href="?offset=<?php echo $this->get( 'nextOffset' ); ?>">load more articles &darr;</a>
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

				<?php if( false and $mod_user->session_login() ): ?>
					<ul>
						<li class="title">Collections <a href="#" class="edit">edit</a></li>
						<li>
							<a href="#">Queenstown</a> <span class="type">1 article</span>
						</li>
						<li><a href="#">Web Design</a> <span class="type">5 articles</span></li>
					</ul>
				<?php endif; ?>

				<?php if( isset( $this->content['accounts'] ) and count( $this->content['accounts'] ) > 0 ): ?>
					<ul>
						<li class="title">Accounts <a href="<?php echo $c_config['root']; ?>/settings" class="edit">edit</a></li>
						<?php foreach( $this->content['accounts'] as $account ): ?>
							<?php if( $this->get( 'account_type' ) == $account['type'] ): ?>
								<li><?php echo ucfirst( $account['type'] ); ?> &rarr;</li>
							<?php else: ?>
								<li><a href="<?php echo $c_config['root']; ?>/account/<?php echo $account['type']; ?>"><?php echo ucfirst( $account['type'] ); ?></a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if( $this->get( 'userid' ) ): ?>
					<ul class="sources">
						<li class="title">Sources<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="' . $c_config['root'] . '/sources/me" class="edit">edit</a>' : ''; ?></li>
						<?php if( $this->get( 'sources' ) ): ?>
						<?php foreach( $this->get( 'sources' ) as $source ): ?>
							<li class="source<?php echo $source['id'] == $this->get( 'source_id' ) ? ' active': ''; ?>">
								<a href="<?php echo $c_config['root']; ?>/source/<?php echo $source['id']; ?>" class="tip">
									<span>
										<strong><?php echo $source['source_title']; ?></strong>
										<small>You are subscribed</small><span></span>
									</span>
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
									<span>
										<strong><?php echo $follow['name']; ?></strong>
										<small>You follow them</small>
										<span></span>
									</span>
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
				<img src="<?php echo $c_config['root']; ?>/inc/img/ads/234x60.gif" alt="" />

				<div class="biglinks">
					<?php if( $this->get( 'title' ) == 'source' ): ?>
						<a href="<?php echo $this->content['source']['site_url']; ?>" target="_blank" class="biglink">
							<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/original.png" alt="" /> View source &rarr;</span>
							view the original source website
						</a>
					<?php endif; ?>

					<a href="<?php echo $c_config['root']; ?>/settings" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/settings.png" alt="" /> Your Pulsefeed Settings</span>
						customize your pulsefeed setup
					</a>
					<a href="#" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/phone.png" alt="" /> Pulsefeed on your Mobile</span>
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
				<a href="#" class="top_link">
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
	function item_template( $item, $uid, $header = 'h3' ) {
		global $mod_user, $mod_token, $c_config;

		//work out if we have the source ref
		$source = false;
		foreach( $item['refs'] as $ref )
			if( $ref['source_type'] == 'source' )
				$source = true;

		$long = true;
?>
				<div class="item" id="article_<?php echo $item['id']; ?>">
					<<?php echo $header; ?>><a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link"><?php echo $item['title']; ?></a></<?php echo $header; ?>>
					<?php if( !empty( $item['image_half'] ) ): $long = false; ?>
						<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link">
							<img class="thumb" src="<?php echo $c_config['root'] . '/' . $item['image_half']; ?>" alt="<?php echo $item['title']; ?>" />
						</a>
					<?php elseif( !empty( $item['image_third'] ) ): $long = false; ?>
						<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link">
							<img class="thumb" src="<?php echo $c_config['root'] . '/' . $item['image_third']; ?>" alt="<?php echo $item['title']; ?>" />
						</a>
					<?php endif; ?>
					<p><?php echo $long ? $item['short_description'] : $item['shorter_description']; ?> <a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link">read article &rarr;</a></p>
					<div class="meta">
						<?php foreach( $item['refs'] as $ref ): ?>
							<a href="<?php echo $c_config['root']; ?>/<?php
								switch( $ref['source_type'] ):
									case 'source':
										echo 'source' . '/' . $ref['source_id'];
										break;
									case 'like':
										echo 'user' . '/' . $ref['source_id'];
										break;
									case 'facebook':
									case 'twitter':
										echo 'account/' . $ref['source_type'];
										break;
									default:
										echo '#';
								endswitch;
								?>" class="tip hover">
								<span>
									<strong><?php echo $ref['source_title']; ?></strong>
									<small><?php
										switch( $ref['source_type'] ):
											case 'source':
												echo 'You are subscribed';
												break;
											case 'twitter':
												echo 'You follow them';
												break;
											case 'facebook':
												echo 'You are subscribed';
												break;
											case 'like':
												echo 'You follow them';
												break;
											default:
												echo 'Unknown';
										endswitch;
									?></small>
									<span></span>
								</span>
								<img src="<?php
									switch( $ref['source_type'] ):
										case 'source':
											echo 'http://www.google.com/s2/favicons?domain=' . $ref['source_data']['domain'];
											break;
										case 'like':
										case 'facebook':
										case 'twitter':
											echo $c_config['root'] . '/inc/img/icons/share/' . $ref['source_type'] . '.png';
											break;
										default:
											echo $c_config['root'] . '/inc/img/icons/sidebar/original.png';
									endswitch;
								?>" />
							</a>
							<?php if( !$source and isset( $ref['origin_id'] ) and $ref['origin_id'] > 0 and isset( $ref['origin_title'] ) and isset( $ref['origin_data'] ) ): ?>
								<a href="<?php echo $c_config['root']; ?>/source/<?php echo $ref['origin_id']; ?>" class="tip hover">
									<span>
										<strong><?php echo $ref['origin_title']; ?></strong>
										<small>Original source</small>
										<span></span>
									</span>
									<img src="http://www.google.com/s2/favicons?domain=<?php echo $ref['origin_data']['domain']; ?>" />
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php if( $mod_user->session_login() ): ?>
						<?php
							if( isset( $item['unread'] ) and $item['unread'] == 1 ):
								echo
									'<form action="' . $c_config['root'] . '/process/article-read" method="post" class="hide_form">
										<input type="hidden" name="article_id" value="' . $item['id'] . '" />
										<input type="hidden" name="mod_token" value="' . $mod_token . '" />
										<input type="submit" value="Hide" />
									</form> - ';
							endif;
						?>
						<?php if( $mod_user->session_permission( 'Collect' ) ): ?>
							<span class="collect"><a href="#">Collect</a><!--<ul class="collections"><span class="tip"></span><li><a href="#">Collection 1</a></li><li><a href="#">Collection 2</a></li><li><form><input type="text" value="new collection..." /></form></li></ul>--></span> - 
						<?php endif; ?>
						<?php if( $mod_user->session_permission( 'Recommend' ) ): ?>
							<form action="<?php echo $c_config['root']; ?>/process/article-<?php echo $item['liked'] ? 'unlike' : 'like'; ?>" method="post" class="like_form">
								<input type="hidden" name="article_id" value="<?php echo $item['id']; ?>" />
								<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
								<input type="submit" value="<?php echo $item['liked'] ? 'Unlike' : 'Like'; ?>" /> <span class="likes">(<span><?php echo $item['likes']; ?></span>)</span>
							</form>
						<?php endif; ?>
					<?php endif; ?>
					<span class="time"> - <?php echo $item['time_ago']; ?></span>
					</div>
				</div><!--end item-->
<?php } ?>