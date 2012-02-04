<?php global $mod_user, $mod_message, $mod_cookie, $mod_token; ?>
<!DOCTYPE html>
<html>
<head>

	<!--
		  _____        _           __              _ 
		 |  __ \      | |         / _|            | |
		 | |__) |_   _| |___  ___| |_ ___  ___  __| |
		 |  ___/| | | | / __|/ _ \  _/ _ \/ _ \/ _` |
		 | |    | |_| | \__ \  __/ ||  __/  __/ (_| |
		 |_|     \__,_|_|___/\___|_| \___|\___|\__,_|

		 hello there! welcome to the source ^^
	-->

	<!--meta-->
	<title>Pulsefeed <?php echo $this->get( 'pageTitle' ) ? ' &rarr; ' . $this->get( 'pageTitle' ) : ''; ?></title>
	<meta charset="UTF-8" />
	
	<!--favicon-->
	<link rel="icon" href="<?php echo $c_config['root']; ?>/inc/img/favicon.png" />

	<!--style-->
	<link rel="stylesheet" href="<?php echo $c_config['root']; ?>?load=css&type=basics,core,main" media="all" />
	<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
	
	<!--scripts-->
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>?load=js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript">
		$( document ).ready( function() {
			$( 'div.message' ).click( function( el ) {
				$( this ).slideUp( 200 );
			});
			function messageslide() {
				$( 'div.message' ).slideUp( 200 );
			}
			setTimeout( messageslide, 2000 );
		});
	</script>
</head>
<body>
	<div id="fb-root"></div>

	<div id="top">
		<div class="wrap">
			<?php if( $this->get( 'externalHeader' ) ): ?>
				<h3 class="external">
					<span><small>&larr; back</small> Pulsefeed</span>
					<a href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) . '#item_' . $_GET['f'] : $c_config['root']; ?>"><small>&larr; back</small> Pulsefeed</a>
				</h3>

				<form id="search">
					<input type="text" id="s" name="s" value="Search Pulsefeed..." onclick="if( this.value == 'Search Pulsefeed...' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'Search Pulsefeed...'; }" />
					<input type="submit" id="submit" value="Search &rarr;" />
				</form>

				<ul id="external">
					<li>
						<form action="<?php echo $c_config['root']; ?>/?process=article-<?php echo $this->content['article']['recommended'] == NULL ? 'recommend' : 'unrecommend'; ?>" method="post">
							<input type="hidden" name="article_id" value="<?php echo $this->content['article']['id']; ?>" />
							<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
							<button type="submit">
								<img src="<?php echo $c_config['root']; ?>/inc/img/icons/<?php echo $this->content['article']['recommended'] == NULL ? 'recommend' : 'recommended'; ?>.png" alt="" />
								<?php echo $this->content['article']['recommended'] == NULL ? 'Recommend' : 'UnRecommend'; ?>
							</button>
						</form>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/collect.png" alt="" />
							Collect
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo $c_config['root']; ?>/inc/img/icons/tag.png" alt="" />
							Tag
						</a>
					</li>
				</ul>
			<?php else: ?>
				<h3>
					<span>Pulsefeed</span>
					<a href="<?php echo $c_config['root'] . ( $mod_user->session_login() ? '/user/' . $mod_user->session_userid() . '/' . $mod_user->session_username() : '' ); ?>">Pulsefeed</a>
				</h3>

				<form id="search">
					<input type="text" id="s" name="s" value="Search Pulsefeed..." onclick="if( this.value == 'Search Pulsefeed...' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'Search Pulsefeed...'; }" />
					<input type="submit" id="submit" value="Search &rarr;" />
				</form>
			<?php endif; ?>

			<ul id="account"<?php echo $mod_user->session_login() ? '' : ' class="nologin"'; ?>>
			<?php if( $mod_user->session_login() ): ?>
				<ul>
					<li class="title">News</li>
					<li><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid() . '/' . $mod_user->session_username(); ?>">Your News Stream</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/sources">Browse Sources</a></li>

					<li class="title">Account</li>
					<li><a href="<?php echo $c_config['root']; ?>/user/settings">Settings</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/sources/me">Sources</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/collections/me">Collections</a></li>

					<li class="title">Other Bits</li>
					<li><a href="#">Help</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/process/logout">Logout</a></li>
				</ul>
				<li class="top">
					<a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid() . '/' . $mod_user->session_username(); ?>"><strong>latest updates</strong></a>
					<span><?php echo $mod_user->session_username(); ?> &darr;</span>
					<!--<img src="https://graph.facebook.com/1618950042/picture" alt="" />-->
				</li>
			<?php else : ?>
				<li class="top">
					<span><a href="<?php echo $c_config['root']; ?>/login"><strong>Login / Register</strong></a></span>
				</li>
			<?php endif; ?>
			</ul>
		</div><!--end wrap-->
	</div><!--end top-->

	<div id="messages">
	<?php foreach( $mod_message->get() as $message ): ?>
		<div class="message <?php echo $message[1]; ?>">
			<div class="wrap">
				<?php echo $message[0]; ?>
				<span class="right">hide message</span>
			</div>
		</div>
	<?php endforeach; ?>
	</div><!--end messages-->