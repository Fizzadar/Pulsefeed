<?php
	global $mod_token, $mod_user, $mod_cookie;
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
				<a class="button blue" href="<?php echo $c_config['root']; ?>/websites">Browse Websites</a>
			</div>

			<h1>Add Website</h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main">
			<form action="<?php echo $c_config['root']; ?>/process/website-add" method="post" class="half">
				<h2>Add Websites/RSS Feeds</h2>
				<p>Enter a website or feed address below, and we'll do our very best to add it to Pulsefeed &amp; subscribe you to it's updates.</p>

				<label for="source_url">Web/feed address:</label>
				<input type="text" name="source_url" id="source_url" value="http://" onclick="if( this.value == 'http://' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'http://'; }" />

				<input type="submit" value="Add to Pulsefeed &#187;" />
				<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			</form>

			<div class="midor">or</div>

			<form action="<?php echo $c_config['root']; ?>/process/website-opml" method="post" enctype="multipart/form-data" class="half">
				<h2>Upload OPML Files</h2>
				<p>Upload an OPML file, which contains a list of all your feeds. These can be exported from many RSS readers. <a target="_blank" href="http://en.wikipedia.org/wiki/OPML">Read more &rarr;</a></p>

				<label for="opml_file">An OPML File:</label>
				<input type="file" name="opml_file" id="opml_file" />

				<input type="submit" value="Upload &#187;" />
				<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			</form>
		</div><!--end main-->
	</div><!--end wrap-->

	<div id="sidebars">
		<div class="wrap">
			<div class="left">
				<ul>
					<li class="title">Browse</li>
					<li><a href="<?php echo $c_config['root']; ?>/topics">Topics</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/websites">Websites</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/collections">Collections</a></li>
				</ul>
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->