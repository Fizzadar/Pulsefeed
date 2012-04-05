<?php
	/*
		file: app/config.php
		desc: configuration for pulsefeed app
	*/
	
	define( 'PULSEFEED_VERSION', '0.9.5a' );

	//config array
	$mod_config = array(
		'template' => 'main',
		'useragent' => 'Pulsefeed / v.' . PULSEFEED_VERSION,
		'iapi' => isset( $_GET['iapi'] ),
		'ajax' => isset( $_GET['ajax'] ),
		'load' => array(
			'204' => '204',
			//js & css
			'js' => 'inc/js',
			//default
			'default' => 'home',
			//article
			'article' => 'article/load',
			'article-collect' => 'article/collect',
			//streams
			'user' => 'stream/user',
			'public' => 'stream/public',
			'source' => 'stream/source',
			'account' => 'stream/account',
			'collection' => 'stream/collection',
			'tag' => 'stream/tag', //todo - can hide
			//sources
			'source-browse' => 'source/browse',
			'source-add' => 'source/add',
			//search
			'search' => 'search',
			//users
			'login' => 'user/login',
			'settings' => 'user/settings',
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
			'login-relogin' => 'user/login',
			'load' => 'user/load',
			'follow' => 'user/follow',
			'unfollow' => 'user/unfollow',
			'settings' => 'user/settings',
			'account-sync' => 'user/account/sync',
			'account-delete' => 'user/account/delete',
			//source
			'source-add' => 'source/add',
			'source-opml' => 'source/opml',
			'subscribe' => 'source/subscribe',
			'unsubscribe' => 'source/unsubscribe',
			'source-tag' => 'source/tag', //todo - can hide
			'source-untag' => 'source/untag', //todo - can hide
			//article
			'article-like' => 'article/like',
			'article-unlike' => 'article/unlike',
			'article-collect' => 'article/collect',
			'article-hide' => 'article/hide',
			'article-tag' => 'article/tag', //todo - can hide
			'article-untag' => 'article/untag', //todo - can hide
			//collection
			'collection-delete' => 'collection/delete',
		),
		'libs' => array(
			//internal libs
			'mod_template' => 'template',
			'mod_message' => 'message',
			'mod_source' => 'source',
			'mod_feed' => 'feed',
			'mod_feed_article' => 'feed_article',
			'mod_stream' => 'stream',
			'mod_stream_site' => 'stream_site',
			'mod_data' => 'data',
			'mod_cookie' => 'cookie',
			'mod_load' => 'load',
			'mod_memcache' => 'memcache',
			'mod_daemon' => 'daemon',
			//external libs
			'SimplePie' => 'external/simplepie',
			'simple_html_dom' => 'external/simpledom',
			'idna_convert' => 'external/idna',
			'resize' => 'external/resize',
			'Readability' => 'external/Readability',
			'Thread' => 'external/Thread',
		),
		'messages' => array(
			//user
			'NewUser' => array( 'Welcome to Pulsefeed', 'success' ),
			'LoginServerError' => array( 'There was a problem contacting the account provider', 'warning' ),
			'FailedLogin' => array( 'We could not validate your login', 'warning' ),
			'AccountAdded' => array( 'External account added', 'success' ),
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
			'SettingsUpdated' => array( 'Settings updated', 'success' ),
			'AccountSyncOn' => array( 'Account sync enabled', 'success' ),
			'AccountSyncOff' => array( 'Account sync disabled', 'success' ),
			'AccountDeleted' => array( 'External account removed', 'success' ),
			'LastAccount' => array( 'You cannot remove all accounts, because you won\'t be able to login!', 'warning' ),
			//posting & requests
			'InvalidToken' => array( 'Invalid session token, please retry your last action', 'warning' ),
			'InvalidPost' => array( 'Incorrect info was sent during the last action, please try again', 'warning' ),
			'InvalidGet' => array( 'Invalid data was sent, please try again', 'warning' ),
			'InvalidEmail' => array( 'Please use a valid email (or none)', 'warning' ),
			//not found/etc
			'NoFeedFound' => array( 'We couldn\'t find a feed on that website, please try another address or direct feed link', 'warning' ),
			//source
			'NoSource' => array( 'The source you are trying to subscribe to doesn\'t exist!', 'warning' ),
			'SourceSubscribed' => array( 'Successfully subscribed to that source', 'success' ),
			'SourceUnsubscribed' => array( 'Successfully unsubscribed to that source', 'success' ),
			'SourcesSubscribed' => array( 'Successfully subscribed to those sources', 'success' ),
			//general errors
			'DatabaseError' => array( 'Unfortunately there was some kind of database error!', 'warning' ),
			'UnknownError' => array( 'An unknown error (eek!) occurred, please try again', 'warning' ),
			'NotFound' => array( 'The page you requested could not be found!', 'warning' ),
			//article
			'ArticleRead' => array( 'Article hidden', 'success' ),
			'ArticleLiked' => array( 'Article liked', 'success' ),
			'ArticleUnLiked' => array( 'Article unliked', 'success' ),
			'ArticleCollected' => array( 'Article collected', 'success' ),
			'ArticleCollectedNew' => array( 'Article collected in new collection', 'success' ),
			//collection
			'CollectionDeleted' => array( 'Collection deleted', 'success' ),
		),
		//how much each type of share/save is worth
		'popularity' => array(
			'like' => 20, //internal recommendations
			'facebook_shares' => 0.5,
			'facebook_comments' => 1,
			'delicious_saves' => 5,
			'twitter_links' => 2,
			'digg_diggs' => 4,
			'reddit_score' => 0.2,
			'google_pluses' => 4,
			'linked_shares' => 5,
		),
		//app info
		'apps' => array(
			'facebook' => array(
				'id' => '346508828699100',
				'token' => '85804588b0a5a0e005bdca184dae17b5',
			),
			'twitter' => array(
				'id' => '9CxR2vqndROknYPJ9vlpw',
				'token' => 'bPnQZYzamUsUoqmdsuztxBmNwEqiqDSsg9IVj9WujyA'
			)
		),
		//memcache servers
		'memcache' => array(
			//mod_memcache servers
			'mod' => array(
				'127.0.0.1' => 11211,
			),
			//raw query servers
			'query' => array(
				'127.0.0.1' => 11211,
			),
			//maintenance server (only tiny)
			'maintenance' => array(
				'127.0.0.1' => 11211,
			),
		),
		//database layout for mod_memcache
		'dblayout' => array(
			'mod_user_reads' => array( //user read an article?
				'user_id',
				'article_id'
			),
			'mod_user_hides' => array( //user hide an article?
				'user_id',
				'article_id'
			),
			'mod_user_follows' => array( //user following a user?
				'user_id',
				'following_id'
			),
			'mod_user_sources' => array( //user subscribed to a source?
				'user_id',
				'source_id'
			),
			'mod_user_likes' => array( //user likes an article?
				'user_id',
				'article_id'
			),
			'mod_article' => array( //articles
				'id'
			),
			'mod_source' => array( //sources
				'id'
			),
			'mod_collection' => array( //collections
				'id'
			),
			'mod_tag' => array( //tags
				'id'
			),
			'core_user' => array( //user
				'id'
			),
		),
	);
?>