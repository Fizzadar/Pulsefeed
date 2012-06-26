<?php
	global $mod_user, $mod_cookie;
	$settings = $this->get( 'settings' ) ? $this->get( 'settings' ) : array();
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
			</div>

			<h1>Your Settings / Accounts</h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main">
			<div class="left half">
				<h2>Linked Accounts</h2>
				<p><strong>Facebook</strong> &amp; <strong>Twitter</strong> accounts can sync with your article stream</p>

				<?php foreach( $this->get( 'oauths' ) as $oauth ): ?>
					<strong><img src="/inc/img/icons/share/<?php echo $oauth['provider']; ?>.png" alt="" class="left favicon" /> <?php echo $oauth['provider']; ?></strong> &rarr; <?php echo $oauth['o_id']; ?>
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
				<?php endforeach; ?>

				<br />
				<a class="button twitter" href="<?php echo $c_config['root']; ?>/process/tw-out">+ Add Twitter</a> 
				<a class="button facebook" href="<?php echo $c_config['root']; ?>/process/fb-out">+ Add Facebook</a>
			</div><!--end left half-->

			<div class="right half">
				<h2>Linked OpenID's</h2>
				<p>OpenID's can be used to login to Pulsefeed, they don't sync articles</p>

				<?php if( count( $this->get( 'oids' ) ) > 0 ): ?>
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

				<br />
				<a class="button green" href="<?php echo $c_config['root']; ?>/login">+ Add OpenID</a>
			</div><!--end right-->
		</div><!--end main-->
	</div><!--end wrap-->

	<div id="sidebars">
		<div class="wrap">
			<div class="left">
				<li class="title">Settings</li>
				<ul>
					<li><a href="<?php echo $c_config['root']; ?>/settings">Profile Setup</a></li>
					<li>Accounts &rarr;</li>
					<?php if( $mod_user->session_permission( 'Debug' ) ): ?><li><a href="<?php echo $c_config['root']; ?>/settings/data">Data</a></li><?php endif; ?>
				</ul>
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->