<?php
	/*
		file: app/lib/load.php
		desc: loads various bits of user data
	*/

	class mod_load {
		//setup
		private $db;
		private $data;
		private $memcache;

		//construct
		public function __construct( $db, $data, $memcache ) {
			//check our db
			if( !method_exists( $db, 'query' ) )
				return false;
			
			//set our db accessor
			$this->db = $db;
			//data
			$this->data = $data;
			//set our memcache class
			$this->memcache = $memcache;
		}

		//load a users sources the follow
		public function load_sources( $user_id ) {
			//load the users sources
			$sources = $this->db->query( '
				SELECT website_id
				FROM mod_user_websites
				WHERE user_id = ' . $user_id . '
			' );

			//build list
			$list = array();
			foreach( $sources as $source )
				$list[] = array(
					'id' => $source['website_id']
				);
			$sources = $this->memcache->get( 'mod_website', $list );

			//make/build some data
			foreach( $sources as $k => $s ):
				$sources[$k]['source_domain'] = @$this->data->domain_url( $s['site_url'] );
			endforeach;

			//return
			return $sources;
		}

		//load a users accounts (unique - basically a list of names)
		public function load_accounts( $user_id ) {
			if( !is_numeric( $user_id ) ) return false;

			//load accounts
			$accounts = $this->db->query( '
				SELECT type
				FROM mod_account
				WHERE user_id = ' . $user_id . '
				AND disabled = 0
				GROUP BY type
			' );

			//return
			return $accounts;
		}

		//load a users users they follow
		public function load_users( $user_id ) {
			if( !is_numeric( $user_id ) ) return false;

			//load users users
			$followings = $this->db->query( '
				SELECT following_id
				FROM mod_user_follows
				WHERE user_id = ' . $user_id . '
			' );

			//build list
			$list = array();
			foreach( $followings as $follow )
				$list[] = array(
					'id' => $follow['following_id']
				);
			$followings = $this->memcache->get( 'core_user', $list );

			//return
			return $followings;
		}

		//load a users collections
		public function load_collections( $user_id ) {
			if( !is_numeric( $user_id ) ) return false;

			$collections = $this->db->query( '
				SELECT id, name, articles
				FROM mod_collection
				WHERE user_id = ' . $user_id . '
			' );

			//return
			return $collections;
		}

		//load a users topics
		public function load_topics( $user_id ) {
			if( !is_numeric( $user_id ) ) return false;

			//select ids from sql
			$topics = $this->db->query( '
				SELECT topic_id
				FROM mod_user_topics
				WHERE user_id = ' . $user_id . '
			' );

			//use memcache to get topics data
			$list = array();
			foreach( $topics as $topic )
				$list[] = array(
					'id' => $topic['topic_id']
				);
			$topics = $this->memcache->get( 'mod_topic', $list );

			//return
			return $topics;
		}

		//load a source object articles w/ images (topic, source, collection)
		public function load_source( $id, $type = 'topic' ) {
			if( !is_numeric( $id ) or !in_array( $type, array( 'topic', 'website', 'collection' ) ) ) return false;

			//basic info
			$data = $this->memcache->get( 'mod_' . $type, array( array(
				'id' => $id
			) ) );
			$data = $data[0];

			//fixes to bring all in line
			switch( $type ):
				case 'website':
					$data['title'] = $data['site_title'];
					$tmp = parse_url( $data['site_url'] );
					$data['site_domain'] = $tmp['host'];
				case 'topic':
					$data['article_count'] = $data['articles'];
					break;
				case 'collection':
					$data['title'] = $data['name'];
					$user = $this->memcache->get( 'core_user', array( array(
						'id' => $data['user_id']
					) ) );
					$data['username'] = ( $user and count( $user ) ) == 1 ? $user[0]['name'] : 'Unknown';
					$data['article_count'] = $data['articles'];
					break;
			endswitch;

			//load articles w/ images via a cached query
			$data['articles'] = $this->db->query( '
				SELECT mod_article.id, mod_article.title, mod_article.image_thumb
				FROM mod_' . $type . '_articles, mod_article
				WHERE mod_article.image_thumb != ""
				AND mod_article.id = mod_' . $type . '_articles.article_id
				AND mod_' . $type . '_articles.' . $type . '_id = ' . $id . '
				ORDER BY mod_' . $type . '_articles.' . ( $type == 'collection' ? 'time' : 'article_time' ) . ' DESC
				LIMIT 3
			', true, 86400 );

			return $data;
		}
	}
?>