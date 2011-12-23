<?php
	global $c_config, $mod_data, $mod_user;
	$items = $this->get( 'currentStreamItems' );
	$item = $items[0];
	$key = $this->get( 'currentStreamKey' );
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