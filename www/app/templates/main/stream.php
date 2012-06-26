<?php 
	/*
		file: app/templates/main/stream.php
		desc: stream template for main design
	*/
	
	//modules
	global $mod_data, $mod_user, $mod_cookie, $mod_token;

	//include item template
	$this->load( 'functions/item_template' );
	
	//work out if even cols or not
	$evencols = true;
	if( in_array( $this->get( 'title' ), array( 'hybrid', 'popular', 'public', 'topic' ) ) ) $evencols = false;
?>

<script type="text/javascript">
	pulsefeed.streamType = '<?php echo $this->get( 'title' ); ?>';
	pulsefeed.streamOffset = <?php echo $this->get( 'nextOffset' ); ?>;
<?php if( $this->get( 'title' ) == 'website' ): ?>
	pulsefeed.streamWebsite = <?php echo $this->get( 'website_id' ); ?>;
	pulsefeed.streamSubscribed = <?php echo $this->get( 'subscribed' ) ? 'true' : 'false'; ?>;
<?php elseif( $this->get( 'title' ) == 'account' ): ?>
	pulsefeed.streamAccount = '<?php echo $this->get( 'account_type' ); ?>';
<?php elseif( $this->get( 'title' ) == 'collection' ): ?>
	pulsefeed.streamCollection = '<?php echo $this->get( 'collection_id' ); ?>';
<?php elseif( $this->get( 'title' ) == 'topic' ): ?>
	pulsefeed.streamTopic = '<?php echo $this->get( 'topic_id' ); ?>';
<?php endif; ?>
	pulsefeed.streamUser = <?php echo $this->get( 'userid' ) ? $this->get( 'userid' ) : -1; ?>;
	pulsefeed.streamUsername = '<?php echo $this->get( 'username' ); ?>';
</script> 

