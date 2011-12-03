<?php
	/*
		file: app/config.php
		desc: configuration for feedbug app
	*/
	
	define( 'FEEDBUG_VERSION', 0.1 );

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
		'ajax' => $_SERVER['HTTP_HOST'] == 'ajax.feedbug.net',
		'useragent' => 'Feedbug / v.' . FEEDBUG_VERSION,
		'load' => array(
			//js & css
			'js' => 'js',
			'css' => 'css',
			//default
			'default' => 'home',
			'article' => 'article',
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
			'cron' => 'cron',
		),
		'libs' => array(
			//internal libs
			'mod_template' => 'template',
			'mod_message' => 'message',
			'mod_source' => 'source',
			'mod_article' => 'article',
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
			//feed
			'NoFeedFound' => array( 'Sorry but we could not find a feed on that website, please try another address or direct feed link', 'warning' ),
			//source
			'NoSource' => array( 'The source you are trying to subscribe to doesn\'t exist!', 'warning' ),
			'SourceSubscribed' => array( 'Successfully subscribed to that source', 'success' ),
			//general errors
			'DatabaseError' => array( 'Unfortunately there was some kind of database error!', 'warning' ),
			'UnknownError' => array( 'An unknown error (eek!) occurred, please try again', 'warning' ),
			'NotFound' => array( 'The page you requested could not be found!', 'warning' ),
		),
	);
?>