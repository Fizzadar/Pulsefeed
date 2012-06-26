<?php
	global $mod_user, $mod_cookie;
	$settings = $this->get( 'settings' ) ? $this->get( 'settings' ) : array();
?>

	<div id="header">
		<div class="wrap">
			<div class="left">
			</div>

			<h1>Your Settings / Data</h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main">
			<p>Reset your account; a warning - <strong>this will erase</strong>:
				<ul>
					<li>All stream articles</li>
					<li>Reading &amp; sharing history</li>
					<li>Recommendations</li>
					<li>Subscribed topics &amp; websites</li>
					<li>Your collections</li>
					<li>Following users</li>
				</ul>
			</p>

			<p><em><strong>Note:</strong></em> this will <u>not</u> delete your linked twitter, facebook or openid accounts. To manage those: <a href="<?php echo $c_config['root']; ?>/settings/accounts">view accounts &rarr;</a></p>

			<form action="" method="post">
				<input type="submit" class="red" value="Reset Account &#187;" />
			</form>

			<span class="edit">You can also <a href="#" class="edit">delete your account</a></span>

			<div class="right half">
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
					<li>Data &rarr;</li>
				</ul>
			</div><!--end left-->
		</div><!--end wrap-->
	</div><!--end sidebars-->