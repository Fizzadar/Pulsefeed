<?php
	global $mod_user, $mod_cookie;
	$settings = $this->get( 'settings' ) ? $this->get( 'settings' ) : array();
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
			</div>

			<h1>Your Settings / Collections</h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main">
			<div class="left half">
				<h2>Collections</h2>
				<p>To create collections, simply collect an article</p>

				<?php foreach( $this->get( 'collections' ) as $collection ): ?>
					<a href="<?php echo $c_config['root']; ?>/collection/<?php echo $collection['id']; ?>"><?php echo $collection['name']; ?></a>
					<span class="edit inline"> <?php echo $collection['articles']; ?> articles - 
						<form action="<?php echo $c_config['root']; ?>/process/collection-delete" method="post" class="inline">
					 		<input type="submit" class="edit" value="delete" />
					 		<input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>" />
					 		<input type="hidden" name="mod_token" value="<?php echo $this->get( 'mod_token' ); ?>" />
					 	</form>
					 </span>
				 	<br />
				<?php endforeach; ?>
			</div>
		</div><!--end main-->
	</div><!--end wrap-->

	<div id="sidebars">
		<div class="wrap">
			<div class="left">
				<li class="title">Settings</li>
				<ul>
					<li><a href="<?php echo $c_config['root']; ?>/settings">Profile Setup</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/settings/accounts">Accounts</a></li>
					<li>Collections &rarr;</li>
				</ul>
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->