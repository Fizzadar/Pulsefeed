<?php
	global $mod_user, $mod_token, $mod_cookie;
?>

<script type="text/javascript">
	pulsefeed.sbrowser = true;
	pulsefeed.sourceOffset = <?php echo $this->get( 'nextOffset' ); ?>;
	pulsefeed.sourceType = '<?php echo $this->get( 'sourceType' ); ?>';
</script>

<div id="header">
	<div class="wrap">
		<div class="left">
			<?php if( $mod_user->session_permission( 'AddSource' ) ): ?>
				<a class="button green" href="<?php echo $c_config['root']; ?>/sources/add">+ add new source</a>
			<?php endif; ?>
		</div>

		<h1><?php echo $this->get( 'sourceOrder' ) == 'mod_source.articles' ? 'Manage Your ' : 'Browse '; ?>Sources</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main wide">
		<span class="edit">
		<?php
			switch( $this->get( 'sourceOrder' ) ):
				case 'mod_source.time':
					echo 'newest sources';
					break;
				case 'mod_source.subscribers':
					echo 'popular sources';
					break;
				case 'mod_source.articles':
					echo 'sources you subscribe to';
					break;
			endswitch;
		?>
		</span>

		<div id="sources">
		<?php foreach( $this->get( 'sources' ) as $source ): ?>
			<div class="source">
				<h2>
					<img src="http://favicon.fdev.in/<?php echo $source['site_domain']; ?>" alt="" />

					<a href="<?php echo $c_config['root']; ?>/source/<?php echo $source['id']; ?>"><?php echo $source['site_title']; ?></a>

					<span class="url">
						<a target="_blank" href="<?php echo $source['site_url']; ?>"><?php echo $source['site_url']; ?></a>
					</span>
				</h2>

				<div class="meta">
					<span class="meta">
						Subscribers: <strong><?php echo $source['subscribers']; ?></strong> - Last Updated: <?php echo $source['time_ago']; ?>
					</span>
				</div><!--end meta-->

				<ul class="articles">
					<?php foreach( $source['articles'] as $article ): ?>
						<li><a href="<?php echo $c_config['root']; ?>/article/<?php echo $article['id']; ?>"><?php echo $article['title']; ?> &rarr;</a></li>
					<?php endforeach; if( count( $source['articles'] ) <= 0 ): ?>
						<li>This source has no articles!</li>
					<?php endif; ?>
				</ul><!--end articles-->

			<?php if( $mod_user->session_login() and $mod_user->session_permission( 'Subscribe' ) ): ?>
				<?php if( isset( $source['subscribed'] ) and is_numeric( $source['subscribed'] ) ): ?>
				<form action="<?php echo $c_config['root']; ?>/process/unsubscribe" method="post" class="source_subscribe">
					<input type="hidden" name="source_id" value="<?php echo $source['id']; ?>" />
					<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
					<input type="submit" value="Unsubscribe" class="button red" />
				</form>
				<?php else: ?>
				<form action="<?php echo $c_config['root']; ?>/process/subscribe" method="post" class="source_subscribe">
					<input type="hidden" name="source_id" value="<?php echo $source['id']; ?>" />
					<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
					<input type="submit" value="+ Subscribe" class="button green" />
				</form>
				<?php endif; ?>
			<?php endif; ?>
			</div>
		<?php endforeach; if( count( $this->get( 'sources' ) ) < 1 ): ?>
			<div class="item article level_1">
				<span class="content">
				<h2>There are no more sources :(</h2>
				<p>We couldn't find any more sources.</p>
				</span>
			</div>
		<?php endif; ?>
		</div><!--end sources-->

		<a class="morelink source_load_more" href="?offset=<?php echo $this->get( 'nextOffset' ); ?>">load more sources &darr;</a>
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left">
			<?php if( $mod_cookie->get( 'RecentStream' ) ): ?>
			<ul>
				<li><a href="<?php echo $mod_cookie->get( 'RecentStream' ); ?>">&larr; back to stream</a></li>
			</ul>
			<?php endif; ?>

			<ul>
				<li class="title">Views</li>
				<?php if( $mod_user->session_login() ): ?>
					<?php if( $this->get( 'sourceOrder' ) == 'mod_source.articles' ): ?>
						<li>My Sources &rarr;</li>
					<?php else: ?>
						<li><a href="<?php echo $c_config['root']; ?>/sources/me">My Sources</a></li>
					<?php endif; ?>
				<?php endif; ?>

				<?php if( $this->get( 'sourceOrder' ) == 'mod_source.subscribers' ): ?>
					<li>Popular Sources &rarr;</li>
				<?php else: ?>
					<li><a href="<?php echo $c_config['root']; ?>/sources">Popular Sources</a></li>
				<?php endif; ?>

				<?php if( $this->get( 'sourceOrder' ) == 'mod_source.time' ): ?>
					<li>Newest Sources &rarr;</li>
				<?php else: ?>
					<li><a href="<?php echo $c_config['root']; ?>/sources/new">Newest Sources</a></li>
				<?php endif; ?>
			</ul>

			<ul>
				<li class="title">Tags</li>
			</ul>
		</div><!--end left-->

		<div class="fix">
			<a href="#" class="top_link">
				&uarr; top
			</a>
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->