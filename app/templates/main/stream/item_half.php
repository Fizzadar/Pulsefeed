<?php
	global $c_config, $mod_data, $mod_user, $mod_token;
	$items = $this->get( 'currentStreamItems' );
	$key = $this->get( 'currentStreamKey' );
?>

<div class="item article half" id="item_<?php echo $key; ?>">
<?php foreach( $items as $item ): ?>
	<div class="posthalf<?php echo empty( $item['image_half'] ) ? '' : ' image'; ?>">
		<h2><a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>?f=<?php echo $key - 1; ?>"><?php echo $item['title']; ?></a></h2>
		
		<span class="content">
			<?php echo empty( $item['image_half'] ) ? $item['short_description'] : '<img src="' . $c_config['root'] . '/' . $item['image_half'] . '" />'; ?>
		</span>

		<div class="meta">
			<div class="details">
				<a href="<?php echo $c_config['root']; ?>/source/<?php echo $item['source_id']; ?>"><img src="http://www.google.com/s2/favicons?domain=<?php echo $item['source_domain']; ?>" alt="" /><strong><?php echo $item['source_title']; ?></strong></a> 
				<span>&rarr; <?php echo $mod_data->time_ago( $item['time'] ); ?></span><br />
				<?php if( $mod_user->session_login() ): ?>
				<span>
					<?php
						if( !$item['expired'] and $item['subscribed'] ):
							echo $item['unread'] ?
								'<form action="' . $c_config['root'] . '/?process=article-read" method="post">
									<input type="hidden" name="article_id" value="' . $item['id'] . '" />
									<input type="hidden" name="mod_token" value="' . $mod_token . '" />
									<input type="submit" value="Mark as read" />
								</form>' : 
								'Article read';
							
							echo ' - ';
						elseif( !$item['expired'] ):
						endif;
					?>
					<a href="#">Collect</a> - 
					<?php
						echo $item['recommended'] ?
							'You and ' . ( $item['recommendations'] - 1 ) . ' others recommend this' :
							'<form action="' . $c_config['root'] . '/?process=article-recommend" method="post">
								<input type="hidden" name="article_id" value="' . $item['id'] . '" />
								<input type="hidden" name="mod_token" value="' . $mod_token . '" />
								<input type="submit" value="Recommend" />
							</form> (' . $item['recommendations'] . ')';
					?>
				</span>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>