<div id="header">
	<div class="wrap">
		<div class="left">
			
		</div>

		<h1>Admin: Permissions</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main wide">
		<?php foreach( $this->get( 'ranks' ) as $rank ): ?>
			<ul class="permissions">
				<li><strong>#<?php echo $rank['id'] . ': ' . $rank['name']; ?></strong></li>
				<ul>
					<li>Permissions</li>
					<?php foreach( $rank['permission'] as $permission ): ?>
						<li class="green"><?php echo $permission; ?></li>
					<?php endforeach; if( empty( $rank['permission'] ) ): ?>
						<li class="red">None</li>
					<?php endif; ?>
				</ul>
				<ul>
					<li>Non-Permissions</li>
					<?php foreach( $rank['nopermission'] as $permission ): ?>
						<li class="red"><?php echo $permission; ?></li>
					<?php endforeach; if( empty( $rank['nopermission'] ) ): ?>
						<li class="red">None</li>
					<?php endif; ?>
				</ul>
			</ul>
		<?php endforeach; ?>
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left">
			<ul>
				<li class="title">Admin</li>
				<li><a href="<?php echo $c_config['root']; ?>/admin">Home</a></li>
				<li>Permissions &rarr;</li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/topics">Topics</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/memcache">Memcache</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/users">Users</a></li>
			</ul>
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->