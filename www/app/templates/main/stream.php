<?php 
	/*
		file: app/templates/main/stream.php
		desc: stream template for main design
	*/
	
	global $mod_data, $mod_user, $mod_cookie, $mod_token;

	//work out if even cols or not
	$evencols = true;
	if( in_array( $this->get( 'title' ), array( 'hybrid', 'popular', 'public' ) ) )	$evencols = false;
?>

<script type="text/javascript">
	pulsefeed.stream = true;
	pulsefeed.streamType = '<?php echo $this->get( 'title' ); ?>';
	pulsefeed.streamOffset = <?php echo $this->get( 'nextOffset' ); ?>;
<?php if( $this->get( 'title' ) == 'source' ): ?>
	pulsefeed.streamSource = <?php echo $this->get( 'source_id' ); ?>;
	pulsefeed.streamSubscribed = <?php echo $this->get( 'subscribed' ) ? 'true' : 'false'; ?>;
<?php elseif( $this->get( 'title' ) == 'account' ): ?>
	pulsefeed.streamAccount = '<?php echo $this->get( 'account_type' ); ?>';
<?php elseif( $this->get( 'title' ) == 'collection' ): ?>
	pulsefeed.streamCollection = '<?php echo $this->get( 'collection_id' ); ?>';
<?php else: ?>
	pulsefeed.streamUser = <?php echo $this->get( 'userid' ) ? $this->get( 'userid' ) : -1; ?>;
	pulsefeed.streamUsername = '<?php echo $this->get( 'username' ); ?>';
<?php endif; ?>
</script> 

<div id="header">
	<div class="wrap">
		<div class="left">
			<?php if( in_array( $this->get( 'title' ), array( 'hybrid', 'unread', 'popular', 'newest' ) ) and $mod_user->session_login() and $mod_user->session_userid() == $this->get( 'userid' ) ): ?>
				<a href="<?php echo $c_config['root']; ?>/sources" class="button green" onclick="$( '#add_source' ).slideToggle( 150 ); return false;">+ add sources</a>
			<?php endif; ?>

			<?php if( $this->get( 'title' ) == 'source' and $mod_user->session_login() and $mod_user->session_permission( 'Subscribe' ) ): ?>
				<?php if( $this->get( 'subscribed' ) ): ?>
					<form action="<?php echo $c_config['root']; ?>/process/unsubscribe" method="post" id="subunsub" class="source_subscribe">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
						<input type="submit" value="Unsubscibe" class="button red" />
					</form>
				<?php else: ?>
					<form action="<?php echo $c_config['root']; ?>/process/subscribe" method="post" id="subunsub" class="source_subscribe">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
						<input type="submit" value="+ Subscribe" class="button green" />
					</form>
				<?php endif; ?>
			<?php elseif( $this->get( 'title' ) != 'collection' and $this->get( 'userid' ) != $mod_user->session_userid() and $mod_user->session_permission( 'Follow' ) ): ?>
				<?php if( $this->get( 'following' ) ): ?>
					<form action="<?php echo $c_config['root']; ?>/process/unfollow" method="post" id="subunsub" class="user_follow">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="user_id" value="<?php echo $this->get( 'userid' ); ?>" />
						<input type="submit" value="UnFollow" class="button red" />
					</form>
				<?php else: ?>
					<form action="<?php echo $c_config['root']; ?>/process/follow" method="post" id="subunsub" class="user_follow">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="user_id" value="<?php echo $this->get( 'userid' ); ?>" />
						<input type="submit" value="+ Follow" class="button green" />
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div><!--end left-->

		<div class="right">
			<span>
				<a class="button green row tip mini down">script: on<span>turn javascript on / off<span></span></span></a>
				<a class="button green row tip mini down">images: on<span>hide images in the stream<span></span></span></a>
				<a class="button blue row tip mini down">columns: 3<span>switch between 2 &amp; 3 columns<span></span></span></a>
				<a class="button blue row tip fourth mini down">order: popular<span>switch between popular &amp; newest articles<span></span></span></a>
				<a class="button red row tip fith mini down">hide message<span>hide the login message<span></span></span></a>
			</span>
		</div><!--end right-->

		<h1><?php echo $this->get( 'pageTitle' ); ?> 
		<?php if( ( isset( $_GET['userid'] ) and count( $this->content['stream']['col1'] ) > 0 ) ):
			echo 'user: <a target="_blank" href="';
			switch( $this->get( 'account_type' ) ):
				case 'facebook':
					echo 'http://facebook.com/' . $this->content['stream']['col1'][0]['refs'][0]['source_id'];
				case 'twitter':
					echo 'http://twitter.com/' . $this->content['stream']['col1'][0]['refs'][0]['source_title'];
			endswitch;
			echo '">' . ( $this->get( 'account_type' ) == 'twitter' ? '@' : '' ) . $this->content['stream']['col1'][0]['refs'][0]['source_title'] . '</a>';
		endif; ?>
		</h1>
	</div><!--end wrap-->
