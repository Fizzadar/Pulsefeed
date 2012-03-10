<?php
	/*
		file: app/lib/stream.php
		desc: load & display streams
	*/

	class mod_stream {
		//set our stream types
		private $stream_types = array(
			'hybrid', //unread, recommendations, sorted by pop + time
			'unread', //unread, sorted by pop + time
			'popular', //24 hour popular, recommendations, sorted by pop
			'newest', //all, recommendations, sorted by time
			'public', //24 hour all articles, sorted by pop + time
			'discover', //non-subscribed + popular recommendations listed as articles + popular unsubscribed articles, sorted by pop + time
			'source', //stream from an individual source, sorted by time
			'tag', //tag streams
			'account', //account streams
		);
		private $db;
		protected $stream_type; //stream type
		protected $data = false; //stores the stream data
		private $source_id;
		private $stream_id;
		private $user_id;
		public $valid = false;
		private $offset = 0;
		private $since_id = 0;
		private $account_type;

		//setup
		public function __construct( $db, $stream_type = 'hybrid' ) {
			//invalid stream type?
			if( !in_array( $stream_type, $this->stream_types ) )
				return false;

			//check our db
			if( !method_exists( $db, 'query' ) )
				return false;
			
			//set our db accessor
			$this->db = $db;

			//set the stream type & userid & etc
			$this->stream_type = $stream_type;

			//now we're valid!
			$this->valid = true;
		}

		//set since_id
		public function set_sinceid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->since_id = $id;
		}
		
		//set offset
		public function set_offset( $count ) {
			if( !is_numeric( $count ) ) return false;
			$this->offset = $count;
		}

		//set userid
		public function set_userid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->user_id = $id;
		}

		//set sourceid
		public function set_sourceid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->source_id = $id;
		}

		//set account type
		public function set_accountType( $type ) {
			if( !in_array( $type, array( 'twitter', 'facebook' ) ) ) return false;
			$this->account_type = $type;
		}

		//match end urls
		private function match_endurls( $a, $b ) {
			//equal?
			if( $a == $b ) return true;

			//a substring of b?
			if( substr( $a, 0, strlen( $b ) ) == $b ) return true;

			//b substring of a?
			if( substr( $b, 0, strlen( $a ) ) == $a ) return true;

			//fail
			return false;
		}

		//sort by time function
		private function sortTime( $a, $b ) {
			return $a['article_time'] < $b['article_time'];
		}

		//sort by popularity function
		private function sortPopscore( $a, $b ) {
			return $a['article_popscore'] < $b['article_popscore'];
		}

		//load our data from the database
		private function load_data() {
			global $mod_data, $mod_memcache, $mod_user;
			//load articles & recommends
			$articles = $this->load_articles();
			//not array?
			if( !is_array( $articles ) )
				return array();

			//return array
			$return = array();

			//firstly, build a list of articles to grab
			$list = array();
			foreach( $articles as $article )
				$list[] = array(
					'id' => $article['article_id']
				);

			//get articles from memcache (we hope!)
			$articledata = $mod_memcache->get( 'mod_article', $list );
			//switch keys to article_ids
			$tmp = array();
			foreach( $articledata as $article )
				$tmp[$article['id']] = $article;
			$articledata = $tmp;

			//switch stream type
			switch( $this->stream_type ):
				//user
				case 'hybrid':
				case 'unread':
				case 'popular':
				case 'newest':
				case 'discover':
				case 'account':
					//prepare articledata
					foreach( $articledata as $k => $v ):
						$articledata[$k]['article_popscore'] = 0;
						$articledata[$k]['refs'] = array();
					endforeach;

					//loop each article
					foreach( $articles as $k => $article ):
						$id = $article['article_id'];

						//increment popscore
						$articledata[$id]['article_popscore'] += $article['article_popscore'];

						//set data
						$articledata[$id]['article_time'] = $article['article_time'];

						if( ( $this->stream_type == 'newest' or $this->stream_type == 'popular' ) and $this->user_id == $mod_user->get_userid() )
							$articledata[$id]['unread'] = $article['unread'];
						elseif( $this->stream_type == 'unread' or $this->stream_type == 'hybrid' )
							$articledata[$id]['unread'] = 1;

						//remove bits
						unset( $article['article_time'] );
						unset( $article['article_popscore'] );
						unset( $article['article_id'] );
						unset( $article['unread'] );

						//load source data
						$article['source_data'] = json_decode( $article['source_data'], true );

						//add source to actual article
						$articledata[$id]['refs'][] = $article;
					endforeach;
					break;

				//public
				case 'public':
					//get list of source ids
					$list = array();
					foreach( $articles as $article )
						$list[] = array(
							'id' => $article['source_id']
						);
					$list = array_unique( $list, SORT_REGULAR );

					$sourcedata = $mod_memcache->get( 'mod_source', $list );
					//switch keys
					$tmp = array();
					foreach( $sourcedata as $source )
						$tmp[$source['id']] = $source;
					$sourcedata = $tmp;

					//loop each article
					foreach( $articles as $k => $article ):
						$id = $article['article_id'];

						//increment popscore
						$articledata[$id]['article_popscore'] = $article['article_popscore'];

						//set data
						$articledata[$id]['article_time'] = $article['article_time'];

						//remove bits
						unset( $article['article_time'] );
						unset( $article['article_popscore'] );
						unset( $article['article_id'] );

						//get domain
						$domain = parse_url( $sourcedata[$article['source_id']]['site_url'] );
						$domain = $domain['host'];

						//set ref data
						$article['source_type'] = 'source';
						$article['source_title'] = $sourcedata[$article['source_id']]['site_title'];
						$article['source_data'] = array( 'domain' => $domain );

						//add source to actual article
						$articledata[$id]['refs'][] = $article;
					endforeach;
					break;

				//source
				case 'source':
					//get source data
					$data = $mod_memcache->get( 'mod_source', array(
						array(
							'id' => $this->source_id
						)
					) );
					$data = $data[0];
					$domain = parse_url( $data['site_url'] );
					$domain = $domain['host'];

					//loop each article
					foreach( $articles as $k => $article ):
						$id = $article['article_id'];

						//set data
						$articledata[$id]['article_time'] = $article['article_time'];

						//remove bits
						unset( $article['article_time'] );
						unset( $article['article_id'] );

						//set ref data
						$article['source_type'] = 'source';
						$article['source_title'] = $data['site_title'];
						$article['source_data'] = array( 'domain' => $domain );
						
						//add source to actual article
						$articledata[$id]['refs'][] = $article;
					endforeach;
					break;

				//tag
				case 'tag':
					break;
			endswitch;

			//recommended? = matters on all articles on all streams, if logged in
			$likes = array();
			if( $mod_user->check_login() ):
				//list of memcache data
				$list = array();
				foreach( $articledata as $article )
					$list[] = array(
						'user_id' => $mod_user->get_userid(),
						'article_id' => $article['id']
					);

				//get them, skip db
				$likes = $mod_memcache->get( 'mod_user_likes', $list, true );
				//switch keys
				$tmp = array();
				foreach( $likes as $like )
					$tmp[$like['article_id']] = $like;
				$likes = $tmp;
			endif;

			//articles, stuff for all of them
			foreach( $articledata as $key => $article ):
				//work out stuffs
				$article['time_ago'] = $mod_data->time_ago( $article['time'] );
				$article['short_description'] = substr( $article['description'], 0, 200 ) . ( strlen( $article['description'] ) > 200 ? '...' : '' );
				$article['shorter_description'] = substr( $article['description'], 0, 120 ) . ( strlen( $article['description'] ) > 120 ? '...' : '' );
				$article['liked'] = isset( $likes[$article['id']] );
				//add to return
				$return[] = $article;
			endforeach;

			//return it
			return $return;
		}

		//load articles (db)
		private function load_articles() {
			//get our logged in user (for recommend check)
			global $mod_config, $mod_user;

			//build our query
			$article_id = 'article_id';
			$sql = '';
			switch( $this->stream_type ):
				//user streams
				case 'hybrid':
				case 'unread':
				case 'popular':
				case 'newest':
				case 'discover':
				case 'account':
					$sql .= '
						SELECT article_id, unread, source_type, source_id, source_title, source_data, article_time, article_popscore
						FROM mod_user_articles';
					switch( $this->stream_type ):
						case 'hybrid':
							$sql .= '
								WHERE expired = 0
								AND unread = 1';
							$order = 'article_popscore';
							break;
						case 'popular':
							$sql .= '
								WHERE expired = 0';
							$order = 'article_popscore';
							break;
						case 'unread':
							$sql .= '
								WHERE unread = 1';
							$order = 'article_time';
							break;
						case 'newest':
							$sql .= '
								WHERE true';
							$order = 'article_time';
							break;
						case 'account':
							$sql .='
								WHERE source_type = "' . $this->account_type . '"';
							$order = 'article_time';
							break;
					endswitch;

					//add user id bit
					$sql .= '
						AND user_id = ' . $this->user_id;

					//are we the not ourselves? only use source refs
					if( $this->stream_type != 'account' and $mod_user->get_userid() != $this->user_id )
						$sql .= '
							AND source_type = "source"';

					break;

				//public stream
				case 'public':
					$sql .= '
						SELECT id AS article_id, source_id, time AS article_time, popularity_score AS article_popscore
						FROM mod_article
						WHERE expired = 0';
					$order = 'popularity_score';
					$article_id = 'id';
					break;

				//tag stream
				case 'tag':
					break;

				//source stream
				case 'source':
					$sql .= '
						SELECT article_id, article_time, source_id
						FROM mod_source_articles
						WHERE source_id = ' . $this->source_id;
					$order = 'article_time';
					break;
			endswitch;

			//end of query
			$sql .= '
				AND ' . $article_id . ' > ' . $this->since_id . '
				ORDER BY ' . $order . ' DESC
				LIMIT ' . $this->offset . ', 64
			';

			//run our query, return the data
			if( $data = $this->db->query( $sql ) )
				return $data;
			else
				return false;
		}

		//prepare our data
		public function prepare() {
			//already loaded?
			if( is_array( $this->data ) ) return true;

			//do we have our required info to start (user_id, stream_id, source_id)
			switch( $this->stream_type ):
				case 'hybrid':
				case 'unread':
				case 'popular':
				case 'newest':
					if( !isset( $this->user_id ) ) return false;
					break;
				case 'source':
					if( !isset( $this->source_id ) ) return false;
					break;
				case 'account':
					if( !isset( $this->account_type ) ) return false;
					break;
			endswitch;

			//load our articles & recommendations
			$this->data = $this->load_data();
			if( !is_array( $this->data ) )
				return false;

			switch( $this->stream_type ):
				//popularity sorted
				case 'hybrid':
				case 'popular':
				case 'public':
					usort( $this->data, array( 'mod_stream', 'sortPopscore' ) );
					break;
				//time sorted
				case 'unread':
				case 'newest':
				case 'account':
				case 'source':
					usort( $this->data, array( 'mod_stream', 'sortTime' ) );
					break;
			endswitch;

			//a ok!
			return true;
		}

		//send data back to template
		public function get_data() {
			if( !$this->data ) return false;
			return array( 'items' => $this->data, 'features' => array() );
		}
	}
?>