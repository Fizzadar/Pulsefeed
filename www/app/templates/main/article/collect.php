<?php 
	/*
		file: app/templates/main/article/collect.php
		desc: minimal collection list for no-js
	*/
	
	global $mod_token, $mod_cookie;

?>

<div id="header">
	<div class="wrap">
		<div class="left noborder">
			<a class="button blue" href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) : $c_config['root']; ?>">&larr; Back to Stream</a>
		</div>

		<h1>Collect Article #<?php echo $_GET['id']; ?></h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main">
		<form action="<?php echo $c_config['root']; ?>/process/article-collect" method="post">
			<label for="collection_id">Current Collection:</label>
			<select name="collection_id" id="collection_id">
				<option value="0">New (add name below)</option>
			<?php foreach( $this->get( 'collections' ) as $collection ): ?>
				<option value="<?php echo $collection['id']; ?>"><?php echo $collection['name']; ?></option>
			<?php endforeach; ?>
			</select>

			<label for="collection_name">New Collection:</label>
			<input type="text" name="collection_name" id="collection_name" />

			<input type="submit" value="Collect Article &#187;" />

			<input type="hidden" name="article_id" value="<?php echo $_GET['id']; ?>" />
			<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left noborder">
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->