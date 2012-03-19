<html>
<head>
	<title>Login to Pulsefeed</title>
	<link rel="stylesheet" href="<?php echo $c_config['root']; ?>?load=css&type=basics,login" />
</head>
<body id="body_login">
	<div id="login">
		<h1><span>Pulsefeed <small>&rarr; login</small></span>Pulsefeed</h1>
		<div id="buttons">
			<p>Pulsefeed has <em>no</em> registration process, to save time simply login with a service below (you will be taken to their site to do this) / <a href="<?php echo $this->get( 'redir' ); ?>">go back &rarr;</a></p>
			<a class="login big" href="<?php echo $c_config['root']; ?>/process/fb-out">
				<img src="<?php echo $c_config['root']; ?>/inc/img/login/facebook.png" /> <em>Login with Facebook</em>
				<span>accounts can sync</span>
			</a>

			<a class="login big" href="<?php echo $c_config['root']; ?>/process/tw-out">
				<img src="<?php echo $c_config['root']; ?>/inc/img/login/twitter.png" /> <em>Login with Twitter</em>
				<span>accounts can sync</span>
			</a>

			<a class="login" href="<?php echo $c_config['root']; ?>/process/openid?openid=https://www.google.com/accounts/o8/id">
				<img src="<?php echo $c_config['root']; ?>/inc/img/login/google.png" /> Login with Google
			</a>

			<a class="login" href="<?php echo $c_config['root']; ?>/process/openid?openid=http://steamcommunity.com/openid">
				<img src="<?php echo $c_config['root']; ?>/inc/img/login/steam.png" /> Login with Steam
			</a>

			<a class="login" href="<?php echo $c_config['root']; ?>/process/openid?openid=https://me.yahoo.com">
				<img src="<?php echo $c_config['root']; ?>/inc/img/login/yahoo.png" /> Login with Yahoo
			</a>

			<a class="login" href="#">
				<img src="<?php echo $c_config['root']; ?>/inc/img/login/openid.png" /> <strike>Login with OpenID</strike>
			</a>
		</div><!--end buttons-->
	</div><!--end login-->
</body>
</html>