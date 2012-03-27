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
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/pulsefeed.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/message.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.stream.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.frame.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/api.page.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/template.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/design.js"></script>
	<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/queue.js"></script>
</head>
<body>
	<div id="top">
		<div class="wrap">
			<?php if( $this->get( 'externalHeader' ) ): ?>
				<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/frame.js"></script>
				<h3 class="external">
					<span><small>&larr;</small> Pulsefeed</span>
					<a href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) : $c_config['root']; ?>"><small>&larr;</small> Pulsefeed</a>
				</h3>

				<?php if( $mod_user->session_login() ): ?>
					<ul id="external">
						<?php if( $mod_user->session_permission( 'Recommend' ) ): ?>
						<li>
							<form action="<?php echo $c_config['root']; ?>/process/article-<?php echo $this->content['article']['liked'] == NULL ? 'like' : 'liked'; ?>" method="post" class="like_form_external">
								<input type="hidden" name="article_id" value="<?php echo $this->content['article']['id']; ?>" />
								<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
								<button type="submit">
									<img src="<?php echo $c_config['root']; ?>/inc/img/icons/<?php echo $this->content['article']['liked'] == NULL ? 'like' : 'liked'; ?>.png" alt="" />
									<span><?php echo $this->content['article']['liked'] == NULL ? 'Like' : 'Unlike'; ?></span>
								</button>
							</form>
						</li>
						<?php endif; ?>
						<?php if( $mod_user->session_permission( 'Collect' ) ): ?>
						<li>
							<a href="#">
								<img src="<?php echo $c_config['root']; ?>/inc/img/icons/collect.png" alt="" />
								Collect
							</a>
						</li>
						<?php endif; ?>
						<?php if( $mod_user->session_permission( 'AddTag' ) ): ?>
						<li>
							<a href="#">
								<img src="<?php echo $c_config['root']; ?>/inc/img/icons/tag.png" alt="" />
								Tag
							</a>
						</li>
						<?php endif; ?>
						<li>
							<a target="_blank" href="<?php echo $this->content['article']['end_url']; ?>">
								<img src="<?php echo $c_config['root']; ?>/inc/img/icons/original.png" alt="" />
								Original
							</a>
						</li>
					</ul>
				<?php endif; ?>

			<?php else: ?>
				<h3>
					<span>Pulsefeed</span>
					<a href="<?php echo $c_config['root']; ?>">Pulsefeed</a>
				</h3>

				<form id="search" action="<?php echo $c_config['root']; ?>/search" method="GET">
					<input type="text" id="q" name="q" value="Search Pulsefeed..." onclick="if( this.value == 'Search Pulsefeed...' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'Search Pulsefeed...'; }" />
					<input type="submit" id="submit" value="Search &rarr;" />

					<ul id="search_results">
						<li><a href="#">
							<span class="title">Title of blog post</span>
							<span class="type">Article</span>
						</a></li>
					</ul><!--end search_results-->
				</form>
			<?php endif; ?>

			<div id="devmessage"><a target="_blank" href="http://blog.pulsefeed.com/pulsefeed-is-currently-in-development/">[!] Pulsefeed is currently in development &rarr;</a></div>

			<ul id="account"<?php echo $mod_user->session_login() ? '' : ' class="nologin"'; ?>>
			<?php if( $mod_user->session_login() ): ?>
				<ul>
					<li class="title">News</li>
					<li><a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>">Your News Stream</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/sources">Browse Sources</a></li>

					<li class="title">Account</li>
					<li><a class="ajax" href="<?php echo $c_config['root']; ?>/settings">Settings</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/sources/me">Sources</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/collections/me">Collections</a></li>

					<li class="title">Other Bits</li>
					<li><a href="#">Help</a></li>
					<li><a href="#">Stats</a></li>
					<li><a href="<?php echo $c_config['root']; ?>/process/logout">Logout</a></li>

					<?php if( $mod_user->session_permission( 'Admin' ) ): ?>
						<li class="title">Secret Admin Zone</li>
						<li><a href="#">Home</a></li>
						<li><a href="#">Permissions</a></li>
					<?php endif; ?>
				</ul>
				<li class="top">
					<a href="<?php echo $c_config['root']; ?>/user/<?php echo $mod_user->session_userid(); ?>"><strong>latest updates</strong></a>
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

	<div id="ajaxbox">