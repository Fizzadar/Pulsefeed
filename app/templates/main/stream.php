<?php 
	/*
		file: app/templates/main/stream.php
		desc: stream template for main design
	*/
	
	global $mod_data, $mod_user, $mod_cookie, $mod_token;
?>

<?php if( !$this->get( 'mainOnly' ) ): ?>
	<div id="header">
		<div class="wrap">
			<div class="left">
				<?php if( in_array( $this->get( 'title' ), array( 'hybrid', 'unread', 'popular', 'newest' ) ) and $mod_user->session_login() and $mod_user->session_userid() == $this->get( 'userid' ) ): ?>
				<a href="<?php echo $c_config['root']; ?>/sources" class="button" onclick="$( '#add_source' ).slideToggle(); return false;">+ add sources</a>
				<?php endif; ?>

				<?php if( $this->content['title'] == 'source' and $mod_user->session_login() ): ?>
					<?php if( $this->get( 'subscribed' ) ): ?>
						<form action="<?php echo $c_config['root']; ?>/?process=source-unsubscribe" method="post" id="subunsub">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
							<input type="submit" value="Unsubscibe" class="button" />
						</form>
					<?php else: ?>
						<form action="<?php echo $c_config['root']; ?>/?process=source-subscribe" method="post" id="subunsub">
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<input type="hidden" name="source_id" value="<?php echo $this->get( 'source_id' ); ?>" />
							<input type="submit" value="+ Subscribe" class="button" />
						</form>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<h1><?php echo $this->get( 'pageTitle' ); ?></h1>
		</div><!--end wrap-->
	</div><!--end header-->
