<?php
	/*
		file: app/lib/source.php
		desc: loads feeds, twitter streams and facebook streams; also locates feeds
	*/

	class mod_source {
		private $pie;
		private $type = 'source';
		private $url;
		private $since_id;

		//start the class
		public function __construct( $url = '', $type = 'source', $since_id = 0 ) {
			global $mod_config;

			//make sure type is valid
			if( in_array( $type, array( 'source', 'twitter', 'facebook' ) ) )
				$this->type = $type;

			//set url (or data)
			$this->url = $url;

			//set since id
			$this->since_id = $since_id;

			//switch our types
			switch( $this->type ):
				case 'source':
					//start simplepie
					$this->pie = new SimplePie();
					$this->pie->enable_cache( false );
					$this->pie->set_useragent( $mod_config['useragent'] );
					break;
				case 'twitter':
					break;
				case 'facebook':
					break;
				default:
					return false;
			endswitch;
		}

		//find a feed (based on url)
		public function find( $search_url ) {
			if( $this->type != 'source' )
				return false;

			//set url, go & get feed list
			$this->pie->set_feed_url( $search_url );
			$this->pie->init();
			$feeds = $this->pie->get_all_discovered_feeds();

			//do we have multiple feeds, return the 'best'/first?
			if( count( $feeds ) > 0 )
				return array(
					'site_title' => $this->pie->get_title(),
					'site_url' => $this->pie->get_permalink(),
					'feed_url' => $feeds[0]->url
				);

			//direct feed?
			if( count( $this->pie->get_items() ) > 0 )
				return array(
					'site_title' => $this->pie->get_title(),
					'site_url' => $this->pie->get_permalink(),
					'feed_url' => $search_url
				);
			
			//no feeds? :(
			return false;
		}

		//load
		public function load() {
			if( !isset( $this->type ) ) return false;
			
			switch( $this->type ):
				case 'source':
					return $this->loadSource();
				case 'twitter':
					return $this->loadTwitter();
				case 'facebook':
					return $this->loadFacebook();
				default:
					return false;
			endswitch;
		}

		//load a feeds data
		private function loadSource( $sourceid = 0 ) {
			global $mod_db;

			//set the url
			$this->pie->set_feed_url( $this->url );
			$this->pie->set_item_class( 'mod_feed' );
			$init = @$this->pie->init();
			
			//no init?
			if( !$init )
				return false;

			//get the items
			$items = $this->pie->get_items();

			//loop items, build articles
			$articles = array();
			foreach( $items as $key => $item ):	
				$i = $item->get_item();

				//debug
				echo 'item #' . $key . ' / ' . count( $items ) . ' : ' . $i->get_end_url() . PHP_EOL;

				//check article
				$check = $this->checkArticle( $i->get_end_url(), $item->get_permalink(), $item->get_title() );
				if( $check and is_array( $check ) ):
					$articles[] = $check;
					echo 'skipping, already got: ' . $i->get_end_url() . PHP_EOL;
					continue;
				endif;

				$i->get_article(); //populates thumb images
				$images = $i->get_thumbs();

				$articles[] = array(
					'title' => $item->get_title(),
					'url' => $item->get_permalink(),
					'end_url' => $i->get_end_url(),
					'summary' => $i->get_summary(),
					'author' => $item->get_author() ? $item->get_author()->get_name() : 'Unknown',
					'image_thumb' => isset( $images['thumb'] ) ? $images['thumb'] : '',
					'image_tall' => isset( $images['tall'] ) ? $images['tall'] : '',
					'image_wide' => isset( $images['wide'] ) ? $images['wide'] : '',
					'image_wide_big' => isset( $images['wide_big'] ) ? $images['wide_big'] : '',
					'time' => $item->get_time(),
					'type' => $i->get_type(),
					'xframe' => $i->get_xframe()
				);

			endforeach;

			//return them
			return $this->articleClean( $articles );
		}

		//load twitter data
		private function loadTwitter() {
			global $mod_config, $argv, $mod_db;

			//load oauth data (passed as json via url)
			$data = json_decode( $this->url );

			//start oauth
			$tw = new TwitterOAuth( $mod_config['apps']['twitter']['id'], $mod_config['apps']['twitter']['token'], $data->token, $data->secret );

			//build request
			$request = array(
				'count' => 100,
				'include_entities' => true,
				'exclude_replies' => true,
				'since_id' => $this->since_id
			);

			//load request
			$home = $tw->get( 'statuses/home_timeline', $request );

			//autherror?
			if( isset( $home->error ) and $home->error == 'Could not authenticate you.' )
				return 'deauthed';

			//not an array?
			if( !is_array( $home ) ):
				var_dump( $home );
				return false;
			endif;

			$tweets = array();
			//locate tweets with urls
			foreach( $home as $tweet ):
				//skip protected tweets
				if( $tweet->user->protected ) continue;

				foreach( $tweet->entities->urls as $url ):
					$tweets[] = array(
						'id' => $tweet->id,
						'url' => $url->url,
						'user' => $tweet->user->screen_name,
						'user_id' => $tweet->user->id,
						'tweet' => $tweet->text,
						'time' => strtotime( $tweet->created_at )
					);
				endforeach;
			endforeach;
			unset( $home );

			//now, get each article
			$articles = array();
			foreach( $tweets as $key => $tweet ):
				//start feed_article
				$i = new mod_feed_article( $tweet['url'] );

				//debug
				echo 'tweet #' . $key . ' / ' . count( $tweets ) . ' : ' . $i->get_end_url() . PHP_EOL;

				$i->get_article(); //populate thumbs + rip content
				$images = $i->get_thumbs();

				//check article
				$check = $this->checkArticle( $i->get_end_url(), $tweet['url'], $i->get_riptitle() );
				if( $check and is_array( $check ) ):
					$check['ex_username'] = $tweet['user'];
					$check['ex_userid'] = $tweet['user_id'];
					$check['ex_text'] = htmlentities( $tweet['tweet'], ENT_QUOTES );
					$check['ex_postid'] = $tweet['id'];
					$articles[] = $check;
					echo 'skipping, already got: ' . $i->get_end_url() . PHP_EOL;
					continue;
				endif;

				$articles[] = array(
					'title' => $i->get_riptitle(),
					'url' => $tweet['url'],
					'end_url' => $i->get_end_url(),
					'summary' => $i->get_summary(),
					'author' => 'Unknown',
					'image_thumb' => isset( $images['thumb'] ) ? $images['thumb'] : '',
					'image_tall' => isset( $images['tall'] ) ? $images['tall'] : '',
					'image_wide' => isset( $images['wide'] ) ? $images['wide'] : '',
					'image_wide_big' => isset( $images['wide_big'] ) ? $images['wide_big'] : '',
					'time' => $tweet['time'],
					'ex_username' => $tweet['user'],
					'ex_userid' => $tweet['user_id'],
					'ex_text' => htmlentities( $tweet['tweet'], ENT_QUOTES ),
					'ex_postid' => $tweet['id'],
					'type' => $i->get_type(),
					'xframe' => $i->get_xframe()
				);
			endforeach;

			return $this->articleClean( $articles );
		}

		//load facebook data
		private function loadFacebook() {
			global $mod_config, $mod_db, $argv;

			//oauth data
			$data = json_decode( $this->url );

			//new facebook app
			$fb = new Facebook( array(
				'appId' => $mod_config['apps']['facebook']['id'],
				'secret' => $mod_config['apps']['facebook']['token']
			) );

			//lets go
			try {
				//get home stream
				$home = $fb->api( '/me/home', 'GET', array(
					'access_token' => $data->token,
					'limit' => 100,
					'since' => $this->since_id
				) );
			} catch( FacebookApiException $e ) {
				$result = $e->getResult();
				if( $result['error'] and $result['error']['type'] == 'OAuthException' )
					if( preg_match( '/authorized application/', $result['error']['message'] ) )
						return 'deauthed';
					else
						return 'authexpire';

				return false;
			}

			//loop items
			$links = array();
			foreach( $home['data'] as $item ):
				if( $item['type'] == 'link' and isset( $item['link'] ) ):
					//skip internal links
					if( preg_match( '/facebook.com/', $item['link'] ) )
						continue;

					//get post id
					$bits = explode( '_', $item['id'] );
					$item['id'] = $bits[1];

					//build array
					$links[] = array(
						'id' => $item['id'],
						'url' => $item['link'],
						'user' => $item['from']['name'],
						'user_id' => $item['from']['id'],
						'title' => isset( $item['name'] ) ? $item['name'] : 'Unknown',
						'time' => strtotime( $item['created_time'] ),
						'status' => isset( $item['message'] ) ? $item['message'] : ( isset( $item['description'] ) ? $item['description'] : '' )
					);
				endif;
			endforeach;
			unset( $home );
			
			//now, get each article
			$articles = array();
			foreach( $links as $key => $link ):
				//start feed_article
				$i = new mod_feed_article( $link['url'] );

				//debug
				echo 'fblink #' . $key . ' / ' . count( $links ) . ' : ' . $i->get_end_url() . PHP_EOL;
				
				$i->get_article(); //populate thumbs + rip content
				$images = $i->get_thumbs();

				//check article
				$check = $this->checkArticle( $i->get_end_url(), $link['url'], $link['title'] );
				if( $check and is_array( $check ) ):
					$check['ex_username'] = $link['user'];
					$check['ex_userid'] = $link['user_id'];
					$check['ex_text'] = htmlentities( $link['status'], ENT_QUOTES );
					$check['ex_postid'] = $link['id'];
					$articles[] = $check;
					echo 'skipping, already got: ' . $i->get_end_url() . PHP_EOL;
					continue;
				endif;

				$articles[] = array(
					'title' => $link['title'],
					'url' => $link['url'],
					'end_url' => $i->get_end_url(),
					'summary' => $i->get_summary(),
					'author' => 'Unknown',
					'image_thumb' => isset( $images['thumb'] ) ? $images['thumb'] : '',
					'image_tall' => isset( $images['tall'] ) ? $images['tall'] : '',
					'image_wide' => isset( $images['wide'] ) ? $images['wide'] : '',
					'image_wide_big' => isset( $images['wide_big'] ) ? $images['wide_big'] : '',
					'time' => $link['time'],
					'ex_username' => $link['user'],
					'ex_userid' => $link['user_id'],
					'ex_text' => htmlentities( $link['status'], ENT_QUOTES ),
					'ex_postid' => $link['id'],
					'type' => $i->get_type(),
					'xframe' => $i->get_xframe()
				);
			endforeach;

			return $this->articleClean( $articles );
		}

		//remove non-wanted articles
		private function articleClean( $articles ) {
			//not got right data?
			foreach( $articles as $key => $article ):
				if( !isset( $article['id'] ) and ( empty( $article['title'] ) or empty( $article['url'] ) or empty( $article['end_url'] ) or empty( $article['summary'] ) ) ):
					unset( $articles[$key] );
					print_r( $article );
					echo 'removing article for incomplete data: ' . $article['end_url'] . PHP_EOL;
				endif;
			endforeach;

			return $articles;
		}

		//check articles
		private function checkArticle( $end_url, $url, $title ) {
			global $mod_db, $mod_memcache;

			//get mcache
			$mod_mcache = get_memcache();

			$id = 0;
			//check for article in memcache! (should always have the recent day or two's articles - sql is too heavy) (md5 only weak needed)
			if( $tmp = @$mod_mcache->get( md5( $end_url ) ) )
				$id = $tmp;
			if( $tmp = @$mod_mcache->get( md5( $url ) ) )
				$id = $tmp;
			if( $tmp = @$mod_mcache->get( md5( $title ) ) )
				$id = $tmp;

			//get article via memcache
			$article = $mod_memcache->get( 'mod_article', array(
				array(
					'id' => $id
				)
			) );

			if( count( $article ) == 1 ):
				$article = $article[0];
			
				//source matching
				$domain = parse_url( $end_url );
				$domain2 = parse_url( $article['end_url'] );

				//must both originate on one domain
				if( $domain['host'] == $domain2['host'] ):
					$article = array(
						'id' => $article['id'],
						'title' => $article['title'],
						'url' => $article['url'],
						'end_url' => $article['end_url'],
						'time' => $article['time'],
						'type' => $article['type']
					);
					return $article;
				endif;
			endif;


			return false;
		}
	}
?>