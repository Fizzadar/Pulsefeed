<?php
	global $c_config, $mod_data, $mod_user;
	$items = $this->get( 'currentStreamItems' );
	$key = $this->get( 'currentStreamKey' );

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
?>