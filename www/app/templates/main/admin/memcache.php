<?php global $mod_data; ?>
<div id="header">
	<div class="wrap">
		<div class="left">
			
		</div>

		<h1>Admin: Memcaches</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main wide">
		<?php foreach( $this->get( 'memcaches' ) as $server => $memcache ): if( !$memcache ): ?>
			<ul class="permissions">
				<li>Server: <strong><?php echo $server; ?></strong></li>
				<li><strong>Offline</strong></li>
			</ul>
		<?php else: ?>
			<ul class="permissions">
				<li>Server: <strong><?php echo $server; ?></strong><?php foreach( $this->content['memcache_names'][$server] as $name ): echo ', ' . $name; endforeach; ?></li>
				<ul>
					<li>Started</li>
					<li>Current Items</li>
					<li>Total Items In</li>
					<li>Evicted Items</li>
					<li>Get Count</li>
					<li>Set Count</li>
					<li>Get Hits</li>
					<li>Get Misses</li>
					<li>Hit Percentage</li>
					<li>Space</li>
				</ul>
				<ul>
					<li><strong><?php echo $mod_data->time_ago( time() - $memcache['uptime'] ); ?></strong></li>
					<li><strong><?php echo number_format( $memcache['curr_items'] ); ?></strong></li>
					<li><strong><?php echo number_format( $memcache['total_items'] ); ?></strong></li>
					<li><strong><?php echo $memcache['evictions']; ?></strong></li>
					<li><strong><?php echo number_format( $memcache['cmd_get'] ); ?></strong></li>
					<li><strong><?php echo number_format( $memcache['cmd_set'] ); ?></strong></li>
					<li><strong><?php echo number_format( $memcache['get_hits'] ); ?></strong></li>
					<li><strong><?php echo number_format( $memcache['get_misses'] ); ?></strong></li>
					<li><strong><?php echo round( ( $memcache['get_hits'] + 1 ) / ( $memcache['cmd_get'] + 1 ) * 100, 3 ); ?>%</strong></li>
					<li><strong><?php echo round( $memcache['bytes'] / 1024 / 1024, 2 ) . '</strong>mb<strong> / ' . round( $memcache['limit_maxbytes'] / 1024 / 1024, 2 ); ?></strong>mb</li>
				</ul>
			</ul>
		<?php endif; endforeach; ?>

	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left">
			<ul>
				<li class="title">Admin</li>
				<li><a href="<?php echo $c_config['root']; ?>/admin">Home</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/permissions">Permissions</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/topics">Topics</a></li>
				<li>Memcache &rarr;</li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/users">Users</a></li>
			</ul>
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->