</div><!--end header-->




<div class="wrap" id="content">
	<div class="main<?php echo $evencols ? ' evencol' : ''; ?>" id="stream">
		<?php if( !$mod_user->session_login() ): ?>
			<div class="welcome top">
				<p>We use your favorite topics, social accounts &amp; websites to build you a personalized magazine which is full of fresh, constantly updated content. And it takes less than minute to setup...</p>

				<a class="button twitter big" href="<?php echo $c_config['root']; ?>/process/tw-out">Sign in with Twitter</a>
				<a class="button facebook big" href="<?php echo $c_config['root']; ?>/process/fb-out">Sign in with Facebook</a>
				<a class="button green big" href="<?php echo $c_config['root']; ?>/login">Other Accounts</a>
			</div>
		<?php endif; ?>

		<?php if( $this->get( 'userid' ) == $mod_user->session_userid() and $this->get( 'title' ) != 'public' and $this->get( 'title' ) != 'source' ): ?>
			<?php if( $mod_user->session_permission( 'Subscribe' ) and count( $this->content['stream']['col1'] ) == 0 ): ?>
			<div class="block">
				<h2>Your stream is empty!</h2>
				<p>
					There's currently no articles in your stream. <strong>If you've already added some topics, accounts or feeds</strong>, it can take up to 30 minutes for the stream to update.<br />
					<strong>If not</strong>, you'll need to add some sources and/or accounts before articles start appearing:
				</p>
				<a class="button blue big" href="<?php echo $c_config['root']; ?>/settings">Add Social Accounts</a>
				<span class="greenor">or</span>
				<a class="button big green" href="<?php echo $c_config['root']; ?>/">Add Topics</a>
				<span class="greenor">or</span>
				<a class="button big green" href="<?php echo $c_config['root']; ?>/sources">Add Sources</a>
			</div>
			<?php endif; ?>
		<?php elseif( count( $this->content['stream']['col1'] ) == 0 ): ?>
			<div class="block">
				<h2>This stream is <em>currently</em> empty <img src="<?php echo $c_config['root']; ?>/inc/img/icons/loader.gif" alt="" /></h2>
				<p>We're doing our best to fill it right now!</p>
			</div>
		<?php endif; ?>

		<div id="add_source" class="hidden">
			<span class="edit">add sources / <a href="#" onclick="$( '#add_source' ).slideToggle( 100 ); return false;">close</a></span>
			<a href="<?php echo $c_config['root']; ?>/settings/accounts" class="linkthird">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/social.png" alt="" />
				Add Social Accounts
				<span>Get articles from twitter &amp; facebook</span>
			</a>
			<a href="#" class="linkthird middle">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/topic.png" alt="" />
				Add Topics
				<span>Follow your favorite topics</span>
			</a>
			<a href="<?php echo $c_config['root']; ?>/sources/add" class="linkthird">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/feed.png" alt="" />
			 	Follow Sources
				<span>Add &amp; Browse RSS feeds</span>
			</a>
		</div><!--end add_source-->
		
		<div class="col col3">
			<?php if( !$evencols ): ?>
				<span class="edit">upcoming articles<?php echo ( $this->get( 'nextOffset' ) > 1 ) ? ', page ' . ( $this->get( 'nextOffset' ) ) : ''; ?></span>
			<?php endif; ?>
			<?php
				foreach( $this->content['stream']['col3'] as $k => $item ):
					if( !$evencols )
						item_template( $this, $item, $this->get( 'userid' ), 'h4', true, true );
					else
						item_template( $this, $item, $this->get( 'userid' ), 'h3' );
				endforeach;
			?>
		</div><!--end col3-->

		<?php
			if( $this->get( 'features' ) ):
				foreach( $this->get( 'features' ) as $key => $feature ):
		?>
			<div class="feature">
				<span class="edit <?php echo $this->get( 'title' ) == 'hybrid' ? 'trending_topic' : ''; ?>">trending topic</span>
				<h1 class="topics">
					<?php
						foreach( $feature['topics'] as $count => $topic ):
							echo $topic . ( $count + 1 == count( $feature['topics'] ) ? '' : ' + ' );
						endforeach;
					?>
				</h1>
				<div class="left">
					<?php
						item_template( $this, $feature['articles'][0], $this->get( 'userid' ), 'h1', true );
						if( count( $feature['articles'] ) > 3 ):
							item_template( $this, $feature['articles'][1], $this->get( 'userid' ), 'h1', true, true );
						endif;
					?>
				</div><!--end left-->
				<div class="right half">
					<?php
						foreach( $feature['articles'] as $count => $article ):
							if( $count == 0 )
								continue;
							if( $count == 1 and count( $feature['articles'] ) > 3 )
								continue;

							if( count( $feature['articles'] ) == 2 ):
								item_template( $this, $article, $this->get( 'userid' ), 'h1', true, true );
							else:
								item_template( $this, $article, $this->get( 'userid' ), 'h3', false, true );
							endif;
						endforeach;
					?>
				</div><!--end right-->
			</div><!--end feature-->
		<?php 
				endforeach;
			endif;
		?>


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
					case 'account':
						echo 'latest articles' . ( ( isset( $_GET['userid'] ) and count( $this->content['stream']['col1'] ) > 0 ) ? ' from ' . ( $this->get( 'account_type' ) == 'twitter' ? '@' : '' ) . $this->content['stream']['col1'][0]['refs'][0]['source_title'] . ', <a href="' . $c_config['root'] . '/account/' . $this->get( 'account_type' ) . '">view all</a>' : '' );
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
						item_template( $this, $item, $this->get( 'userid' ) );
					elseif( $k < 2 )
						item_template( $this, $item, $this->get( 'userid' ), 'h1' );
					elseif( $k < 5 )
						item_template( $this, $item, $this->get( 'userid' ), 'h2' );
					else
						item_template( $this, $item, $this->get( 'userid' ) );
				endforeach;
			?>
		</div><!--end col1-->


		<div class="col col2">
			<?php
				foreach( $this->content['stream']['col2'] as $k => $item ):
					if( $evencols )
						item_template( $this, $item, $this->get( 'userid' ) );
					elseif( $k < 1 )
						item_template( $this, $item, $this->get( 'userid' ), 'h1' );
					elseif( $k < 4 )
						item_template( $this, $item, $this->get( 'userid' ), 'h2' );
					else
						item_template( $this, $item, $this->get( 'userid' ) );
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