<div id="header">
	<div class="wrap">
		<div class="left">
			<?php if( in_array( $this->get( 'title' ), array( 'hybrid', 'unread', 'popular', 'newest' ) ) and $mod_user->session_login() and $mod_user->session_userid() == $this->get( 'userid' ) ): ?>
				<a href="<?php echo $c_config['root']; ?>/topics" class="button green" onclick="$( '#add_source' ).slideToggle( 150 ); return false;">+ Add Sources</a>
			<?php endif; ?>

			<?php if( $this->get( 'title' ) == 'website' and $mod_user->session_login() and $mod_user->session_permission( 'Subscribe' ) ): ?>
				<?php if( $this->get( 'subscribed' ) ): ?>
					<form action="<?php echo $c_config['root']; ?>/process/website-unsubscribe" method="post" id="subunsub" class="website_subscribe">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="website_id" value="<?php echo $this->get( 'website_id' ); ?>" />
						<input type="submit" value="Unsubscibe" class="button red" />
					</form>
				<?php else: ?>
					<form action="<?php echo $c_config['root']; ?>/process/website-subscribe" method="post" id="subunsub" class="website_subscribe">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="website_id" value="<?php echo $this->get( 'website_id' ); ?>" />
						<input type="submit" value="+ Subscribe" class="button green" />
					</form>
				<?php endif; ?>
			<?php elseif( $this->get( 'title' ) != 'collection' and $this->get( 'userid' ) != $mod_user->session_userid() and $mod_user->session_permission( 'Follow' ) ): ?>
				<?php if( $this->get( 'following' ) ): ?>
					<form action="<?php echo $c_config['root']; ?>/process/unfollow" method="post" id="subunsub" class="user_follow">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="user_id" value="<?php echo $this->get( 'userid' ); ?>" />
						<input type="submit" value="Unfollow" class="button red" />
					</form>
				<?php else: ?>
					<form action="<?php echo $c_config['root']; ?>/process/follow" method="post" id="subunsub" class="user_follow">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="user_id" value="<?php echo $this->get( 'userid' ); ?>" />
						<input type="submit" value="+ Follow" class="button green" />
					</form>
				<?php endif; ?>
			<?php elseif( $this->get( 'title' ) == 'topic' and $mod_user->session_login() and $mod_user->session_permission( 'Subscribe' ) ): ?>
				<?php if( $this->get( 'subscribed' ) ): ?>
					<form action="<?php echo $c_config['root']; ?>/process/topic-unsubscribe" method="post" id="subunsub" class="topic_subscribe">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="topic_id" value="<?php echo $this->get( 'topic_id' ); ?>" />
						<input type="submit" value="Unsubscibe" class="button red" />
					</form>
				<?php else: ?>
					<form action="<?php echo $c_config['root']; ?>/process/topic-subscribe" method="post" id="subunsub" class="topic_subscribe">
						<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
						<input type="hidden" name="topic_id" value="<?php echo $this->get( 'topic_id' ); ?>" />
						<input type="submit" value="+ Subscribe" class="button green" />
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div><!--end left-->

		<div class="right">
			<span>
				<!--js?-->
				<a class="button row tip mini down <?php echo !$mod_cookie->get( 'no_js' ) ? 'green' : 'red'; ?>" href="?<?php echo !$mod_cookie->get( 'no_js' ) ? 'js_off' : 'js_on'; ?>">Script: <?php echo !$mod_cookie->get( 'no_js' ) ? 'on' : 'off'; ?><span>turn javascript on / off<span></span></span></a>

				<!--images on/off-->
				<a class="stream_images_toggle button <?php echo !$mod_cookie->get( 'hide_images' ) ? 'green' : 'red'; ?> row tip mini down" href="?<?php echo !$mod_cookie->get( 'hide_images' ) ? 'images_off' : 'images_on'; ?>">Images: <?php echo !$mod_cookie->get( 'hide_images' ) ? 'on' : 'off'; ?><span><?php echo !$mod_cookie->get( 'hide_images' ) ? 'hide' : 'show'; ?> images in the stream<span></span></span></a>

				<!--columns 2/3-->
				<a class="stream_column_toggle button blue row tip mini down" href="?<?php echo !$mod_cookie->get( 'two_col' ) ? 'two_col' : 'three_col'; ?>">Columns: <?php echo !$mod_cookie->get( 'two_col' ) ? '3' : '2'; ?><span>switch between 2 &amp; 3 columns<span></span></span></a>

				<!--<?php //if( in_array( $this->get( 'title' ), array( 'account', 'topic' ) ) ): $pop = true; ?>
					<a class="stream_order_toggle button blue row tip fourth mini down">Order: popular<span>switch between popular &amp; newest articles<span></span></span></a>
				<?php //endif; ?>-->

				<?php if( !$mod_user->session_login() ): ?>
					<!--message show/hide-->
					<a class="stream_message_toggle button <?php echo !$mod_cookie->get( 'hide_message' ) ? 'red' : 'green'; ?> row tip <?php echo isset( $pop ) ? 'fith' : 'fourth'; ?> mini down" href="?<?php echo !$mod_cookie->get( 'hide_message' ) ? 'hide' : 'show'; ?>_message"><?php echo !$mod_cookie->get( 'hide_message' ) ? 'Hide' : 'Show'; ?> Message<span><?php echo !$mod_cookie->get( 'hide_message' ) ? 'hide' : 'show'; ?> the login message<span></span></span></a>
				<?php endif; ?>
			</span>
		</div><!--end right-->

		<h1>
			<?php echo ( $this->get( 'user') and !empty( $this->content['user']['avatar_url'] ) ) ? '<img src="' . $this->content['user']['avatar_url'] . '" class="avatar" /> ' : ''; ?>
			<?php echo $this->get( 'pageTitle' ); ?> 
		<?php if( ( isset( $_GET['userid'] ) and count( $this->content['stream']['col1'] ) > 0 ) ):
			echo 'user: <a target="_blank" href="';
			switch( $this->get( 'account_type' ) ):
				case 'facebook':
					echo 'http://facebook.com/' . $this->content['stream']['col1'][0]['refs'][0]['source_id'];
					break;
				case 'twitter':
					echo 'http://twitter.com/' . $this->content['stream']['col1'][0]['refs'][0]['source_title'];
					break;
			endswitch;
			echo '">' . ( $this->get( 'account_type' ) == 'twitter' ? '@' : '' ) . $this->content['stream']['col1'][0]['refs'][0]['source_title'] . '</a>';
		endif; ?>
		</h1>
	</div><!--end wrap-->
</div><!--end header-->