<?php endif; ?>

	<div class="wrap" id="content">
		<div class="main" id="stream">

			<div id="add_source" class="hidden">
				<span class="edit">add sources / <a href="#" onclick="$( '#add_source' ).slideToggle(); return false;">close</a></span>
				<a href="<?php echo $c_config['root']; ?>/sources/me" class="morelink">Manage Sources</a>
				<a href="<?php echo $c_config['root']; ?>/sources" class="widelink">Browse Sources<span>Browse the directory of sources</span></a>
				<a href="<?php echo $c_config['root']; ?>/sources/add" class="widelink right">Add Directly<span>Enter a website / feed url</span></a>
			</div><!--end add_source-->

			<span class="edit">
			<?php
				if( count( $this->content['stream'] ) < 1 ):
					echo 'Oh no!';
				else:
					switch( $this->content['title'] ):
						case 'hybrid':
							echo 'popular unread articles';
							break;
						case 'public':
						case 'popular':
							echo 'popular articles';
							break;
						case 'user':
						case 'newest':
						case 'source':
							echo 'latest articles';
							break;
						case 'unread':
							echo 'latest unread articles';
							break;
					endswitch;
					echo isset( $_GET['list'] ) ? ', list style' : '';
					echo ( $this->get( 'nextOffset' ) > 1 ) ? ', page ' . ( $this->get( 'nextOffset' ) ) : '';
				endif;
			?>
			</span>
			
			<div class="item article feature">
				<h1><a href="#">Facebook</a> + <a href="#">Timeline</a> <span>trending</span></h1>
				<div class="contentslide">
					<div class="imeg">
						<div class="content">
							<h2><a href="#">Facebook Timeline Now Pushed To Everyone, Users Get A Week To Clean Up Profiles</a></h2>
							<img class="thumb" src="http://localhost/Feedbug/site/data/thumbs/quarter/404e6fb551b16a7f3a939c525fe8c3fdee6b9cd0.jpg" />
							<p>You can run, but you can’t hide. Facebook’s biggest user interface overhaul since the Wall, the Facebook Timeline, is rolling out worldwide starting today. According to the company, over the next few weeks, everyone will get the new Timeline. And here’s the important part: when you do, you’ll have just seven days to preview what’s there now, and hide anything...</p>
						</div>
						<!--<img class="bg" src="http://localhost/Feedbug/site/data/thumbs/half/404e6fb551b16a7f3a939c525fe8c3fdee6b9cd0.jpg" alt="" />-->
					</div>
				</div><!--end contentslide-->
				<ul class="controls">
					<li>
						<strong><a href="#">Facebook Timeline Now Pushed To Everyone, Users...</a></strong>
						<span>from <a href="#">Mashable!</a> &rarr; 1 hour ago</span>
					</li>
					<li>
						<strong><a href="#">Facebook Timeline Now Pushed To Everyone, Users...</a></strong>
						<span>from <a href="#">Mashable!</a> &rarr; 1 hour ago</span>
					</li>
					<li>
						<strong><a href="#">Facebook Timeline Now Pushed To Everyone, Users...</a></strong>
						<span>from <a href="#">Mashable!</a> &rarr; 1 hour ago</span>
					</li>
					<li>
						<strong><a href="#">Facebook Timeline Now Pushed To Everyone, Users...</a></strong>
						<span>from <a href="#">Mashable!</a> &rarr; 1 hour ago</span>
					</li>
				</ul>
			</div><!--end article feature-->

			<?php
				//loop our items (layers within the stream)
				foreach( $this->content['stream'] as $key => $item ):
					$this->add( 'currentStreamItems', $item['items'] );
					$this->add( 'currentStreamKey', $key );
					$this->load( 'stream/' . $item['template'] );
				endforeach;

				//empty stream
				if( count( $this->content['stream'] ) < 1 ):
			?>
				<div class="item article level_1">
					<span class="content">
					<h2>There are no more articles :(</h2>
					<p>We couldn't find any more articles for this stream.</p>
					</span>
				</div>
			<?php
				endif;
			?>

			<a class="morelink" href="?offset=<?php echo $this->get( 'nextOffset' ); ?>">load more articles &darr;</a>
		</div><!--end main-->
	</div><!--end content-->

<?php if( !$this->get( 'mainOnly' ) ): ?>
	<div id="sidebars">
		<div class="wrap">
			<div class="left" id="leftbar">
				<ul>
					<li class="title">Streams <a href="#" class="edit">&larr; what?</a></li>
					<?php if( is_numeric( $this->get( 'userid' ) ) ): ?>
						<li>
							<?php if( $this->content['title'] == 'hybrid' ): ?>
							Hybrid &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ) . '/' . $this->get( 'username' ); ?>">Hybrid</a>
							<?php endif; ?>
						</li>
						<li>
							<?php if( $this->content['title'] == 'unread' ): ?>
							Unread &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ) . '/' . $this->get( 'username' ); ?>/unread">Unread</a>
							<?php endif; ?>
						</li>
						<li>Discover <span class="type">coming soon</span></li>
						<li>
							<?php if( $this->content['title'] == 'popular' ): ?>
							Popular &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ) . '/' . $this->get( 'username' ); ?>/popular">Popular</a>
							<?php endif; ?>
						</li>
						<li>
							<?php if( $this->content['title'] == 'newest' ): ?>
							Newest &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ) . '/' . $this->get( 'username' ); ?>/newest">Newest</a>
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

					<?php if( $this->get( 'streams' ) ): foreach( $this->get( 'streams' ) as $stream ): ?>
						<li>
							<?php if( $this->get( 'streamid' ) == $stream['id'] ): ?>
							<?php echo $stream['name']; ?> &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/<?php echo $stream['id']; ?>"><?php echo $stream['name']; ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; if( $mod_user->session_login() ): ?>

						<li>
							<a class="edit" href="<?php echo $c_config['root']; ?>/user/settings/streams">add streams</a>
						</li>

					<?php endif; endif; ?>
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

				<ul>
					<li class="title">Sources<?php echo $this->get( 'userid' ) == $mod_user->session_userid() ? ' <a href="#" class="edit">edit</a>' : ''; ?></li>
					<?php if( $this->get( 'sources' ) ): ?>
					<?php foreach( $this->get( 'sources' ) as $source ): ?>
						<li class="source<?php echo $source['id'] == $this->get( 'source_id' ) ? ' active': ''; ?>">
							<a href="<?php echo $c_config['root']; ?>/source/<?php echo $source['id']; ?>">
								<img src="http://www.google.com/s2/favicons?domain=<?php echo $source['source_domain']; ?>" />
								<span class="first"><span><?php echo $source['source_title']; ?></span></span>
							</a>
						</li>
					<?php endforeach; ?>
					<?php else: ?>
						<li><a href="#">Add Sources &#187;</a></li>
					<?php endif; ?>
				</ul>
			</div><!--end left-->

			<div class="right">
				<div id="recommendations">
				<?php foreach( $this->get( 'recommends' ) as $recommend ): ?>
					<div>
						<a href="<?php echo $c_config['root']; ?>/article/<?php echo $recommend['id']; ?>" class="title"><img src="http://www.google.com/s2/favicons?domain=<?php echo $mod_data->domain_url( $recommend['site_url'] ); ?>" alt="" /> <strong><?php echo $recommend['title']; ?></strong></a>
						<span>was recommended by <a href="<?php echo $c_config['root']; ?>/user/<?php echo $recommend['user_id']; ?>/<?php echo $recommend['user_name']; ?>"><?php echo $recommend['user_name']; ?></a></span>
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