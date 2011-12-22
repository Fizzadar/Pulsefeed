<?php 
	/*
		file: app/templates/main/stream.php
		desc: stream template for main design
	*/
	
	global $mod_data, $mod_user, $mod_cookie;

	//template funcs
	function item_wide( $items, $key = 2 ) {
		global $c_config, $mod_data, $mod_user;
		$item = $items[0];
?>
	<div class="item article level_<?php echo $key + 1; ?>">
		<h2><a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a></h2>
		<span class="content">
			<?php if( !empty( $item['image_quarter'] ) ): ?>
			<img class="thumb<?php echo ( $key % 2 ) ? ' alt' : ''; ?>" src="<?php echo $c_config['root']; ?>/<?php echo $item['image_quarter']; ?>" alt="" />
			<?php endif; ?>
			<?php echo $item['description']; ?>
		</span>
		<div class="meta">
			<div class="details">
				<a href="<?php echo $c_config['root']; ?>/source/<?php echo $item['source_id']; ?>">
					<img src="http://www.google.com/s2/favicons?domain=<?php echo $item['source_domain']; ?>" alt="" /><strong><?php echo $item['source_title']; ?></strong>
				</a> <span>&rarr; <?php echo $mod_data->time_ago( $item['time'] ); ?>
				<?php echo $mod_user->check_permission( 'Debug' ) ? ' (poptime: ' . $item['popularity_time'] . ', pop: ' . $item['popularity'] . ')' : ''; ?></span><br />
				<span>
					<a href="#">Mark as read</a> - 
					<a href="#">Collect</a> - 
					<?php echo $item['recommended'] ? 'You and ' . ( $item['recommendations'] - 1 ) . ' others recommend this' : '<a href="#">Recommend</a> (' . $item['recommendations'] . ')'; ?>
				</span>
			</div>
			<ul class="similar">
			</ul>
		</div>
	</div>
<?php
	}

	function item_wide_image( $items, $key ) {
		global $c_config, $mod_data, $mod_user;
		$item = $items[0];
?>
	<div class="item article wide_image level_<?php echo $key + 1; ?>">
		<span class="content">
			<img src="<?php echo $c_config['root']; ?>/<?php echo $item['image_wide']; ?>" alt="" />
		</span>
		<h2><a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a></h2>
		<div class="meta">
			<div class="details">
				<a href="<?php echo $c_config['root']; ?>/source/<?php echo $item['source_id']; ?>">
					<img src="http://www.google.com/s2/favicons?domain=<?php echo $item['source_domain']; ?>" alt="" /><strong><?php echo $item['source_title']; ?></strong>
				</a> <span>&rarr; <?php echo $mod_data->time_ago( $item['time'] ); ?>
				<?php echo $mod_user->check_permission( 'Debug' ) ? ' (poptime: ' . $item['popularity_time'] . ', pop: ' . $item['popularity'] . ')' : ''; ?></span><br />
				<span>
					<a href="#">Mark as read</a> - 
					<a href="#">Collect</a> - 
					<?php echo $item['recommended'] ? 'You and ' . ( $item['recommendations'] - 1 ) . ' others recommend this' : '<a href="#">Recommend</a> (' . $item['recommendations'] . ')'; ?>
				</span>
			</div>
		</div>
	</div>
<?php
	}

	function item_wide_list( $items, $key = 2 ) {
		global $c_config, $mod_data, $mod_user;
		foreach( $items as $item ):
?>
	<div class="item article level_<?php echo $key + 1; ?>">
		<h2><a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a></h2>
		<span class="content">
			<?php echo $item['short_description']; ?>
		</span>
		<div class="meta">
			<div class="details">
				<a href="<?php echo $c_config['root']; ?>/source/<?php echo $item['source_id']; ?>">
					<img src="http://www.google.com/s2/favicons?domain=<?php echo $item['source_domain']; ?>" alt="" /><strong><?php echo $item['source_title']; ?></strong>
				</a> <span>&rarr; <?php echo $mod_data->time_ago( $item['time'] ); ?>
				<?php echo $mod_user->check_permission( 'Debug' ) ? ' (poptime: ' . $item['popularity_time'] . ', pop: ' . $item['popularity'] . ')' : ''; ?></span><br />
				<span>
					<a href="#">Mark as read</a> - 
					<a href="#">Collect</a> - 
					<?php echo $item['recommended'] ? 'You and ' . ( $item['recommendations'] - 1 ) . ' others recommend this' : '<a href="#">Recommend</a> (' . $item['recommendations'] . ')'; ?>
				</span>
			</div>
			<ul class="similar">
			</ul>
		</div>
	</div>
<?php
		endforeach;
	}

	function item_half( $items, $key ) {
		global $c_config, $mod_data, $mod_user;
?>
	<div class="item article half">
	<?php foreach( $items as $item ): ?>
		<div class="posthalf">
			<h2><a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a></h2>
			<span class="content">
				<?php if( !empty( $item['image_quarter'] ) ): ?>
				<img class="thumb" src="<?php echo $c_config['root']; ?>/<?php echo $item['image_quarter']; ?>" alt="" />
				<?php endif; ?>
				<?php echo $item['short_description']; ?>
			</span>
			<div class="meta">
				<div class="details">
					<a href="<?php echo $c_config['root']; ?>/source/<?php echo $item['source_id']; ?>">
						<img src="http://www.google.com/s2/favicons?domain=<?php echo $item['source_domain']; ?>" alt="" /><strong><?php echo $item['source_title']; ?></strong>
					</a> <span>&rarr; <?php echo $mod_data->time_ago( $item['time'] ); ?>
					<?php echo $mod_user->check_permission( 'Debug' ) ? ' (poptime: ' . $item['popularity_time'] . ', pop: ' . $item['popularity'] . ')' : ''; ?></span><br />
					<span>
						<a href="#">Mark as read</a> - 
						<a href="#">Collect</a> - 
						<?php echo $item['recommended'] ? 'You and ' . ( $item['recommendations'] - 1 ) . ' others recommend this' : '<a href="#">Recommend</a> (' . $item['recommendations'] . ')'; ?>
					</span>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
<?php	
	}

	function item_half_image( $items, $key ) {
		global $c_config, $mod_data, $mod_user;
?>
	<div class="item article half image">
	<?php foreach( $items as $item ): ?>
		<div class="posthalf">
			<span class="content">
				<img src="<?php echo $c_config['root']; ?>/<?php echo $item['image_half']; ?>" alt="" />
			</span>
			<h2><a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a></h2>
			<div class="meta">
				<div class="details">
					<a href="<?php echo $c_config['root']; ?>/source/<?php echo $item['source_id']; ?>">
						<img src="http://www.google.com/s2/favicons?domain=<?php echo $item['source_domain']; ?>" alt="" /><strong><?php echo $item['source_title']; ?></strong>
					</a> <span>&rarr; <?php echo $mod_data->time_ago( $item['time'] ); ?>
					<?php echo $mod_user->check_permission( 'Debug' ) ? ' (poptime: ' . $item['popularity_time'] . ', pop: ' . $item['popularity'] . ')' : ''; ?></span><br />
					<span>
						<a href="#">Mark as read</a> - 
						<a href="#">Collect</a> - 
						<?php echo $item['recommended'] ? 'You and ' . ( $item['recommendations'] - 1 ) . ' others recommend this' : '<a href="#">Recommend</a> (' . $item['recommendations'] . ')'; ?>
					</span>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
<?php	
	}

	function item_third_image( $items, $key ) {
		global $c_config, $mod_data, $mod_user;
?>
	<div class="item images third">
	<?php foreach( $items as $item ): ?>
		<a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>">
			<img src="<?php echo $c_config['root']; ?>/<?php echo $item['image_third']; ?>" alt="" />
			<span><?php echo $item['title']; ?></span>
		</a>
	<?php endforeach; ?>
	</div>
<?php	
	}

	function item_quarter_image( $items, $key ) {
		global $c_config, $mod_data, $mod_user;
?>
	<div class="item images">
	<?php foreach( $items as $item ): ?>
		<a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>">
			<img src="<?php echo $c_config['root']; ?>/<?php echo $item['image_quarter']; ?>" alt="" />
			<span><?php echo $item['title']; ?></span>
		</a>
	<?php endforeach; ?>
	</div>
<?php	
	}
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
				<a class="top">stream options &darr;</a>
				<ul>
					<li><a href="#">Mark all Read</a></li>
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
					<li><a href="#">See Recommended</a></li>
					<li><a href="#">Browse Sources</a></li>
					<li><a href="#">Enter a website</a></li>
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
							echo 'popular articles';
							break;
						case 'newest':
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
				//print_r( $this->content['stream'] );
				foreach( $this->content['stream'] as $key => $item ):
					if( isset( $_GET['list'] ) ):
						item_wide_list( $item['items'], $key );
					elseif( function_exists( $item['template'] ) ):
						$item['template']( $item['items'], $key );
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
					<li>
						<?php if( $this->content['title'] == 'hybrid' ): ?>
						<u>Hybrid</u> &rarr;
						<?php else: ?>
						<a href="<?php echo $c_config['root']; ?>/user/<?php echo $_GET['id']; ?><?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Hybrid</a>
						<?php endif; ?>
					</li>
					<li>
						<?php if( $this->content['title'] == 'unread' ): ?>
						<u>Unread</u> &rarr;
						<?php else: ?>
						<a href="<?php echo $c_config['root']; ?>/user/<?php echo $_GET['id']; ?>/unread<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Unread</a>
						<?php endif; ?>
					</li>
					<li>Discover <span class="type">coming soon</span></li>
					<li>
						<?php if( $this->content['title'] == 'popular' ): ?>
						<u>Popular</u> &rarr;
						<?php else: ?>
						<a href="<?php echo $c_config['root']; ?>/user/<?php echo $_GET['id']; ?>/popular<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Popular</a>
						<?php endif; ?>
					</li>
					<li>
						<?php if( $this->content['title'] == 'newest' ): ?>
						<u>Newest</u> &rarr;
						<?php else: ?>
						<a href="<?php echo $c_config['root']; ?>/user/<?php echo $_GET['id']; ?>/newest<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">Newest</a>
						<?php endif; ?>
					</li>
					<li>
						<?php if( $this->content['title'] == 'public' ): ?>
						<u>All/Public</u> &rarr;
						<?php else: ?>
						<a href="<?php echo $c_config['root']; ?>/user/<?php echo $_GET['id']; ?>/public<?php echo isset( $_GET['list'] ) ? '?list' : ''; ?>">All/Public</a>
						<?php endif; ?>
					</li>
				</ul>

				<ul>
					<li class="title">Collections <a href="#" class="edit">edit</a></li>
					<li>
						<a href="#">Queenstown</a> <span class="type">10 articles</span>
					</li>
					<li><a href="#">Web Design</a> <span class="type">5 articles</span></li>
					<li>
						<a href="#" class="edit">show more...</a>
						<ul>
							<li><a href="#">Test Collection</a> <span class="type">15 articles</span></li>
							<li><a href="#">Test Collection</a> <span class="type">15 articles</span></li>
							<li><a href="#">Test Collection</a> <span class="type">15 articles</span></li>
						</ul>
					</li>
				</ul>

				<ul>
					<li class="title">Sources <a href="#" class="edit">edit</a></li>
					<?php foreach( $this->content['sources'] as $source ): ?>
						<li class="source">
							<a href="<?php echo $c_config['root']; ?>/source/<?php echo $source['id']; ?>">
								<img src="http://www.google.com/s2/favicons?domain=<?php echo $source['source_domain']; ?>" />
								<span class="first"><span><?php echo $source['source_title']; ?></span></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
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