<!--content/wrap-->
<div class="wrap" id="content">
	<div class="main wide<?php echo $evencols ? ' evencol' : ''; echo $mod_cookie->get( 'two_col' ) ? ' twocol' : ''; ?>" id="stream">
		<?php if( !$mod_user->session_login() ): ?>
			<!--not logged in-->
			<div class="welcome top"<?php echo $mod_cookie->get( 'hide_message' ) ? ' style="display:none;"' : ''; ?>>
				<p>We use your favorite topics, social accounts &amp; websites to build you a personalized magazine which is full of fresh, constantly updated content. And it takes less than minute to setup...</p>

				<a class="button twitter big" href="<?php echo $c_config['root']; ?>/process/tw-out">Sign in with Twitter</a>
				<a class="button facebook big" href="<?php echo $c_config['root']; ?>/process/fb-out">Sign in with Facebook</a>
				<a class="button green big" href="<?php echo $c_config['root']; ?>/login">Other Accounts</a>
			</div>
		<?php else: ?>
			<div class="welcome notop <?php echo isset( $_GET['welcome'] ) ? '' : 'hidden'; ?>">
				<h1>Hello.</h1>
				<p>Welcome to Pulsefeed; to get your stream started pick some interesting <a href="<?php echo $c_config['root']; ?>/topics">topics</a> or <a href="<?php echo $c_config['root']; ?>/websites">websites</a> to subscribe to. Your Facebook &amp; Twitter accounts can also <a href="<?php echo $c_config['root']; ?>/settings/accounts">be linked up</a> to receive stories from your friends.</p>
				<br />
				<a href="<?php echo $c_config['root']; ?>/topics" class="button big green">Popular Topics</a>
				<a href="<?php echo $c_config['root']; ?>/websites" class="button big blue">Browse Websites</a><br /><br />
				<a href="<?php echo $c_config['root']; ?>/process/fb-out" class="button big facebook">+ Add Facebook Account</a>
				<a href="<?php echo $c_config['root']; ?>/process/tw-out" class="button big twitter">+ Add Twitter Account</a>
			</div><!--end welcome-->
		<?php endif; ?>

		<!--empty stream-->
		<?php if( $this->get( 'userid' ) == $mod_user->session_userid() and !in_array( $this->get( 'title' ), array( 'topic', 'source', 'public' ) ) and count( $this->content['stream']['col1'] ) == 0 ): ?>
			<div class="welcome">
			<?php switch( $this->get( 'title' ) ):
					case 'collection':
				?><p>This is your empty collection. To place articles in here simply click select below any article (on the right).<br /><br /><a href="<?php echo $c_config['root']; ?>">Hybrid Stream &rarr;</a></p>
			<?php break; default: ?>
				<p>This stream is currently empty :(&nbsp;&nbsp;&nbsp;</p>
				<p>If you haven't subscribed to any <a href="<?php echo $c_config['root']; ?>/topics">topics</a>, <a href="<?php echo $c_config['root']; ?>/sources">sources</a> or <a href="<?php echo $c_config['root']; ?>/users">users</a> yet, you're stream make take longer to fill.</p>
				<p>If you have very recently joined or added accounts, it may take 10-15 minutes to 'warm up' your stream.</p>
				
				<a href="<?php echo $c_config['root']; ?>/topics" class="button big blue">Interesting Topics</a>
				<a href="<?php echo $c_config['root']; ?>/websites" class="button big blue">Popular Websites</a>
				<a href="<?php echo $c_config['root']; ?>/websites/add" class="button big green">+ Add RSS/OPML</a>

				<br /><br /><a href="<?php echo $c_config['root']; ?>/process/fb-out" class="button big facebook">+ Add Facebook Account</a>
				<a href="<?php echo $c_config['root']; ?>/process/tw-out" class="button big twitter">+ Add Twitter Account</a>
			<?php endswitch; ?>
			</div><!--end welcome-->
		<?php elseif( count( $this->content['stream']['col1'] ) == 0 ): ?>
			<div class="welcome">
				<p>This stream is currently empty :(&nbsp;&nbsp;&nbsp;There may be a number of reasons for this:</p>
				<p><strong>Topic Stream</strong>: this means no articles have been allocated to this topic in the past 7 days. This is normally because the topic was only created recently.</p>
				<p><strong>Source Stream</strong>: we only track sources with subscribers; if this source has subscribers the feed may be failing to load; someone should locate and fix the issue within 24-48 hours from now.</p>
				<p><strong>Another Users Stream</strong>: this could be because the user has not added any accounts, or perhaps not subscribed to any topics/sources.</p>

				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/loader.gif" alt="" />
			</div>
		<?php endif; ?>

		<!--add source-->
		<div id="add_source" class="hidden">
			<span class="edit">add sources / <a href="#" onclick="$( '#add_source' ).slideToggle( 100 ); return false;">close</a></span>
			<a href="<?php echo $c_config['root']; ?>/settings/accounts" class="linkthird">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/social.png" alt="" />
				Add Social Accounts
				<span>Get articles from twitter &amp; facebook</span>
			</a>
			<a href="<?php echo $c_config['root']; ?>/topics" class="linkthird middle">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/topic.png" alt="" />
				Add Topics
				<span>Follow your favorite topics</span>
			</a>
			<a href="<?php echo $c_config['root']; ?>/websites" class="linkthird">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/big/feed.png" alt="" />
			 	Follow Websites
				<span>Add RSS feeds &amp; OPML files</span>
			</a>
		</div><!--end add_source-->
		
		<?php if( count( $this->content['stream']['col1'] ) > 0 ): ?>
		<span class="edit">
		<?php
			if( count( $this->content['stream'] ) < 1 ):
				echo 'Oh no!';
			else:
				switch( $this->content['title'] ):
					case 'hybrid':
					case 'public':
					case 'popular':
					case 'topic':
						echo 'popular articles';
						break;
					case 'user':
					case 'newest':
					case 'source':
					case 'unread':
					case 'collection':
						echo 'latest articles';
						break;
					case 'account':
						echo 'latest articles' . ( ( isset( $_GET['userid'] ) and count( $this->content['stream']['col1'] ) > 0 ) ? ' from ' . ( $this->get( 'account_type' ) == 'twitter' ? '@' : '' ) . $this->content['stream']['col1'][0]['refs'][0]['source_title'] . ', <a href="' . $c_config['root'] . '/account/' . $this->get( 'account_type' ) . '">view all</a>' : '' );
						break;
				endswitch;
				echo ( $this->get( 'nextOffset' ) > 1 ) ? ', page ' . ( $this->get( 'nextOffset' ) ) : '';
			endif;
		?>
		</span>
		<?php endif; ?>

	<div class="col12">
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
	</div><!--end col12-->
	
		<div class="col col3">
			<!--<span class="edit">follow users</span>

			<span class="edit">Bits &amp; Bobs</span>

				<div>hi</div>

	<div class="item" id="article_16154">
	<h4>
		<a href="http://pulsefeed.dev/article/16154" rel="nofollow" class="article_link">2012 — FOSS.IN</a>
	</h4>
	<p>
		FOSS.IN is an effort by volunteers – it is not a commercial event. Almost every task is handled by someone from Team FOSS.IN, we very rarely, if ever, outsource		<span class="extended hidden"> things to a third party. Every now and then, we will be calling for help, when we find that we do not have someone in the [...]...</span>
		... <a href="http://pulsefeed.dev/article/16154" class="article_link" rel="nofollow">
	read article &rarr;</a></p>
	<ul class="meta">
		<li class="tip hover big"><span>	<ul><li><small class="edit">author</small> Unknown</li>
							<li><small class="edit">date</small> 5th June</li>
												</ul>
					<strong><a href="http://pulsefeed.dev/website/372">View Article &rarr;</a></strong>
					<small>Sponsored Article</small><span></span></span>
				<a class="link" href="http://pulsefeed.dev/website/372">
				<img class="icon" src="/inc/img/icons/share/coins.png" alt="" /></a></li>

				<li class="tip hover big" style="display:inline;margin-left:-10px;"><span>	<ul>
							<li><small class="edit">info</small> <a href="#">What You Get</a> - <a href="#">Why?</a></li>
												</ul>
					<img src="/inc/img/icons/share/plus.png" alt="" /><strong><a href="http://pulsefeed.dev/website/372">Upgrade to pro &rarr;</a></strong>
					<small>Upgrade to hide ads &amp; more</small><span></span></span>
				<a class="link" href="http://pulsefeed.dev/website/372">
				<img class="icon" src="/inc/img/icons/share/bullet_delete.png" alt="" /></a></li>
				</ul>
	<div class="meta">

		<form action="http://pulsefeed.dev/process/article-hide" method="post" class="hide_form">
<input type="hidden" name="article_id" value="16154" />
<input type="hidden" name="mod_token" value="c7dcf0a577d42835cc19db6e054bdec4" />
<input type="submit" value="Hide" class="meta" />
</form> - 

		<span class="collect"><a class="collect_button tip mini always" href="http://pulsefeed.dev/article/16154/collect" data-articleid="16154">Collect</a></span> - 

	<span class="share"><a class="share_button tip mini always" href="http://pulsefeed.dev/article/16154/share" data-articleid="16154" data-articleurl="http://FOSS.IN/2012" data-fbshares="0" data-twlinks="57">Share</a></span>
	<span class="time"> - 16h ago</span>
	</div>
</div>
-->








			<?php if( !$evencols and count( $this->content['stream']['col3'] ) > 0 ): ?>
				<span class="edit">upcoming articles<?php echo ( $this->get( 'nextOffset' ) > 1 ) ? ', page ' . ( $this->get( 'nextOffset' ) ) : ''; ?></span>
			<?php else: ?>
				<span class="edit">&nbsp;</span>
			<?php endif; ?>
			
			<?php
				foreach( $this->content['stream']['col3'] as $k => $item ):
					if( !$evencols )
						item_template( $this, $item, $this->get( 'userid' ), 'h4', true, true );
					else
						item_template( $this, $item, $this->get( 'userid' ), 'h3', false, true );
				endforeach;
			?>
		</div><!--end col3-->

	<?php if( count( $this->content['stream']['col1'] ) > 0 ): ?>
		<!--more link-->
		<a class="morelink stream_load_more" href="?offset=<?php echo $this->get( 'nextOffset' ); ?>">load more articles &darr;</a>
	<?php endif; ?>
	</div><!--end main-->
</div><!--end content-->



<!--sidebars-->
<div id="sidebars">
	<div class="wrap">
		<div class="left" id="leftbar">
			<!--back to my stuff-->
			<?php if( $mod_user->session_login() and $this->get( 'userid' ) != $mod_user->session_userid() ): ?>
				<ul>
					<li><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>">&larr; my streams</a></li>
				</ul>
			<?php endif; ?>

			<!--streams-->
			<ul class="streams">
				<li class="title">Streams <a href="<?php echo $c_config['root']; ?>/help/streams" class="edit">&larr; what?</a></li>
				<?php
					if( is_numeric( $this->get( 'userid' ) ) ):
						foreach( array( 'hybrid', 'unread' ) as $stream_type ):
							if( ( $stream_type == 'hybrid' or $stream_type == 'unread' ) and $this->get( 'userid' ) != $mod_user->session_userid() ) continue;
							echo '<li>' . (
								$this->content['title'] == $stream_type ? ucfirst( $stream_type ) . ' &rarr;' : '<a href="' . $c_config['root'] . '/user/' . $this->get( 'userid' ) . ( $stream_type == 'hybrid' ? '' : '/' . $stream_type ) . '">' . ucfirst( $stream_type ) . '</a>'
							) . '</li>';
						endforeach;
					endif;
					if( $this->get( 'userid' ) and $this->get( 'userid' ) == $mod_user->session_userid() ):
				?>
				<li><a class="edit" onclick="$( '#sidebars .left ul.streams div.extra' ).fadeToggle( 100 ); if( $( this ).html() == 'more &darr;' ) { $( this ).html( 'less &uarr;' ); } else { $( this ).html( 'more &darr;' ); }"><?php echo in_array( $this->get( 'title' ), array( 'public', 'popular', 'newest', 'videos', 'photos' ) ) ? 'less &uarr;' : 'more &darr;'; ?></a></li>
				<?php endif; ?>
			<div class="extra <?php echo in_array( $this->get( 'title' ), array( 'public', 'popular', 'newest', 'videos', 'photos' ) ) ? '' : 'hidden'; ?>">
				<?php if( $this->get( 'userid' ) and $this->get( 'userid' ) == $mod_user->session_userid() ): ?>
				<!--<li><a href="#">Videos</a></li>
				<li><a href="#">Photos</a></li>-->
				<?php endif;
					if( is_numeric( $this->get( 'userid' ) ) ):
						foreach( array( 'popular', 'newest' ) as $stream_type ):
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
			</div><!--end hidden-->
			</ul>

			<!--accounts-->
			<?php if( $this->get( 'accounts' ) ): ?>
				<ul>
					<li class="title">Accounts <a href="<?php echo $c_config['root']; ?>/settings/accounts" class="edit">edit</a></li>
					<?php foreach( $this->get( 'accounts' ) as $account ): ?>
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
			
			<!--topics-->
			<?php if( $this->get( 'topics' ) ): ?>
				<ul>
					<li class="title">
						<a href="<?php echo $c_config['root']; ?>/topics">Topics</a>
						<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="' . $c_config['root'] . '/topics/me" class="edit">edit</a>' : ''; ?>
					</li>
					<?php foreach( $this->get( 'topics' ) as $topic ): ?>
						<li>
							<?php if( $this->get( 'topic_id' ) == $topic['id'] ): ?>
								<?php echo $topic['title']; ?> &rarr;
							<?php else: ?>
								<a href="<?php echo $c_config['root']; ?>/topic/<?php echo $topic['id']; ?>"><?php echo $topic['title']; ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<!--collections-->
			<?php if( $this->get( 'collections' ) ): ?>
				<ul>
					<li class="title">
						<a href="<?php echo $c_config['root']; ?>/collections">Collections</a>
						<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="' . $c_config['root'] . '/collections/me" class="edit">edit</a>' : ''; ?>
					</li>
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

			<!--websites-->
			<?php if( $this->get( 'websites' ) ): ?>
			<ul class="sources">
				<li class="title">
					<a href="<?php echo $c_config['root']; ?>/websites">Websites</a>
					<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="' . $c_config['root'] . '/websites/me" class="edit">edit</a>' : ''; ?>
				</li>
				<?php foreach( $this->get( 'websites' ) as $source ): ?>
					<li class="source<?php echo $source['id'] == $this->get( 'website_id' ) ? ' active': ''; ?>">
						<a href="<?php echo $c_config['root']; ?>/website/<?php echo $source['id']; ?>" class="tip">
							<span><strong><?php echo $source['site_title']; ?></strong>
							<small><?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? 'You are subscribed' : $this->get( 'username' ) . ' is subscribed'; ?></small><span></span></span>
							<img src="http://favicon.fdev.in/<?php echo $source['source_domain']; ?>" />
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<!--users-->
			<?php if( $this->get( 'followings' ) ): ?>
			<ul>
				<li class="title">
					<a href="<?php echo $c_config['root']; ?>/users">Users</a>
					<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? '' : ''; ?>
				</li>
				<?php foreach( $this->get( 'followings' ) as $follow ): ?>
					<li class="source">
						<a href="<?php echo $c_config['root']; ?>/user/<?php echo $follow['id']; ?>" class="tip">
							<span><strong><?php echo $follow['name']; ?></strong>
							<small><?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? 'You follow them' : $this->get( 'username' ) . ' follows them'; ?></small><span></span></span>
							<img src="<?php echo !empty( $follow['avatar_url'] ) ? $follow['avatar_url'] : $c_config['root'] . '/inc/img/icons/user.png'; ?>" alt="<?php echo $follow['name']; ?>" />
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div><!--end left-->


		<!--right sidebar-->
		<div class="right" style="display:none;">
		<?php if( $mod_user->session_login() ): ?>
			<!--latest changes-->
			<div class="infobox success">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/info/success.png" alt="" />
				<p>
					<strong>Welcome to version <?php echo PULSEFEED_VERSION; ?></strong>
					<br /><a href="http://blog.pulsefeed.com/post/15" target="_blank"><strong>Read about the changes &#187;</strong></a></a>
				</p>
			</div>
		<?php endif; ?>

		<?php if( $mod_user->session_login() and $mod_cookie->get( 'ChangeUsernameMessage' ) == '1' ): ?>
			<!--change username-->
			<div class="infobox info">
				<img src="<?php echo $c_config['root']; ?>/inc/img/icons/info/info.png" alt="" />
				<p>
					We noticed you haven't yet changed your username!
					<br /><a href="<?php echo $c_config['root']; ?>/settings"><strong>Change username &#187;</strong></a>
				</p>
			</div>
		<?php endif; ?>

		<!--blank advert!-->
		<div class="ad">
			<img src="<?php echo $c_config['root']; ?>/inc/img/ads/234x60.gif" alt="" />
		</div><!--end ad-->

		<?php if( false and $this->get( 'userid' ) == $mod_user->session_userid() and $this->get( 'username' ) ): ?>
			<!--recommended-->
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
					<a href="<?php echo $c_config['root']; ?>/suggest" class="biglink">
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
				<a href="http://blog.pulsefeed.com/" class="biglink" target="_blank">
					<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/blog.png" alt="" /> View our Blog</span>
					get the latest updates on pulsefeed
				</a>

				<a href="http://twitter.com/pulsefeed" class="biglink" target="_blank">
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