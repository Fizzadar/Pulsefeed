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

			'website', //stream from an individual source, sorted by time

			'topic', //tag streams

			'account', //account streams

			'collection', //collection stream
		);
		private $db;
		protected $stream_type; //stream type
		protected $data = false; //stores the stream data
		private $website_id;
		private $stream_id;
		private $collection_id;
		private $topic_id;
		private $user_id;
		private $source_id;
		public $valid = false;
		private $offset = 0;
		private $since_id = 0;
		private $limit = 40;
		private $account_type;
		private $order = 'popscore';
		private $memcache;

		//setup
		public function __construct( $db, $stream_type = 'hybrid', $memcache = false ) {
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

			//memcache class (hopefully)
			if( !isset( $_GET['nocache'] ) )
				$this->memcache = $memcache;
		}

		//set since_id
		public function set_sinceid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->since_id = $id;
		}

		//set source_id
		public function set_sourceid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->source_id = $id;
		}
		
		//set offset
		public function set_offset( $count ) {
			if( !is_numeric( $count ) ) return false;
			$this->offset = $count;
		}

		//set limit
		public function set_limit( $count ) {
			if( !is_numeric( $count ) ) return false;
			$this->limit = $count;
		}

		//set userid
		public function set_userid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->user_id = $id;
		}

		//set collectionid
		public function set_collectionid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->collection_id = $id;
		}

		//set sourceid
		public function set_websiteid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->website_id = $id;
		}

		//set topicid
		public function set_topicid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->topic_id = $id;
		}

		//set account type
		public function set_accountType( $type ) {
			if( !in_array( $type, array( 'twitter', 'facebook' ) ) ) return false;
			$this->account_type = $type;
		}

		//set order (for topic, public)
		public function set_order( $order ) {
			switch( $order ):
				case 'time':
					$this->order = 'article_time';
				default:
					$this->order = 'popscore';
			endswitch;
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
		protected function sortTime( $a, $b ) {
			return $a['article_time'] < $b['article_time'];
		}

		//sort by popularity function
		protected function sortPopscore( $a, $b ) {
			return $a['popscore'] < $b['popscore'];
		}

		//determin subscriptions
		private function subscribed_data() {
			global $mod_user, $mod_memcache, $mod_config;

			$return = $this->data;
			$subscribed = array(
				'topics' => array(),
				'websites' => array(),
				'users' => array()
			);

			//loop articles
			foreach( $return as $key => $article ):
				foreach( $article['refs'] as $k => $ref ):
					switch( $ref['source_type'] ):
						case 'website':
						case 'topic':
							if( !isset( $subscribed[$ref['source_type'] . 's'][$ref['source_id']] ) )
								$subscribed[$ref['source_type'] . 's'][$ref['source_id']] = count( $mod_memcache->get( 'mod_user_' . $ref['source_type'] . 's', array( array(
									'user_id' => $mod_user->session_userid(),
									$ref['source_type'] . '_id' => $ref['source_id']
								) ), true ) ) == 1;

							$return[$key]['refs'][$k]['subscribed'] = $subscribed[$ref['source_type'] . 's'][$ref['source_id']];
							break;

						case 'share':
							if( !isset( $subscribed['users'][$ref['source_id']] ) )
								$subscribed['users'][$ref['source_id']] = count( $mod_memcache->get( 'mod_user_follows', array( array(
									'user_id' => $mod_user->session_userid(),
									'following_id' => $ref['source_id']
								) ), true ) ) == 1;

							$return[$key]['refs'][$k]['subscribed'] = $subscribed['users'][$ref['source_id']];
							break;

						default:
							$return[$key]['refs'][$k]['subscribed'] = false;
							break;
					endswitch;
				endforeach;
			endforeach;

			//quick sort function
			function sort_refs( $a, $b ) {
				//websites first
				if( $a['source_type'] == 'website' )
					return 0;
				//if subscribed and comparing to a non-website
				elseif( $a['subscribed'] and $b['source_type'] != 'website' )
					return 0;
				else
					return 1;
			}
			//sort to make sources come first
			foreach( $return as $key => $article ):
				if( $mod_config['api'] ):
					$return[$key]['short_description'] = utf8_encode( $article['short_description'] );
					$return[$key]['extended_description'] = utf8_encode( $article['extended_description'] );
					$return[$key]['description'] = utf8_encode( $article['description'] );
				endif;

				usort( $return[$key]['refs'], 'sort_refs' );
			endforeach;

			return $return;
		}

		//load our data from memcache/etc
		private function load_data() {
			global $mod_data, $mod_memcache, $mod_user, $mod_streamcache;
			
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

			//prepare articledata refs
			foreach( $articledata as $k => $v ):
				$articledata[$k]['refs'] = array();
				$articledata[$k]['popscore'] = 0;
			endforeach;

			//switch stream type
			switch( $this->stream_type ):
				//user streams
				case 'hybrid':
				case 'unread':
				case 'popular':
				case 'newest':
				case 'discover':
				case 'account':
					//add references
					foreach( $articles as $article ):
						$id = $article['article_id'];

						//no article found?
						if( !isset( $articledata[$id] ) )
							continue;

						//set popscore, time
						$articledata[$id]['popscore'] = $article['popscore'];
						$articledata[$id]['article_time'] = $article['article_time'];

						//split refs
						$refs = explode( ' :&&: ', $article['refs'] );
						foreach( $refs as $key => $ref ):
							list( $source_type, $source_id, $source_title, $source_data, $origin_id, $origin_title, $origin_data ) = explode( ' :&: ', $ref );
							$refs[$key] = array(
								'source_type' => $source_type,
								'source_id' => $source_id,
								'source_title' => $source_title,
								'source_data' => $source_data,
								'origin_id' => $origin_id,
								'origin_title' => $origin_title,
								'origin_data' => $origin_data
							);
						endforeach;

						foreach( $refs as $ref ):
							//recommend? hide
							if( $ref['source_type'] == 'recommend' ) continue;

							//add ref
							$articledata[$id]['refs'][] = array(
								'source_type' => $ref['source_type'],
								'source_id' => $ref['source_id'],
								'source_title' => $ref['source_title'],
								'source_data' => json_decode( $ref['source_data'], true ),
								'origin_id' => $ref['origin_id'],
								'origin_title' => $ref['origin_title'],
								'origin_data' => json_decode( $ref['origin_data'], true )
							);
						endforeach;
					endforeach;

					//reloop articledata (to sort sources)
					foreach( $articledata as $key => $article ):
						//do we have a source?
						$source = false;
						foreach( $article['refs'] as $ref ):
							if( $ref['source_type'] == 'website' ):
								$source = true;
								break;
							endif;
						endforeach;

						//if we have a source ref, skip
						if( $source )
							continue;

						//find the origin (is only going to be type source)
						foreach( $article['refs'] as $ref ):
							if( $ref['origin_id'] > 0 ):
								$articledata[$key]['refs'][] = array(
									'source_type' => 'website',
									'source_id' => $ref['origin_id'],
									'source_title' => $ref['origin_title'],
									'source_data' => $ref['origin_data'],
									'subscribed' => false
								);
								break;
							endif;
						endforeach;
					endforeach;
					break;

				//public
				case 'public':
					//loop each article
					foreach( $articles as $k => $article ):
						//no article found?
						if( !isset( $articledata[$article['article_id']] ) )
							continue;

						$id = $article['article_id'];

						//increment popscore
						$articledata[$id]['popscore'] = $article['popscore'];

						//set data
						$articledata[$id]['article_time'] = $article['article_time'];

						//remove bits
						unset( $article['article_time'] );
						unset( $article['popscore'] );
						unset( $article['article_id'] );

						$article['source_data'] = json_decode( $article['source_data'], true );

						//add source to actual article
						$articledata[$id]['refs'][] = $article;
					endforeach;
					break;

				//source
				case 'website':
					//get source data
					$data = $mod_memcache->get( 'mod_website', array(
						array(
							'id' => $this->website_id
						)
					) );
					$data = $data[0];
					$domain = parse_url( $data['site_url'] );
					$domain = $domain['host'];

					//loop each article
					foreach( $articles as $k => $article ):
						//no article found?
						if( !isset( $articledata[$article['article_id']] ) )
							continue;

						$id = $article['article_id'];

						//set data
						$articledata[$id]['article_time'] = $article['article_time'];

						//remove bits
						unset( $article['article_time'] );
						unset( $article['article_id'] );

						//set ref data
						$article['source_type'] = 'website';
						$article['source_title'] = $data['site_title'];
						$article['source_data'] = array( 'domain' => $domain );
						
						//add source to actual article
						$articledata[$id]['refs'][] = $article;
					endforeach;
					break;

				//topic
				case 'topic':
					foreach( $articles as $k => $article ):
						//no article found?
						if( !isset( $articledata[$article['article_id']] ) )
							continue;
						
						//set data
						$articledata[$article['article_id']]['article_time'] = $article['article_time'];
						$articledata[$article['article_id']]['popscore'] = $article['popscore'];

						//make ref (if available)
						if( $articledata[$article['article_id']]['source_id'] ):
							$ref = array();
						
							$ref['source_title'] = $articledata[$article['article_id']]['source_title'];
							$ref['source_data'] = json_decode( $articledata[$article['article_id']]['source_data'], true );
							$ref['source_id'] = $articledata[$article['article_id']]['source_id'];
							$ref['source_type'] = 'website';

							$articledata[$article['article_id']]['refs'] = array( $ref );
						endif;
					endforeach;
					break;

				//collection
				case 'collection':
					foreach( $articles as $k => $article ):
						//no article found?
						if( !isset( $articledata[$article['article_id']] ) )
							continue;
						
						//set data
						$articledata[$article['article_id']]['article_time'] = $article['article_time'];

						//make ref (if available)
						if( $articledata[$article['article_id']]['source_id'] ):
							$ref['source_title'] = $articledata[$article['article_id']]['source_title'];
							$ref['source_data'] = json_decode( $articledata[$article['article_id']]['source_data'], true );
							$ref['source_id'] = $articledata[$article['article_id']]['source_id'];
							$ref['source_type'] = 'website';

							$articledata[$article['article_id']]['refs'] = array( $ref );
						endif;
					endforeach;
					break;
			endswitch;

			//articles, stuff for all of them
			foreach( $articledata as $key => $article ):
				//work out stuffs
				$article['time_ago'] = $mod_data->time_ago( $article['time'] );

				//words
				$words = explode( ' ', $article['description'] );
				$short = '';
				$extended = '';

				//loop words
				foreach( $words as $key => $word ):
					if( $key <= 28 )
						$short .= $word . ' ';
					else
						$extended .= $word . ' ';
				endforeach;

				$article['short_description'] = trim( $short, ' .,	' );
				$article['extended_description'] = trim( $extended, ' .,	' );

				//hybrid & unread = unread
				if( in_array( $this->stream_type, array( 'hybrid', 'unread' ) ) ):
					$article['unread'] = 1;
				endif;

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
			$sql = '';
			switch( $this->stream_type ):
				//user streams
				case 'hybrid':
				case 'unread':
				case 'popular':
				case 'newest':
				case 'account':
					$group_by = 'article_id';

					$sql .= '
						SELECT article_id, unread, article_time, MAX( popscore ) AS popscore,
							GROUP_CONCAT(
								cast( concat( source_type, " :&: ", source_id, " :&: ", source_title, " :&: ", source_data, " :&: ", origin_id, " :&: ", origin_title, " :&: ", origin_data ) AS CHAR 
							) ORDER BY source_id DESC SEPARATOR " :&&: " ) AS refs
						FROM mod_user_articles';
					switch( $this->stream_type ):
						case 'hybrid':
							$sql .= '
								WHERE expired = 0
								AND unread = 1';
							$order = 'popscore';
							break;
						case 'popular':
							$sql .= '
								WHERE expired = 0';
							$order = 'popscore';
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
								WHERE expired = 0 
								AND source_type = "' . $this->account_type . '"';
							$sql .= $this->source_id ? 'AND source_id = ' . $this->source_id : '';
							$order = 'article_time';
							break;
					endswitch;

					//remove facebook if not logged in (privacy)
					$sql .= $mod_user->get_userid() == $this->user_id ? '' : '
					AND source_type != "facebook"';

					//add user id bit
					$sql .= '
						AND user_id = ' . $this->user_id;

					break;

				//public stream
				case 'public':
					$sql .= '
						SELECT article_id, source_type, source_id, source_title, source_data, article_time, popscore
						FROM mod_user_articles
						WHERE expired = 0
						AND ( source_type = "website" OR source_type = "twitter" )';
					$order = 'popscore';
					$group_by = 'article_id';
					break;

				//topic stream
				case 'topic':
					$sql .= '
						SELECT article_id, source_id, source_title, source_data, article_time, popscore
						FROM mod_topic_articles
						WHERE expired = 0
						AND topic_id = ' . $this->topic_id;
					$order = 'popscore';
					$group_by = 'article_id';
					break;

				//source stream
				case 'website':
					$sql .= '
						SELECT article_id, article_time, website_id AS source_id
						FROM mod_website_articles
						WHERE website_id = ' . $this->website_id;
					$order = 'article_time';
					$group_by = 'article_id';
					break;

				//colelction stream
				case 'collection':
					$sql .= '
						SELECT article_id, time AS article_time
						FROM mod_collection_articles
						WHERE collection_id = ' . $this->collection_id;
					$order = 'article_time';
					$group_by = 'article_id';
			endswitch;

			//end of query
			$sql .= '
				AND article_id > ' . $this->since_id . '
				' . ( isset( $group_by ) ? 'GROUP BY ' . $group_by : '' ) . '
				ORDER BY ' . $order . ' DESC
				LIMIT ' . ( $this->offset * $this->limit ) . ', ' . $this->limit;

			//run our query, return the data
			if( $data = $this->db->query( $sql ) )
				return $data;
			else
				return false;
		}

		//prepare our data
		public function prepare() {
			global $c_debug;

			//already loaded?
			if( is_array( $this->data ) ) return true;

			//cache name (only caching user, topics & websites atm)
			$cache_name = false;

			//do we have our required info to start (user_id, stream_id, source_id)
			switch( $this->stream_type ):
				case 'hybrid':
				case 'unread':
				case 'popular':
				case 'newest':
					if( !isset( $this->user_id ) ) return false;
					break;
				case 'website':
					if( !isset( $this->website_id ) ) return false;
					$cache_name = 'website_' . $this->website_id;
					break;
				case 'account':
					if( !isset( $this->account_type ) ) return false;
					break;
				case 'collection':
					if( !isset( $this->collection_id ) ) return false;
					break;
				case 'topic':
					if( !isset( $this->topic_id ) ) return false;
					$cache_name = 'topic_' . $this->topic_id;
					break;
			endswitch;

			//?nocache
			//cache?
			if( !isset( $_GET['nocache'] ) and $cache_name and $this->memcache and $this->data = $this->memcache->get( $cache_name ) ):
				$c_debug->add( 'loaded from cache: ' . $cache_name, 'stream' );
			else:
				//load our articles & recommendations
				$this->data = $this->load_data();
				if( !is_array( $this->data ) )
					return false;

				switch( $this->stream_type ):
					//popularity sorted
					case 'hybrid':
					case 'popular':
					case 'public':
					case 'topic':
						usort( $this->data, array( 'mod_stream', 'sortPopscore' ) );
						break;
					//time sorted
					default:
						usort( $this->data, array( 'mod_stream', 'sortTime' ) );
						break;
				endswitch;

				//memcache?
				if( $cache_name and $this->memcache )
					$this->memcache->set( $cache_name, $this->data, 900 );
			endif;

			//work out subscribed
			$this->data = $this->subscribed_data();

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