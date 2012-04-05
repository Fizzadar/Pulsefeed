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
				SELECT id, type, site_title AS source_title, site_url AS source_url
				FROM mod_source, mod_user_sources
				WHERE mod_source.id = mod_user_sources.source_id
				AND mod_user_sources.user_id = ' . $user_id . '
			' );

			//make/build some data
			foreach( $sources as $k => $s ):
				$sources[$k]['source_domain'] = @$this->data->domain_url( $s['source_url'] );
			endforeach;

			//return
			return $sources;
		}

		//load a users accounts (unique - basically a list of names)
		public function load_accounts( $user_id ) {
			//load accounts
			$accounts = $this->db->query( '
				SELECT type
				FROM mod_account
				WHERE user_id = ' . $user_id . '
				GROUP BY type
			' );

			//return
			return $accounts;
		}

		//load a users users they follow
		public function load_users( $user_id ) {
			//load users users
			$followings = $this->db->query( '
				SELECT id, name, avatar_url
				FROM core_user, mod_user_follows
				WHERE core_user.id = mod_user_follows.following_id
				AND mod_user_follows.user_id = ' . $user_id . '
			' );

			//edit some data
			foreach( $followings as $k => $f ):
				$followings[$k]['name'] = $this->data->str_tooltip( $f['name'] );
			endforeach;

			//return
			return $followings;
		}

		//load a users collections
		public function load_collections( $user_id ) {
			$collections = $this->db->query( '
				SELECT id, name, articles
				FROM mod_collection
				WHERE user_id = ' . $user_id . '
			' );

			//return
			return $collections;
		}
	}
?>