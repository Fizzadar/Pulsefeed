<?php
	global $mod_user, $mod_cookie;
	$settings = $this->get( 'settings' ) ? $this->get( 'settings' ) : array();
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
			</div>

			<h1>Your Settings</h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main">
			<form action="<?php echo $c_config['root']; ?>/process/settings" method="post" class="left half">
				<label for="username">Username <small>(30 character max, alphanumeric)</small>:</label>
				<input type="text" name="username" id="username" value="<?php echo $settings['name']; ?>" maxlength="30" />

				<label for="email">Email <small>(optional)</small>:</label>
				<input type="text" name="email" id="email" value="<?php echo $settings['email']; ?>" />

				<p><em>Note:</em> <strong>email updates don't currently run</strong></p>
				<label class="checkbox" for="daily_email">Daily Email Digest:</label>
				<input type="checkbox" name="daily_email" id="daily_email" <?php echo $settings['daily_email'] ? 'checked' : ''; ?>/>

				<label class="checkbox" for="weekly_email">Weekly Email Digest:</label>
				<input type="checkbox" name="weekly_email" id="weekly_email" <?php echo $settings['weekly_email'] ? 'checked' : ''; ?>/>

				<input type="submit" value="Update Settings &#187;" />
				<input type="hidden" name="mod_token" value="<?php echo $this->get( 'mod_token' ); ?>" />
			</form>

			<div class="right half">
			</div>
		</div><!--end main-->
	</div><!--end wrap-->

	<div id="sidebars">
		<div class="wrap">
			<div class="left">
				<li class="title">Settings</li>
				<ul>
					<li>Profile Setup &rarr;</li>
					<li><a href="<?php echo $c_config['root']; ?>/settings/accounts">Accounts</a></li>
					<?php if( $mod_user->session_permission( 'Debug' ) ): ?><li><a href="<?php echo $c_config['root']; ?>/settings/data">Data</a></li><?php endif; ?>
				</ul>
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->