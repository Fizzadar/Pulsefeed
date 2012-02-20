<?php
	/*
		file: app/config.php
		desc: configuration for pulsefeed app
	*/
	
	define( 'PULSEFEED_VERSION', '0.6.0' );

	//templates from hostnames!
	$templates = array(
		'mobile.pulsefeed.com' => 'mobile'
	);

	//config array
	$mod_config = array(
		'dbhost' => '127.0.0.1',
		'dbname' => 'feedbug',
		'dbuser' => 'root',
		'dbpass' => 'root',
		'template' => isset( $templates[$_SERVER['HTTP_HOST']] ) ? $templates[$_SERVER['HTTP_HOST']] : 'main',
		'api' => ( $_SERVER['HTTP_HOST'] == 'api.pulsefeed.com' or isset( $_GET['api'] ) ),
		'ajax' => ( $_SERVER['HTTP_HOST'] == 'ajax.pulsefeed.com' or isset( $_GET['ajax'] ) ),
		'useragent' => 'Pulsefeed / v.' . PULSEFEED_VERSION,
		'load' => array(
			'204' => '204',
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
			'tag' => 'stream/tag', //todo - can hide
			//sources
			'source-browse' => 'source/browse',
			'source-add' => 'source/add',
			//search
			'search' => 'search', //todo - must do
			//users
			'login' => 'user/login',
			'invite' => 'user/invite',
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
			'follow' => 'user/follow',
			'unfollow' => 'user/unfollow',
			'invite' => 'user/invite',
			//source
			'source-add' => 'source/add',
			'subscribe' => 'source/subscribe',
			'unsubscribe' => 'source/unsubscribe',
			'source-tag' => 'source/tag', //todo
			'source-untag' => 'source/untag', //todo
			//article
			'article-recommend' => 'article/recommend',
			'article-unrecommend' => 'article/unrecommend',
			'article-collect' => 'article/collect', //todo
			'article-read' => 'article/read',
			'article-tag' => 'article/tag', //todo
			'article-untag' => 'article/untag', //todo
			//collection
			'collection-add' => 'collection/add', //todo
			'collection-delete' => 'collection/delete', //todo
			//settings
			'settings-save' => 'settings/save', //todo
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
			'mod_load' => 'load',
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
			'MustLogin' => array( 'To do that you need to login!', 'warning' ),
			'NoPermission' => array( 'You do not have the required permissions to do that!', 'warning' ),
			'UserFollowed' => array( 'Sucesfully following that user', 'success' ),
			'UserUnFollowed' => array( 'Successfully unfollowed that user', 'success' ),
			'AlreadyInvited' => array( 'You are already invited to Pulsefeed!', 'success' ),
			'InvalidInviteCode' => array( 'Invalid invite code', 'warning' ),
			'InviteCodeAdded' => array( 'Welcome to the Pulsefeed Alpha!', 'success' ),
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
			'ArticleRead' => array( 'Article hidden', 'success' ),
			'ArticleRecommended' => array( 'Article liked', 'success' ),
			'ArticleUnRecommended' => array( 'Article unliked', 'success' ),
		),
		//how much each type of share/save is worth
		'popularity' => array(
			'recommend' => 20, //internal recommendations
			'facebook_shares' => 2,
			'facebook_comments' => 1,
			'delicious_saves' => 5,
			'twitter_links' => 2,
			'digg_diggs' => 3,
		),
		//no-go tag words (words must be larger than 2 already)
		'no_tag' => array(
			'the',
			'what',
			'you',
			'and',
			'when',
			'any',
			'into',
		),
	);
?>