<div id="sidebars">
	<div class="wrap">
		<div class="left" id="leftbar">
			<?php if( $mod_user->session_login() and $this->get( 'userid' ) != $mod_user->session_userid() ): ?>
				<ul>
					<li><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>">&larr; my streams</a></li>
				</ul>
			<?php endif; ?>

			<ul>
				<li class="title">Streams <a href="<?php echo $c_config['root']; ?>/help/streams" class="edit">&larr; what?</a></li>
				<?php
					if( is_numeric( $this->get( 'userid' ) ) ):
						foreach( array( 'hybrid', 'unread', 'popular', 'newest' ) as $stream_type ):
							if( ( $stream_type == 'hybrid' or $stream_type == 'unread' ) and $this->get( 'userid' ) != $mod_user->session_userid() ) continue;
							echo '<li>' . (
								$this->content['title'] == $stream_type ? ucfirst( $stream_type ) . ' &rarr;' : '<a href="' . $c_config['root'] . '/user/' . $this->get( 'userid' ) . ( $stream_type == 'hybrid' ? '' : '/' . $stream_type ) . '">' . ucfirst( $stream_type ) . '</a>'
							) . '</li>';
						endforeach;
					endif;
				?>

				<li>
					<?php echo $this->get( 'title' ) == 'public' ? 'All/Public &rarr;' : '<a href="' . $c_config['root'] . '/public">All/Public</a>'; ?>
				</li>
			</ul>

			<?php if( isset( $this->content['accounts'] ) and $mod_user->session_userid() == $this->get( 'userid' ) ): ?>
				<ul>
					<li class="title">Accounts <a href="<?php echo $c_config['root']; ?>/settings/accounts" class="edit">edit</a></li>
					<?php foreach( $this->content['accounts'] as $account ): ?>
						<?php if( $this->get( 'account_type' ) == $account['type'] and !isset( $_GET['userid'] ) ): ?>
							<li><?php echo ucfirst( $account['type'] ); ?> &rarr;</li>
						<?php else: ?>
							<li><a href="<?php echo $c_config['root']; ?>/account/<?php echo $account['type']; ?>"><?php echo ucfirst( $account['type'] ); ?></a></li>
						<?php endif; ?>
					<?php endforeach; if( count( $this->get( 'accounts' ) ) <= 0 ): ?>
						<li><a href="<?php echo $c_config['root']; ?>/settings/accounts">Add accounts &rarr;</a></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
			
				<ul>
					<li class="title">Topics</li>
				</ul>

			<?php if( isset( $this->content['collections'] ) and count( $this->get( 'collections' ) ) > 0 ): ?>
				<ul>
					<li class="title">Collections <a href="<?php echo $c_config['root']; ?>/settings/collections" class="edit">edit</a></li>
					<?php foreach( $this->get( 'collections' ) as $collection ): ?>
						<li>
							<?php if( $collection['id'] == $this->get( 'collection_id' ) ): ?>
								<?php echo $collection['name']; ?> &rarr;
							<?php else: ?>
								<a href="<?php echo $c_config['root']; ?>/collection/<?php echo $collection['id']; ?>"><?php echo $collection['name']; ?></a> 
								<span class="type"><?php echo $collection['articles']; ?> article<?php echo $collection['articles'] == 1 ? '' : 's'; ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if( $this->get( 'userid' ) ): ?>
				<?php if( $this->get( 'sources' ) ): ?>
				<ul class="sources">
					<li class="title">Sources<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="' . $c_config['root'] . '/sources/me" class="edit">edit</a>' : ''; ?></li>
					<?php foreach( $this->get( 'sources' ) as $source ):

