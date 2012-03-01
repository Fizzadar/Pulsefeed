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
			//'discover', //non-subscribed + popular recommendations listed as articles + popular unsubscribed articles, sorted by pop + time
			'source', //stream from an individual source, sorted by time
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

		//load our data from the database
		private function load_data() {
			global $mod_data;
			//load articles & recommends
			$articles = $this->load_articles();
			$recommendations = $this->load_recommendations();
			//make sure they're arrays
			if( !$articles )
				$articles = array();
			if( !$recommendations )
				$recommendations = array();

			//build our return array
			$return = array(
				'items' => array(), //stream items, build them now
				'recommends' => array()
			);

			//duplicates
			$dups = array();

			//articles
			foreach( $articles as $key => $article ):
				//no dupes
				if( in_array( $key, $dups ) ) continue;

				//check if duplicate
				foreach( $articles as $k => $a ):
					if( $k != $key and $this->match_endurls( $article['end_url'], $a['end_url'] ) ):
						//$dups[] = $k;
					endif;
				endforeach;

				//work out stuffs
				$article['source_domain'] = $mod_data->domain_url( $article['source_url'] );
				$article['source_title'] = $mod_data->str_tooltip( $article['source_title'] );
				$article['time_ago'] = $mod_data->time_ago( $article['time'] );
				$article['type'] = 'article';
				$article['recommended'] = ( isset( $article['recommended'] ) and is_numeric( $article['recommended'] ) and $article['recommended'] == $article['id'] );
				$article['unread'] = ( isset( $article['unread'] ) and is_numeric( $article['unread'] ) and $article['unread'] == $article['id'] );
				$article['short_description'] = substr( $article['description'], 0, 200 ) . ( strlen( $article['description'] ) > 200 ? '...' : '' );
				$article['shorter_description'] = substr( $article['description'], 0, 120 ) . ( strlen( $article['description'] ) > 120 ? '...' : '' );
				$article['expired'] = $article['expired_unread'];
				$return['items'][] = $article;
			endforeach;

			//recommendations
			foreach( $recommendations as $key => $recommend ):
				$recommend['type'] = 'recommend';
				$recommend['source_domain'] = $mod_data->domain_url( $recommend['site_url'] );
				$return['recommends'][] = $recommend;
			endforeach;

			//return it
			return $return;
		}

		//load articles (db)
		private function load_articles() {
			//get our logged in user (for recommend check)
			global $mod_user, $mod_config;
			$logged_userid = false;
			if( $mod_user->check_login() )
				$logged_userid = $mod_user->get_userid();

			$extra_tables = '';
			$order = 'mod_article.popularity_score';
			switch( $this->stream_type ):
				//unread
				case 'unread':
					$order = 'mod_article.time';
				case 'hybrid':
					$extra_tables .= ', mod_user_unread';
					break;
				//all
				case 'newest':
					$order = 'mod_article.time';
				case 'popular':
					$extra_tables .= ', mod_user_sources';
					break;
				//source
				case 'source':
					$order = 'mod_article.time';
			endswitch;

			//build query, start selecting basic stuff
			$sql = '
				SELECT
					mod_article.id, mod_article.source_id, mod_article.title, mod_article.url, mod_article.end_url, mod_article.description, mod_article.time, mod_article.recommendations, mod_article.popularity, mod_article.popularity_score, mod_article.image_quarter, mod_article.image_third, mod_article.image_half, mod_article.expired_unread,
					' . ( $logged_userid ? 'mod_user_recommends.article_id AS recommended, uread.article_id AS unread, usub.source_id AS subscribed,' : '' ) . '
					mod_source.site_title AS source_title, mod_source.site_url AS source_url
				FROM
					' . (
							$logged_userid ? 
							'mod_article
							LEFT JOIN mod_user_recommends ON mod_article.id = mod_user_recommends.article_id AND mod_user_recommends.user_id = ' . $logged_userid . '
							LEFT JOIN mod_user_unread AS uread ON mod_article.id = uread.article_id AND uread.user_id = ' . $logged_userid . '
							LEFT JOIN mod_user_sources AS usub ON mod_article.source_id = usub.source_id AND usub.user_id = ' . $logged_userid : 
							'mod_article'
						) . ',
					mod_source' . $extra_tables . '
				WHERE 
					mod_source.id = mod_article.source_id
			';

			//decide where values/etc
			switch( $this->stream_type ):
				//unread only items
				case 'hybrid':
					$sql .= '
						AND mod_article.popularity_score != 0
						AND mod_article.expired_stream = 0
					';
				case 'unread':
					$sql .= '
						AND mod_user_unread.article_id = mod_article.id
						AND mod_user_unread.user_id = ' . $this->user_id . '
					';
					break;
				//popular
				case 'popular':
				case 'public':
					$sql .= '
						AND mod_article.expired_stream = 0
						AND mod_article.popularity_score != 0
					';
					if( $this->stream_type == 'public' ) break;
				//new
				case 'newest':
					$sql .= '
						AND mod_user_sources.source_id = mod_article.source_id
						AND mod_user_sources.user_id = ' . $this->user_id . '
					';
					break;
				//source stream
				case 'source':
					$sql .= '
						AND mod_source.id = ' . $this->source_id . '
					';
			endswitch;

			//end of query (popularity_score must be above 0, aka must be ranked [min = 5])
			$sql .= '
				AND mod_article.id > ' . $this->since_id . '
				GROUP BY mod_article.id
				ORDER BY ' . $order . ' DESC
				LIMIT ' . $this->offset . ', 64
			';

			//run our query, return the data
			if( $data = $this->db->query( $sql ) )
				return $data;
			else
				return false;
		}

		//load recommendations (from db)
		private function load_recommendations() {
			//decide our where values according to feed type
			switch( $this->stream_type ):
				//nothing if unread & user streams
				case 'unread':
				case 'public':
				case 'source':
					return array();
			endswitch;
			
			//build query, start selecting basic stuff
			$sql = '
				SELECT
					mod_article.id, mod_article.source_id, mod_article.title,
					core_user.id AS user_id, core_user.name AS user_name, mod_user_recommends.time,
					mod_source.site_title, mod_source.site_url
				FROM
					mod_article, core_user, mod_user_recommends, mod_user_follows, mod_source, mod_user_unread
				WHERE
					mod_article.id = mod_user_recommends.article_id
					AND mod_source.id = mod_article.source_id
					AND mod_user_recommends.user_id = mod_user_follows.following_id
					AND core_user.id = mod_user_recommends.user_id
					AND mod_user_follows.user_id = ' . $this->user_id . '
					AND mod_user_unread.article_id = mod_user_recommends.article_id
					AND mod_user_unread.user_id = ' . $this->user_id . '
			';

			//end
			$sql .= '
				GROUP BY mod_article.id
				ORDER BY mod_user_recommends.time DESC
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
			if( $this->data ) return true;
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
			endswitch;
			//load our articles & recommendations
			$this->data = $this->load_data();
			if( !$this->data )
				return false;

			//sort -> already sorted by poptime/pop/time, now to push articles to be from differing sources
			switch( $this->stream_type ):
				case 'popular':
				case 'public':
				case 'hybrid':
					//array of used articles
					$used_articles = array();

					//get an array of our used source ids (none)
					$source_ids = array();
					foreach( $this->data['items'] as $item ):
						$source_ids[$item['source_id']] = 0;
					endforeach;

					//loop articles
					$articles = array();
					foreach( $this->data['items'] as $key => $item ):
						//already used?
						if( isset( $used_articles[$key] ) ) continue;

						//less than two articles?
						if( false and count( $articles ) < 2 ):
							$articles[] = $item;
							$used_articles[$key] = true;
							$source_ids[$item['source_id']]++;
							continue;
						endif;

						//has this source already got content (x2) in the stream? look for another.
						if( $source_ids[$item['source_id']] > 2 ):
							$found = false;

							//locate an article with an unused source
							foreach( $this->data['items'] as $k => $a ):
								if( isset( $used_articles[$k] ) ) continue;

								if( $source_ids[$a['source_id']] < 2 ):
									$found = true;
									$articles[] = $a;
									$used_articles[$k] = true;
									$source_ids[$a['source_id']]++;
									break;
								endif;
							endforeach;

							//found an article from an unused source? continue on
							if( $found ):
								continue;
							endif;
						endif;

						//last two articles and this all from same source?
						if( count( $articles ) > 1 and $item['source_id'] == $articles[count( $articles ) - 1]['source_id'] and $item['source_id'] == $articles[count( $articles ) - 2]['source_id'] ):
							$found = false;

							//find an article with a different source id
							foreach( $this->data['items'] as $k => $a ):
								if( isset( $used_articles[$k] ) ) continue;

								if( $a['source_id'] != $item['source_id'] ):
									$found = true;
									$articles[] = $a;
									$used_articles[$k] = true;
									$source_ids[$a['source_id']]++;
									break;
								endif;
							endforeach;

							//did we find an article? woop woop
							if( $found ):
								continue;
							endif;
						endif;

						//default action
						$articles[] = $item;
						$used_articles[$key] = true;
						$source_ids[$item['source_id']]++;
					endforeach;

					//set our net articles
					$this->data['items'] = $articles;
			endswitch;
			

			return true;
		}

		//send data back to template
		public function get_data() {
			if( !$this->data ) return false;
			return $this->data;
		}
	}
?>