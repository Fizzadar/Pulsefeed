<?php
	global $mod_token, $mod_user, $mod_cookie;
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
				<a class="button" href="<?php echo $c_config['root']; ?>/sources">browse sources</a>
			</div>

			<h1>Add a Source</h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main">
			<p>Enter a website or feed address below, and we'll do our very best to add it to Pulsefeed &amp; subscribe you to it's updates.</p>

			<form action="<?php echo $c_config['root']; ?>/?process=source-add" method="post">
				<label for="source_url">Web/feed address:</label>
				<input type="text" name="source_url" id="source_url" value="http://" onclick="if( this.value == 'http://' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'http://'; }" />
				<input type="submit" value="Add to Pulsefeed &#187;" />
				<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			</form>
		</div><!--end main-->
	</div><!--end wrap-->

	<div id="sidebars">
		<div class="wrap">
			<div class="left">
				<?php if( $mod_cookie->get( 'RecentStream' ) ): ?>
				<ul>
					<li><a href="<?php echo $mod_cookie->get( 'RecentStream' ); ?>">&larr; back to stream</a></li>
				</ul>
				<?php endif; ?>

				<ul>
					<li class="title">Places</li>
					<?php if( $mod_user->session_login() ): ?>
					<li><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>">My Streams</a></li>
					<?php endif; ?>
					<li><a href="<?php echo $c_config['root']; ?>/public">Public Stream</a></li>
				</ul>
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->