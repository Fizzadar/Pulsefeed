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
				<label for="username">Username <small>(30 character max)</small>:</label>
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
				<h2>Linked Accounts</h2>
				<p>All the accounts can use to login to Pulsefeed. <strong>Facebook</strong> &amp; <strong>Twitter</strong> accounts can also sync with your article stream <a href="<?php echo $c_config['root']; ?>/login"><strong>Add another account &rarr;</strong></a></p>

				<?php foreach( $this->get( 'oauths' ) as $oauth ): ?>
					<strong><?php echo $oauth['provider']; ?></strong> &rarr; <?php echo $oauth['o_id']; ?>
					 <span class="edit inline"> - 
					 	<form action="<?php echo $c_config['root']; ?>/process/account-sync" method="post" class="inline">
					 		<input type="submit" class="edit" value="<?php echo $oauth['nosync'] ? 'start' : 'stop'; ?> sync" />
					 		<input type="hidden" name="o_id" value="<?php echo $oauth['o_id']; ?>" />
					 		<input type="hidden" name="provider" value="<?php echo $oauth['provider']; ?>" />
					 		<input type="hidden" name="mod_token" value="<?php echo $this->get( 'mod_token' ); ?>" />
					 		<input type="hidden" name="sync" value="<?php echo $oauth['nosync']; ?>" />
					 	</form> or 
					 	<form action="<?php echo $c_config['root']; ?>/process/account-delete" method="post" class="inline">
					 		<input type="submit" class="edit" value="delete" />
					 		<input type="hidden" name="type" value="oauth" />
					 		<input type="hidden" name="o_id" value="<?php echo $oauth['o_id']; ?>" />
					 		<input type="hidden" name="provider" value="<?php echo $oauth['provider']; ?>" />
					 		<input type="hidden" name="mod_token" value="<?php echo $this->get( 'mod_token' ); ?>" />
					 	</form>
					 </span>
					<br />
				<?php endforeach; if( count( $this->get( 'oids' ) ) > 0 ): ?>

					<br />
					OpenID's:<br />
					<?php foreach( $this->get( 'oids' ) as $oid ): $bits = parse_url( $oid['open_id'] ); ?>
						<strong><?php echo $bits['host']; ?></strong> &rarr; <?php echo $bits['path']; ?> 
						<span class="edit inline">- 
							<form action="<?php echo $c_config['root']; ?>/process/account-delete" method="post" class="inline">
								<input type="submit" class="edit" value="delete" />
								<input type="hidden" name="type" value="openid" />
								<input type="hidden" name="open_id" value="<?php echo $oid['open_id']; ?>" />
								<input type="hidden" name="mod_token" value="<?php echo $this->get( 'mod_token' ); ?>" />
							</form>
						</span>
						<br />
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div><!--end main-->
	</div><!--end wrap-->

	<div id="sidebars">
		<div class="wrap">
			<div class="left">
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->