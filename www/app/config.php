<?php
	/*
		file: app/config.php
		desc: configuration for pulsefeed app
	*/
	
	define( 'PULSEFEED_VERSION', '0.9.8' );

	//config array
	$mod_config = array(
		'template' => 'main',
		'useragent' => 'Pulsefeed / v.' . PULSEFEED_VERSION,
		'iapi' => isset( $_GET['iapi'] ),
		'ajax' => isset( $_GET['ajax'] ),
		'load' => array(
			//204 blank
			'204' => '204',
			//default
			'default' => 'home',
			//article
			'article' => 'article/load',
			'article-collect' => 'article/collect',
			'article-share' => 'article/share',
			//streams
			'user' => 'stream/user',
			'public' => 'stream/public',
			'website' => 'stream/website',
			'account' => 'stream/account',
			'collection' => 'stream/collection',
			'topic' => 'stream/topic',
			//source browsing
			'website-browse' => 'source/browse',
			'topic-browse' => 'source/browse',
			'collection-browse' => 'source/browse',
			//add website
			'website-add' => 'website/add',
			//search
			'search' => 'search',
			//users
			'login' => 'user/login',
			'settings' => 'user/settings',
			'settings-accounts' => 'user/settings',
			'settings-data' => 'user/settings',
			//static pages
			'page' => 'page',
			//stats
			'stats' => 'stats',
			//admin pages
			'admin' => 'admin/home',
			'admin-permissions' => 'admin/permissions',
			'admin-topics' => 'admin/topics',
			'admin-memcache' => 'admin/memcache',
			'admin-users' => 'admin/users'
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
			//website
			'website-add' => 'website/add',
			'website-opml' => 'website/opml',
			'website-subscribe' => 'website/subscribe',
			'website-unsubscribe' => 'website/unsubscribe',
			//article
			'article-collect' => 'article/collect',
			'article-uncollect' => 'article/uncollect',
			'article-hide' => 'article/hide',
			'article-share' => 'article/share',
			'article-unshare' => 'article/unshare',
			//collection
			'collection-delete' => 'collection/delete',
			//topic
			'topic-subscribe' => 'topic/subscribe',
			'topic-unsubscribe' => 'topic/unsubscribe',
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
			'Minifier' => 'external/Minifier',
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
			//website
			'NoWebsite' => array( 'The website you are trying to subscribe to doesn\'t exist!', 'warning' ),
			'WebsiteSubscribed' => array( 'Successfully subscribed to that website', 'success' ),
			'WebsiteUnsubscribed' => array( 'Successfully unsubscribed to that website', 'success' ),
			'WebsitesSubscribed' => array( 'Successfully subscribed to those websites', 'success' ),
			//general errors
			'DatabaseError' => array( 'Unfortunately there was some kind of database error!', 'warning' ),
			'UnknownError' => array( 'An unknown error (eek!) occurred, please try again', 'warning' ),
			'NotFound' => array( 'The page you requested could not be found!', 'warning' ),
			//article
			'ArticleRead' => array( 'Article hidden', 'success' ),
			'ArticleShared' => array( 'Article shared', 'success' ),
			'ArticleUnshared' => array( 'Article unshared', 'success' ),
			'ArticleCollected' => array( 'Article collected', 'success' ),
			'ArticleCollectedNew' => array( 'Article collected in new collection', 'success' ),
			'ArticleUnCollected' => array( 'Article uncollected', 'success' ),
			//collection
			'CollectionDeleted' => array( 'Collection deleted', 'success' ),
			//topic
			'NoTopic' => array( 'The topic you are trying to find doesn\'t exist!', 'warning' ),
			'TopicSubscribed' => array( 'Successfully subscribed to that topic', 'success' ),
			'TopicUnsubscribed' => array( 'Successfully unsubscribed to that topic', 'success' ),
		),
		//how much each type of share/save is worth
		'popularity' => array(
			'share' => 20, //internal recommendations
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
				'127.0.0.1' => 11211
			),
			//raw query servers
			'query' => array(
				'127.0.0.1' => 11211
			),
			//maintenance server
			'maintenance' => array(
				'127.0.0.1' => 11211
			),
			//stream cache
			'stream' => array(
				'127.0.0.1' => 11211
			)
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
			'mod_user_websites' => array( //user subscribed to a source?
				'user_id',
				'website_id'
			),
			'mod_user_shares' => array( //user shared an article?
				'user_id',
				'article_id'
			),
			'mod_user_topics' => array( //user subscribed to a topic?
				'user_id',
				'topic_id'
			),
			'mod_article' => array( //articles
				'id'
			),
			'mod_website' => array( //sources
				'id'
			),
			'mod_collection' => array( //collections
				'id'
			),
			'mod_topic' => array( //topics
				'id'
			),
			'core_user' => array( //user
				'id'
			),
		),
		//image sizes
		'image_sizes' => array(
			//browsing topics, websites, etc
			'thumb' => array(
				'w' => 134,
				'h' => 77,
				'scale' => 1
			),
			//iphone retina screen /2 <= for mobile/phone?
			'tall' => array(
				'w' => 320,
				'h' => 480,
				'scale' => 0.8
			),
			//stream main size
			'wide' => array(
				'w' => 460,
				'h' => 127,
				'scale' => 0.8
			),
			//better stream one
			'wide_big' => array(
				'w' => 460,
				'h' => 200,
				'scale' => 0.9
			)
		),
		//static pages
		'pages' => array(
			'help' => 'help/index',
			'help-streams' => 'help/streams',
			'help-accounts' => 'help/accounts',
			'help-topics' => 'help/topics',
			'help-collections' => 'help/collections',
			'help-sources' => 'help/sources',
			'about' => 'about',
			'contact' => 'contact',
			'suggest' => 'suggest',
		),
		'is' => array(
			'an RSS reader on steroids',
			'your personalized magazine',
			'the best way to read the news',
			'a stream of interesting stuff',
		),
	);
?>