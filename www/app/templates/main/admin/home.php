<?php
?>

<div id="header">
	<div class="wrap">
		<div class="left">
			
		</div>

		<h1>Admin: Home</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main">
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left">
			<ul>
				<li class="title">Admin</li>
				<li>Home &rarr;</li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/permissions">Permissions</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/topics">Topics</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/memcache">Memcache</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/users">Users</a></li>
			</ul>
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->