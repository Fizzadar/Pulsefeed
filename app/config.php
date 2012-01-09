<?php
	/*
		file: app/config.php
		desc: configuration for feedbug app
	*/
	
	define( 'PULSEFEED_VERSION', '0.5.0' );

	//templates from hostnames!
	$templates = array(
		'facebook.feedbug.net' => 'facebook',
		'mobile.feedbug.net' => 'mobile'
	);

	//config array
	$mod_config = array(
		'dbhost' => 'localhost',
		'dbname' => 'feedbug',
		'dbuser' => 'root',
		'dbpass' => 'root',
		'template' => isset( $templates[$_SERVER['HTTP_HOST']] ) ? $templates[$_SERVER['HTTP_HOST']] : 'main',
		'api' => ( $_SERVER['HTTP_HOST'] == 'api.feedbug.net' or isset( $_GET['api'] ) ),
		'ajax' => ( $_SERVER['HTTP_HOST'] == 'ajax.feedbug.net' or isset( $_GET['ajax'] ) ),
		'useragent' => 'Pulsefeed / v.' . PULSEFEED_VERSION,
		'load' => array(
			//js & css
			'js' => 'inc/js',
			'css' => 'inc/css',
			//default
			'default' => 'home',
			//article
			'article' => 'article',
			//streams
			'user' => 'stream/user',
			'public' => 'stream/public',
			'source' => 'stream/source',
			//sources
			'source-browse' => 'source/browse',
			'source-add' => 'source/add',
			//login page
			'login' => 'user/login',
			'user-new' => 'user/new', //todo
			//settings
			'settings' => 'settings/core', //todo
			'settings-sources' => 'settings/sources', //todo
			'settings-collections' => 'settings/collections', //todo
			'settings-streams' => 'settings/streams', //todo
		),
		'process' => array(
			//user
			'logout' => 'user/logout',
			'openid' => 'user/openid',
			'fb-out' => 'user/fb_out',
			'tw-out' => 'user/tw_out',
			'login-facebook' => 'user/login',
			'login-twitter' => 'user/login',
			'login-openid' => 'user/login',
			'load' => 'user/load',
			'follow' => 'user/follow', //todo
			'unfollow' => 'user/unfollow', //todo
			//source
			'source-add' => 'source/add',
			'source-subscribe' => 'source/subscribe',
			'source-unsubscribe' => 'source/unsubscribe',
			//article
			'article-recommend' => 'article/recommend', //todo
			'article-collect' => 'article/collect', //todo
			'article-read' => 'article/read',
			//collection
			'collection-add' => 'collection/add', //todo
			'collection-delete' => 'collection/delete', //todo
			//settings
			'settings-save' => 'settings/save', //todo
			//cron
			'cron-update' => 'cron/update',
			'cron-popularity' => 'cron/popularity',
			'cron-cleanup' => 'cron/cleanup',
			'cron-popcalc' => 'cron/popcalc',
		),
		'libs' => array(
			//internal libs
			'mod_template' => 'template',
			'mod_message' => 'message',
			'mod_source' => 'source',
			'mod_article' => 'article',
			'mod_stream' => 'stream',
			'mod_stream_site' => 'stream_site',
			'mod_data' => 'data',
			'mod_cookie' => 'cookie',
			//external libs
			'SimplePie' => 'external/simplepie',
			'simple_html_dom' => 'external/simpledom',
			'idna_convert' => 'external/idna',
			'resize' => 'external/resize',
			'Readability' => 'external/Readability',
		),
		'messages' => array(
			//user
			'NewUser' => array( 'Welcome to Pulsefeed', 'success' ),
			'LoginServerError' => array( 'There was a problem contacting the account provider', 'warning' ),
			'FailedLogin' => array( 'We could not validate your login', 'warning' ),
			'AccountAdded' => array( 'External Account Added', 'success' ),
			'LoggedIn' => array( 'Sucessfully logged in', 'success' ),
			'ReLoggedIn' => array( 'Sucessfully logged in (again)', 'success' ),
			'LoggedOut' => array( 'You have logged out of Pulsefeed, come back soon!', 'success' ),
			'MustLogin' => array( 'To do tha you need to login!', 'warning' ),
			'NoPermission' => array( 'You do not have the required permissions to do that!', 'warning' ),
			//posting & requests
			'InvalidToken' => array( 'Invalid session token, please retry your last action', 'warning' ),
			'InvalidPost' => array( 'Incorrect info was sent during the last action, please try again', 'warning' ),
			'InvalidGet' => array( 'Invalid data was sent, please try again', 'warning' ),
			//not found/etc
			'NoFeedFound' => array( 'We couldn\'t find a feed on that website, please try another address or direct feed link', 'warning' ),
			//source
			'NoSource' => array( 'The source you are trying to subscribe to doesn\'t exist!', 'warning' ),
			'SourceSubscribed' => array( 'Successfully subscribed to that source', 'success' ),
			'SourceUnsubscribed' => array( 'Successfully unsubscribed to that source', 'success' ),
			//general errors
			'DatabaseError' => array( 'Unfortunately there was some kind of database error!', 'warning' ),
			'UnknownError' => array( 'An unknown error (eek!) occurred, please try again', 'warning' ),
			'NotFound' => array( 'The page you requested could not be found!', 'warning' ),
			//article
			'ArticleRead' => array( 'Article marked as read', 'success' ),
		),
		//how much each type of share/save is worth
		'popularity' => array(
			'hour' => 25, //points each hour of time is worth (removed)
			'recommend' => 20, //internal recommendations
			'facebook_shares' => 2,
			'facebook_comments' => 1,
			'delicious_saves' => 5,
			'twitter_links' => 2,
			'digg_diggs' => 3,
		),
		//expire time for articles (in hours)
		'article_expire' => 48,
	);
?>