?><li class="source<?php echo $source['id'] == $this->get( 'source_id' ) ? ' active': ''; ?>"><a href="<?php echo $c_config['root']; ?>/source/<?php echo $source['id']; ?>" class="tip"><span>
<strong><?php echo $source['source_title']; ?></strong>
<small><?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? 'You are subscribed' : $this->get( 'username' ) . ' is subscribed'; ?></small><span></span>
</span><img src="http://favicon.fdev.in/<?php echo $source['source_domain']; ?>" /></a></li><?php 

						endforeach; ?>
				</ul>
				<?php endif; ?>

				
				<ul>
					<li class="title">Users<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="#" class="edit"><strike>edit</strike></a>' : ''; ?></li>
					<?php if( $this->get( 'followings' ) ): ?>
					<?php foreach( $this->get( 'followings' ) as $follow ):

?><li class="source"><a href="<?php echo $c_config['root']; ?>/user/<?php echo $follow['id']; ?>" class="tip"><span>
<strong><?php echo $follow['name']; ?></strong>
<small><?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? 'You follow them' : $this->get( 'username' ) . ' follows them'; ?></small><span></span>
</span><img src="<?php echo !empty( $follow['avatar_url'] ) ? $follow['avatar_url'] : $c_config['root'] . '/inc/img/icons/user.png'; ?>" alt="<?php echo $follow['name']; ?>" /></a></li><?php 

					endforeach; ?>
					<?php elseif( $mod_user->session_login() and $this->get( 'userid' ) == $mod_user->session_userid() ): ?>
						<li><a href="#">Add Users &#187;</a></li>
					<?php else: ?>
						<li><?php echo $this->get( 'username' ); ?> follows no users</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</div><!--end left-->

		<div class="right">
			<?php if( $mod_user->session_login() ): ?>
				<div class="infobox success">
					<img src="<?php echo $c_config['root']; ?>/inc/img/icons/info/success.png" alt="" />
					<p>
						<strong>Welcome to version <?php echo PULSEFEED_VERSION; ?></strong>
						<br /><a href="http://blog.pulsefeed.com/post/15" target="_blank"><strong>Read about the changes &#187;</strong></a></a>
					</p>
				</div>
			<?php endif; ?>

		<?php if( $mod_user->session_login() and $mod_cookie->get( 'ChangeUsernameMessage' ) == '1' ): ?>
			<div class="infobox info">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/info/info.png" alt="" />
				<p>
					We noticed you haven't yet changed your username!
					<br /><a href="<?php echo $c_config['root']; ?>/settings"><strong>Change username &#187;</strong></a>
				</p>
			</div>
		<?php endif; ?>

		<?php if( $this->get( 'title' ) != 'public' or $mod_user->session_login() ): ?>
			<div class="ad">
				<img src="<?php echo $c_config['root']; ?>/inc/img/ads/234x60.gif" alt="" />
			</div><!--end ad-->
		<?php endif; ?>

		<?php if( $this->get( 'userid' ) == $mod_user->session_userid() and $this->get( 'username' ) ): ?>
			<h4>Recommended Topics</h4>
			<ul class="recommendations topics">
				<li>
					<span class="title"><a href="#">#facebook</a></span>
					<span class="edit">recommended topic<form class="inline"><input type="submit" class="inline edit" value="+subscribe" /></form></span>
				</li>
				<li>
					<span class="title"><a href="#">#technology</a></span>
					<span class="edit">recommended topic<form class="inline"><input type="submit" class="inline edit" value="+subscribe" /></form></span>
				</li>
				<li>
					<span class="title"><a href="#">#osx</a></span>
					<span class="edit">recommended topic<form class="inline"><input type="submit" class="inline edit" value="+subscribe" /></form></span>
				</li>
			</ul>

			<h4>Recommended Users</h4>
			<ul class="recommendations users">
				<li>
					<img src="http://a0.twimg.com/profile_images/981707561/avatar_normal.png" />
					<span class="title"><a href="#">Fizzadar</a></span>
					<span class="edit">following on twitter<form class="inline"><input type="submit" class="inline edit" value="+follow" /></form></span>
				</li>
				<li>
					<img src="http://a0.twimg.com/profile_images/502309381/steam_logo_normal.png" />
					<span class="title"><a href="#">LuaStoned</a></span>
					<span class="edit">similar user<form class="inline"><input type="submit" class="inline edit" value="+follow" /></form></span>
				</li>
				<li>
					<img src="http://a0.twimg.com/profile_images/717452266/c65521093b7c2855ec6a000a104d8318b46a4bed_full_normal.jpg" />
					<span class="title"><a href="#">Jack Bell</a></span>
					<span class="edit">friend on facebook<form class="inline"><input type="submit" class="inline edit" value="+follow" /></form></span>
				</li>
			</ul>
		<?php endif; ?>

		<?php if( $this->get( 'title' ) == 'source' ): ?>
			<h4>Source Topics</h4>
			<ul class="tags">
				<?php if( count( $this->get( 'source_topics' ) ) > 0 ): foreach( $this->get( 'source_topics' ) as $topic ): ?>
					<li><a href="<?php echo $c_config['root']; ?>/topic/<?php echo $topic['id']; ?>">#<?php echo $topic['title']; ?></a></li>
				<?php endforeach; else: ?>
					<li>No topics</li>
				<?php endif; ?>

				<?php if( $mod_user->session_permission( 'TagSource' ) ): ?>
				<li class="form"><form class="inline">
					<input type="text" value="type tags..." class="edit" onclick="if( this.value == 'type tags...' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'type tags...'; }" />
					<input type="submit" class="edit" value="Add tags &#187;" />
				</form></li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

			<h4>Bits &amp; Bobs</h4>
			<div class="biglinks">
				<!--page specific-->
				<?php if( $this->get( 'title' ) == 'source' ): ?>
					<a href="<?php echo $this->content['source']['site_url']; ?>" target="_blank" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/original.png" alt="" /> View source &rarr;</span>
						view the original source website
					</a>
				<?php endif; ?>

				<!--user related-->
				<?php if( $mod_user->session_login() ): ?>
					<a href="<?php echo $c_config['root']; ?>/settings" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/settings.png" alt="" /> Your Pulsefeed Settings</span>
						customize your pulsefeed setup
					</a>
					<a href="<?php echo $c_config['root']; ?>/feedback" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/suggestion.png" alt="" /> Make Suggestions</span>
						what would you like pulsefeed to do?
					</a>
				<?php else: ?>
					<a href="<?php echo $c_config['root']; ?>/login" class="biglink">
						<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/login.png" alt="" /> Login to Pulsefeed</span>
						no need to regsiter, just login!
					</a>
				<?php endif; ?>

				<!--and the rest-->
				<a href="http://blog.pulsefeed.com/" class="biglink">
					<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/blog.png" alt="" /> View our Blog</span>
					get the latest updates on pulsefeed
				</a>

				<a href="http://twitter.com/pulsefeed" class="biglink">
					<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/twitter.png" alt="" /> Follow @pulsefeed</span>
					follow us on twitter
				</a>

				<!--
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
				-->
			</div><!--end biglinks-->
		</div><!--end right-->

		<div class="fix">
			<a href="#" class="top_link">
				&uarr; top
			</a>
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->



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
			&rarr; recommended sources &amp; topics are listed here, along with useful links around the site
		</div>
		<a href="<?php echo $c_config['root']; ?>" class="button" onclick="$( '#tour' ).fadeOut( 500 ); return false;">Continue to Pulsefeed &rarr;</a>
	</div><!--end wrap-->
