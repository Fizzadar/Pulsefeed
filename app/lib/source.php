<?php
	/*
		file: app/lib/source.php
		desc: loads feeds, twitter streams and facebook streams; also locates feeds
	*/

	class mod_source {
		private $pie;
		private $type = 'source';
		private $url;

		//start the class
		public function __construct( $url, $type = 'source' ) {
			global $mod_config;

			//make sure type is valid
			if( in_array( $type, array( 'source', 'twitter', 'facebook' ) ) )
				$this->type = $type;

			//set url (or data)
			$this->url = $url;

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
			switch( $this->type ):
				case 'source':
					return $this->loadSource();
				case 'twitter':
					return $this->loadTwitter();
				case 'facebook':
					return $this->loadFacebook();
			endswitch;
		}

		//load a feeds data
		private function loadSource() {
			global $mod_db;

			//set the url
			$this->pie->set_feed_url( $this->url );
			$this->pie->set_item_class( 'mod_feed' );
			$this->pie->init();
			
			//get the items
			$items = $this->pie->get_items();

			//loop items, build articles
			$articles = array();
			foreach( $items as $item ):
				$i = $item->get_item();

				//check for article
				$check = $mod_db->query( '
					SELECT id, popularity_score, time, title, url, end_url, image_quarter, image_half, image_third, description
					FROM mod_article
					WHERE end_url = "' . $i->get_end_url() . '"
					LIMIT 1
				' );
				if( $check and count( $check ) == 1 ):
					$articles[] = array(
						'id' => $check[0]['id'],
						'popularity_score' => $check[0]['popularity_score'],
						'title' => $check[0]['title'],
						'url' => $check[0]['url'],
						'end_url' => $check[0]['end_url'],
						'time' => $item->get_time(),
					);
				else:
					$i->get_article(); //populates thumb images
					$images = $i->get_thumbs();

					$articles[] = array(
						'title' => $item->get_title(),
						'url' => $item->get_permalink(),
						'end_url' => $i->get_end_url(),
						'summary' => $i->get_summary(),
						'image_quarter' => isset( $images['quarter'] ) ? $images['quarter'] : '',
						'image_third' => isset( $images['third'] ) ? $images['third'] : '',
						'image_half' => isset( $images['half'] ) ? $images['half'] : '',
						'time' => $item->get_time(),
					);
				endif;
			endforeach;

			//return them
			return $articles;
		}

		//load twitter data
		private function loadTwitter() {
			global $mod_config;

			//load oauth data (passed as json via url)
			$data = json_decode( $this->url );

			//start oauth
			$tw = new TwitterOAuth( $mod_config['apps']['twitter']['id'], $mod_config['apps']['twitter']['token'], $data->token, $data->secret );

			//load home stream
			$home = $tw->get( 'statuses/home_timeline', array(
				'count' => 200,
				'include_entities' => true,
				'exclude_replies' => true
			) );

			$tweets = array();
			//locate tweets with urls
			foreach( $home as $tweet ):
				foreach( $tweet->entities->urls as $url ):
					$tweets[] = array(
						'url' => $url->url,
						'user' => $tweet->user->screen_name,
						'user_id' => $tweet->user->id,
						'tweet' => $tweet->text,
						'time' => strtotime( $tweet->created_at )
					);
				endforeach;
			endforeach;

			//now, get each article
			$articles = array();
			foreach( $tweets as $tweet ):
				$i = new mod_feed_article( $tweet['url'] );
				$i->get_article(); //populate thumbs + rip content
				$images = $i->get_thumbs();

				$articles[] = array(
					'title' => empty( $i->riptitle ) ? $tweet['tweet'] : $i->riptitle,
					'url' => $tweet['url'],
					'end_url' => $i->get_end_url(),
					'summary' => $i->get_summary(),
					'image_quarter' => isset( $images['quarter'] ) ? $images['quarter'] : '',
					'image_third' => isset( $images['third'] ) ? $images['third'] : '',
					'image_half' => isset( $images['half'] ) ? $images['half'] : '',
					'time' => $tweet['time'],
					'ex_username' => $tweet['user'],
					'ex_userid' => $tweet['user_id']
				);
			endforeach;

			return $articles;
		}

		//load facebook data
		private function loadFacebook() {
			global $mod_config;

			//oauth data
			$data = json_decode( $this->url );

			//new facebook app
			$fb = new Facebook( array(
				'appId' => $mod_config['apps']['facebook']['id'],
				'secret' => $mod_config['apps']['facebook']['token']
			) );

			//get home stream
			$home = $fb->api( '/me/home', 'GET', array(
				'access_token' => $data->token,
				'limit' => 200
			) );

			//loop items
			$links = array();
			foreach( $home['data'] as $item ):
				if( $item['type'] == 'link' ):
					//skip internal links
					if( preg_match( '/facebook.com/', $item['link'] ) )
						continue;

					$links[] = array(
						'url' => $item['link'],
						'user' => $item['from']['name'],
						'user_id' => $item['from']['id'],
						'title' => $item['name'],
						'time' => strtotime( $item['created_time'] )
					);
				endif;
			endforeach;

			//now, get each article
			$articles = array();
			foreach( $links as $link ):
				$i = new mod_feed_article( $link['url'] );
				$i->get_article(); //populate thumbs + rip content
				$images = $i->get_thumbs();

				$articles[] = array(
					'title' => $link['title'],
					'url' => $link['url'],
					'end_url' => $i->get_end_url(),
					'summary' => $i->get_summary(),
					'image_quarter' => isset( $images['quarter'] ) ? $images['quarter'] : '',
					'image_third' => isset( $images['third'] ) ? $images['third'] : '',
					'image_half' => isset( $images['half'] ) ? $images['half'] : '',
					'time' => $link['time'],
					'ex_username' => $link['user'],
					'ex_userid' => $link['user_id']
				);
			endforeach;

			return $articles;
		}
	}
?>