<?php
	/*
		file: app/config.php
		desc: configuration for feedbug app
	*/
	
	define( 'PULSEFEED_VERSION', 0.1 );

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
		'api' => $_SERVER['HTTP_HOST'] == 'api.feedbug.net',
		'ajax' => ( $_SERVER['HTTP_HOST'] == 'ajax.feedbug.net' or isset( $_GET['ajax'] ) ),
		'useragent' => 'Pulsefeed / v.' . PULSEFEED_VERSION,
		'load' => array(
			//js & css
			'js' => 'js',
			'css' => 'css',
			//default
			'default' => 'home',
			//article
			'article' => 'article',
			//user/stream
			'user' => 'user',
			//source list
			'source' => 'source',
		),
		'process' => array(
			//user
			'logout' => 'user/logout',
			'openid' => 'user/openid',
			'login-facebook' => 'user/login',
			'login-openid' => 'user/login',
			//source
			'source-add' => 'source/add',
			'source-subscribe' => 'source/subscribe',
			//article
			'article-recommend' => 'article/recommend',
			'article-collect' => 'article/collect',
			//collection
			'collection-new' => 'collection/new',
			//cron
			'cron-update' => 'cron/update',
			'cron-popularity' => 'cron/popularity',
			'cron-cleanup' => 'cron/cleanup',
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
		),
		'messages' => array(
			//user
			'NewUser' => array( 'Welcome to Feedbug', 'success' ),
			'FailedLogin' => array( 'We could not validate your login', 'warning' ),
			'OpenidAdded' => array( 'OpenID Added', 'success' ),
			'LoggedIn' => array( 'Sucessfully logged in', 'success' ),
			'LoggedOut' => array( 'You have logged out of Feedbug, come back soon!', 'success' ),
			'MustLogin' => array( 'To do tha you need to login!', 'warning' ),
			//posting & requests
			'InvalidToken' => array( 'Invalid session token, please retry your last action', 'warning' ),
			'InvalidPost' => array( 'Incorrect info was sent during the last action, please try again', 'warning' ),
			'InvalidGet' => array( 'Invalid data was sent, please try again', 'warning' ),
			//not found/etc
			'NoFeedFound' => array( 'Sorry but we could not find a feed on that website, please try another address or direct feed link', 'warning' ),
			//source
			'NoSource' => array( 'The source you are trying to subscribe to doesn\'t exist!', 'warning' ),
			'SourceSubscribed' => array( 'Successfully subscribed to that source', 'success' ),
			//general errors
			'DatabaseError' => array( 'Unfortunately there was some kind of database error!', 'warning' ),
			'UnknownError' => array( 'An unknown error (eek!) occurred, please try again', 'warning' ),
			'NotFound' => array( 'The page you requested could not be found!', 'warning' ),
		),
		//how much each type of share/save is worth
		'popularity' => array(
			'hour' => 6, //points each hour of time is worth (removed)
			'recommend' => 20, //internal recommendations
			'facebook_shares' => 1,
			'facebook_comments' => 0, //0 due to biased on blogs with fb comments?
			'delicious_saves' => 10,
			'twitter_links' => 5,
			'digg_diggs' => 7,
		),
	);
?>