</div><!--end tour-->



<?php
	function item_template( $that, $item, $uid, $header = 'h3', $force_long = false, $no_image = false ) {
		global $mod_user, $mod_token, $c_config;

		//work out if we have the source ref
		$source = false;
		$orig = false;
		foreach( $item['refs'] as $ref )
			if( $ref['source_type'] == 'source' )
				$source = true;

		$long = $no_image ? false : true;

?><div class="item" id="article_<?php echo $item['id']; ?>">
	<<?php echo $header; ?>><a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" rel="nofollow" class="article_link"><?php echo $item['title']; ?></a></<?php echo $header; ?>>
	<?php if( !empty( $item['image_half'] ) and !$no_image ): $long = false; ?>
		<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link" rel="nofollow">
			<img class="thumb" src="<?php echo $c_config['root'] . '/' . $item['image_half']; ?>" alt="<?php echo $item['title']; ?>" />
		</a>
	<?php elseif( !empty( $item['image_third'] ) and !$no_image ): $long = false; ?>
		<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link" rel="nofollow">
			<img class="thumb" src="<?php echo $c_config['root'] . '/' . $item['image_third']; ?>" alt="<?php echo $item['title']; ?>" />
		</a>
	<?php endif;
	?><p><?php echo ( $long or $force_long ) ? $item['short_description'] : $item['shorter_description']; ?><a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link" rel="nofollow">
	<?php switch( $item['type'] ):
			case 'video':
				echo '';
				break;
			default:
				echo 'read article &rarr;';
	endswitch; ?></a></p>
	<div class="meta">
		<?php foreach( $item['refs'] as $ref ):
			?><a href="<?php echo $c_config['root']; ?>/<?php
				switch( $ref['source_type'] ):
					case 'source':
					case 'public':
						echo 'source' . '/' . $ref['source_id'];
						break;
					case 'like':
						echo 'user' . '/' . $ref['source_id'];
						break;
					case 'facebook':
					case 'twitter':
						echo 'account/' . $ref['source_type'] . '/' . $ref['source_id'];
						break;
					default:
						echo '#';
				endswitch;
				?>" class="tip hover"><span><?php
						switch( $ref['source_type'] ):
							case 'twitter':
							case 'facebook':
								echo '<img src="' . $c_config['root'] . '/inc/img/icons/share/' . $ref['source_type'] . '.png" />';
						endswitch;
					?><strong><?php echo ( $ref['source_type'] == 'twitter' ? '@' : '' ) . $ref['source_title']; ?></strong><small><?php
						switch( $ref['source_type'] ):
							case 'public':
								echo 'Public source';
								break;
							case 'source':
								if( $that->get( 'title' ) == 'source' ):
									echo ( $that->get( 'subscribed' ) ? 'You are' : 'Not' ) . ' subscribed';
								elseif( $that->get( 'title' ) == 'collection' ):
									echo 'Original Source';
								else:
									echo ( $that->get( 'userid' ) == $mod_user->session_userid() ? 'You are' : $that->get( 'username' ) . ' is' ) . ' subscribed';
								endif;
								break;
							case 'facebook':
								echo 'You are subscribed';
								break;
							case 'twitter':
							case 'like':
								echo ( $that->get( 'userid' ) == $mod_user->session_userid() ? 'You follow' : $that->get( 'username' ) . ' follows' ) . ' them';
								break;
							default:
								echo 'Unknown';
						endswitch;
					?></small><span></span></span>
				<img src="<?php
					switch( $ref['source_type'] ):
						case 'source':
						case 'public':
							echo 'http://favicon.fdev.in/' . $ref['source_data']['domain'];
							break;
						case 'like':
							echo $c_config['root'] . '/inc/img/icons/share/' . $ref['source_type'] . '.png';
							break;
						case 'twitter':
							echo 'http://tweeter.fdev.in/' . $ref['source_id'];
							break;
						case 'facebook':
							echo 'http://graph.facebook.com/' . $ref['source_id'] . '/picture';
							break;
						default:
							echo $c_config['root'] . '/inc/img/icons/sidebar/original.png';
					endswitch;
				?>" /></a>
			<?php if( !$orig and !$source and isset( $ref['origin_id'] ) and $ref['origin_id'] > 0 and isset( $ref['origin_title'] ) and isset( $ref['origin_data'] ) ): $orig = true; ?>
				<a href="<?php echo $c_config['root']; ?>/source/<?php echo $ref['origin_id']; ?>" class="tip">
					<span><strong><?php echo $ref['origin_title']; ?></strong><small>Original source</small><span></span></span>
					<img src="http://favicon.fdev.in/<?php echo $ref['origin_data']['domain']; ?>" /></a>
			<?php endif; 
		endforeach;
	if( $mod_user->session_login() ): ?>
<!--hide-->
		<?php
			if( isset( $item['unread'] ) and $item['unread'] == 1 and $that->get( 'userid' ) == $mod_user->session_userid() ):
				echo
					'<form action="' . $c_config['root'] . '/process/article-hide" method="post" class="hide_form">
<input type="hidden" name="article_id" value="' . $item['id'] . '" />
<input type="hidden" name="mod_token" value="' . $mod_token . '" />
<input type="submit" value="Hide" />
</form> - ';
			endif;
		?>

<!--remove from collection-->
		<?php
			if( $that->get( 'title' ) == 'collection' and $that->get( 'userid' ) == $mod_user->session_userid() ):
				echo
					'<form action="' . $c_config['root'] . '/process/article-uncollect" method="post" class="uncollect_form">
<input type="hidden" name="article_id" value="' . $item['id'] . '" />
<input type="hidden" name="collection_id" value="' . $that->get( 'collection_id' ) . '" />
<input type="hidden" name="mod_token" value="' . $mod_token . '" />
<input type="submit" value="Remove" />
</form> - ';
			endif;
		?>
		
		<!--collect-->
		<span class="collect"><a class="collect_button tip mini always" href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>/collect" articleID="<?php echo $item['id']; ?>">Collect</a></span> - 

		<!--like button-->
		<form action="<?php echo $c_config['root']; ?>/process/article-<?php echo $item['liked'] ? 'unlike' : 'like'; ?>" method="post" class="like_form">
			<input type="hidden" name="article_id" value="<?php echo $item['id']; ?>" />
			<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			<input type="submit" value="<?php echo $item['liked'] ? 'Unlike' : 'Like'; ?>" /> <span class="likes">(<span><?php echo $item['likes']; ?></span>)</span>
		</form>
	<?php endif; ?>
	<span class="time"> - <?php echo $item['time_ago']; ?></span>
	</div>
</div><!--end item--><?php } ?>