<?php
	/*
		file: app/lib/load.php
		desc: loads various bits of user data
	*/

	class mod_load {
		//setup
		private $db;
		private $data;

		//construct
		public function __construct( $db, $data ) {
			//check our db
			if( !method_exists( $db, 'query' ) )
				return false;
			
			//set our db accessor
			$this->db = $db;
			//data
			$this->data = $data;
		}

		//load a users sources the follow
		public function load_sources( $user_id, $type = false ) {
			//load the users sources
			$sources = $this->db->query( '
				SELECT id, type, site_title AS source_title, site_url AS source_url
				FROM mod_source, mod_user_sources
				WHERE mod_source.id = mod_user_sources.source_id
				' . ( $type ? ' AND mod_source.type = "' . $type . '"' : '' ) . '
				AND mod_user_sources.user_id = ' . $user_id . '
			' );

			//make/build some data
			foreach( $sources as $k => $s ):
				$sources[$k]['source_domain'] = @$this->data->domain_url( $s['source_url'] );
				$sources[$k]['source_title'] = $this->data->str_tooltip( $s['source_title'] );
			endforeach;

			//return
			return $sources;
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
	}
?>