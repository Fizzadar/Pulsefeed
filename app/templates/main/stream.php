<?php 
	/*
		file: app/templates/main/stream.php
		desc: stream template for main design
	*/
	
	global $mod_data, $mod_user, $mod_cookie;
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
				<a class="top">stream options &darr;</a>
				<ul>
					<li>
						<?php if( isset( $_GET['list'] ) ): ?>
						<a href="<?php echo str_replace( '?list', '', $_SERVER['REQUEST_URI'] ); ?>">Dynamic style</a>
						<?php else: ?>
						<a href="<?php echo $_SERVER['REQUEST_URI']; ?>?list">List style</a>
						<?php endif; ?>
				</ul>
			</div>

			<div class="right">
				<a class="top">&darr; add source</a>
				<ul>
					<li><a href="#">Browse Sources</a></li>
					<li><a href="#">Add a source</a></li>
				</ul>
			</div>

			<h1><?php echo $this->get( 'pageTitle' ); ?></h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main" id="stream">
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
						case 'user':
							echo 'popular articles';
							break;
						case 'newest':
						case 'source':
							echo 'latest articles';
							break;
						case 'unread':
							echo 'latest unread articles';
							break;
					endswitch;
					echo isset( $_GET['list'] ) ? ', list style' : '';
				endif;
			?>
			</span>

			<?php
				//loop our items (layers within the stream)
				foreach( $this->content['stream'] as $key => $item ):
					if( isset( $_GET['list'] ) ):
						$this->add( 'currentStreamItems', $item['items'] );
						$this->add( 'currentStreamKey', $key );
						$this->load( 'stream/item_wide_list' );
					else:
						$this->add( 'currentStreamItems', $item['items'] );
						$this->add( 'currentStreamKey', $key );
						$this->load( 'stream/' . $item['template'] );
					endif;
				endforeach;

				//empty stream
				if( count( $this->content['stream'] ) < 1 ):
			?>
				<div class="item article level_1">
					<span class="content">
					<h2>This stream is (currently) empty</h2>
					<p>For whatever reason, there is no content to be displayed here now, please try one of the other streams listed on the left.</p>

					<p>If this is one of your streams, it's probably empty because you don't have enough news sources or the ones you have don't update often enough. <a href="#">Manage your sources here</a>.</p>
					</span>
				</div>
			<?php
				endif;
			?>
		</div><!--end main-->
	</div><!--end content-->

<div id="sidebars">

		<div class="wrap">

			<div class="left" id="leftbar">
				<ul>
					<li class="title">Streams <a href="#" class="edit">&larr; what?</a></li>
					<?php if( $this->get( 'userid' ) ): ?>
						<li>
							<?php if( $this->content['title'] == 'hybrid' ): ?>
							<u>Hybrid</u> &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?><?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Hybrid</a>
							<?php endif; ?>
						</li>
						<li>
							<?php if( $this->content['title'] == 'unread' ): ?>
							<u>Unread</u> &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/unread<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Unread</a>
							<?php endif; ?>
						</li>
						<li>Discover <span class="type">coming soon</span></li>
						<li>
							<?php if( $this->content['title'] == 'popular' ): ?>
							<u>Popular</u> &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/popular<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Popular</a>
							<?php endif; ?>
						</li>
						<li>
							<?php if( $this->content['title'] == 'newest' ): ?>
							<u>Newest</u> &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/newest<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Newest</a>
							<?php endif; ?>
						</li>
					<?php endif; ?>

					<li>
						<?php if( $this->get( 'title' ) == 'public' ): ?>
							<u>All/Public</u> &rarr;
						<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/public<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">All/Public</a>
						<?php endif; ?>
					</li>

					<?php if( $this->get( 'streams' ) ): foreach( $this->get( 'streams' ) as $stream ): ?>
						<li>
							<?php if( $this->get( 'streamid' ) == $stream['id'] ): ?>
							<u><?php echo $stream['name']; ?></u> &rarr;
							<?php else: ?>
							<a href="<?php echo $c_config['root']; ?>/user/<?php echo $this->get( 'userid' ); ?>/<?php echo $stream['id']; ?><?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>"><?php echo $stream['name']; ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; if( $mod_user->session_login() ): ?>

						<li>
							<a class="edit" href="#">add streams</a>
						</li>

					<?php endif; endif; ?>
				</ul>

				<?php if( $mod_user->session_login() ): ?>
				<ul>
					<li class="title">Collections <a href="#" class="edit">edit</a></li>
					<li>
						<a href="#">Queenstown</a> <span class="type">10 articles</span>
					</li>
					<li><a href="#">Web Design</a> <span class="type">5 articles</span></li>
				</ul>
				<?php endif; ?>

				<?php if( $this->get( 'sources' ) ): ?>
				<ul>
					<li class="title">Sources <a href="#" class="edit">edit</a></li>
					<?php foreach( $this->get( 'sources' ) as $source ): ?>
						<li class="source">
							<a href="<?php echo $c_config['root']; ?>/source/<?php echo $source['id']; ?>">
								<img src="http://www.google.com/s2/favicons?domain=<?php echo $source['source_domain']; ?>" />
								<span class="first"><span><?php echo $source['source_title']; ?></span></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div><!--end left-->

			<div class="right">
				<div class="biglinks">
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

				<img src="<?php echo $c_config['root']; ?>/inc/img/ads/234x60.gif" alt="" />
			</div><!--end right-->

		</div><!--end wrap-->
	</div><!--end sidebars-->