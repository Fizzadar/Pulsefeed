<?php
	global $mod_token, $mod_user, $mod_cookie;
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
			</div>

			<h1>Enter Invite Code</h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main">
			<p>Enter a valid invite code to join the Pulsefeed Alpha.</p>

			<form action="<?php echo $c_config['root']; ?>/process/invite" method="post">
				<label for="invite_code">Invite Code:</label>
				<input type="text" name="invite_code" id="invite_code" />
				<input type="submit" value="Enter Code &#187;" />
				<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			</form>
		</div><!--end main-->
	</div><!--end wrap-->

	<div id="sidebars">
		<div class="wrap">
			<div class="left">
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->