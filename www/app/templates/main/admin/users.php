<div id="header">
	<div class="wrap">
		<div class="left">
			
		</div>

		<h1>Admin: Users</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main wide">
		<table width="100%" cellborder="0" cellspacing="0" class="admin">
			<thead>
				<tr>
					<th width="10%">ID</th>
					<th width="30%">Username</th>
					<th width="10%">Group</th>
					<th width="30%">Email</th>
					<th width="20%">Controls</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $this->get( 'users' ) as $user ): ?>
				<tr>
					<td><?php echo $user['id']; ?></td>
					<td><?php echo $user['name']; ?></td>
					<td><?php echo $user['group']; ?></td>
					<td><?php echo $user['email']; ?></td>
					<td><strike><a href="#">Login</a></strike> - <strike><a href="#">Remove</a></strike></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
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
				<li><a href="<?php echo $c_config['root']; ?>/admin/memcache">Memcache</a></li>
				<li>Users &rarr;</li>
			</ul>
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->