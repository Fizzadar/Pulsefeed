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
			'user', //user-created consisting of user sources
			//'source', //stream from an individual source, sorted by time
		);
		private $db;
		protected $stream_type; //stream type
		protected $data = false; //stores the stream data
		private $source_id;
		private $stream_id;
		private $user_id;
		public $valid = false;
		private $offset = 0;

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

		//set streamid
		public function set_streamid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->stream_id = $id;
		}

		//set sourceid
		public function set_sourceid( $id ) {
			if( !is_numeric( $id ) ) return false;
			$this->source_id = $id;
		}

		//usort poptime
		private function usort_poptime( $a, $b ) {
			return $a['popularity_time'] < $b['popularity_time'];
		}

		//usort pop
		private function usort_pop( $a, $b ) {
			return $a['popularity'] < $b['popularity'];
		}

		//usort time
		private function usort_time( $a, $b ) {
			return $a['time'] < $b['time'];
		}

		//sort data by pop_time
		private function sort_poptime() {
			if( !$this->data )
				return false;
			//sort
			return usort( $this->data['items'], array( 'mod_stream', 'usort_poptime' ) );
		}

		//sort data by pop
		private function sort_pop() {
			if( !$this->data )
				return false;
			//sort
			return usort( $this->data['items'], array( 'mod_stream', 'usort_pop' ) );
		}

		//sort data by time
		private function sort_time() {
			if( !$this->data )
				return false;
			//sort
			return usort( $this->data['items'], array( 'mod_stream', 'usort_time' ) );
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

			//articles
			foreach( $articles as $article ):
				//find (& skip less popular) duplicate articles
				$dup = false;
				foreach( $articles as $a )
					if( $article['title'] == $a['title'] and $article['popularity'] < $a['popularity'] )
						$dup = true;
				if( $dup )
					continue;

				$article['source_domain'] = $mod_data->domain_url( $article['source_url'] );
				$article['type'] = 'article';
				$article['recommended'] = ( isset( $article['recommended'] ) and is_numeric( $article['recommended'] ) and $article['recommended'] == $article['id'] );
				$article['short_description'] = substr( $article['description'], 0, 150 ) . ( strlen( $article['description'] ) > 150 ? '...' : '' );
				$return['items'][] = $article;
			endforeach;
			//recommendations
			foreach( $recommendations as $key => $recommend ):
				//already displaying the article?
				$dup = false;
				foreach( $articles as $article )
					if( $article['id'] == $recommend['id'] )
						$dup = true;
				if( $dup )
					continue;
				 
				$recommend['type'] = 'recommend';
				$return['recommends'][] = $recommend;
			endforeach;

			//return it
			return $return;
		}

		//load articles (db)
		private function load_articles() {
			//get our logged in user (for recommend check)
			global $mod_user;
			$logged_userid = false;
			if( $mod_user->check_login() )
				$logged_userid = $mod_user->get_userid();

			$extra_tables = '';
			$order = 'mod_article.popularity_time';
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
				//custom stream
				case 'user':
					$extra_tables .= ', mod_stream, mod_stream_sources';
					break;
			endswitch;

			//build query, start selecting basic stuff
			$sql = '
				SELECT
					mod_article.id, mod_article.source_id, mod_article.title, mod_article.url, mod_article.end_url, mod_article.description, mod_article.time, mod_article.recommendations, mod_article.popularity, mod_article.popularity_time, mod_article.image_quarter, mod_article.image_third, mod_article.image_half, mod_article.image_wide,
					' . ( $logged_userid ? 'mod_user_recommends.article_id AS recommended,' : '' ) . '
					mod_source.site_title AS source_title, mod_source.site_url AS source_url
				FROM
					' . (
							$logged_userid ? 
							'mod_article LEFT JOIN mod_user_recommends ON mod_article.id = mod_user_recommends.article_id AND mod_user_recommends.user_id = ' . $logged_userid . '' : 
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
						AND mod_article.time > ' . ( time() - 24 * 3600 ) . '
					';
					if( $this->stream_type == 'public' )
						break;
				//new
				case 'newest':
					$sql .= '
						AND mod_user_sources.source_id = mod_article.source_id
						AND mod_user_sources.user_id = ' . $this->user_id . '
					';
					break;
				//user stream
				case 'user':
					$sql .= '
						AND mod_stream_sources.source_id = mod_article.source_id
						AND mod_stream_sources.stream_id = mod_stream.id
						AND mod_stream.id = ' . $this->stream_id . '
						AND mod_stream.user_id = ' . $this->user_id . '
						AND mod_article.time > ' . ( time() - 24 * 3600 ) . '
					';
			endswitch;

			//end of query
			$sql .= '
				GROUP BY mod_article.id
				ORDER BY ' . $order . ' DESC
				LIMIT ' . $this->offset . ', 32
			';

			//run our query, return the data
			if( $data = $this->db->query( $sql ) )
				return $data;
			else
				return false;
		}

		//load recommendations (from db)
		private function load_recommendations() {
			$extra_tables = '';
			switch( $this->stream_type ):
				case 'hybrid':
					$extra_tables .= ', mod_user_unread';
			endswitch;

			//build query, start selecting basic stuff
			$sql = '
				SELECT
					mod_article.id, mod_article.source_id, mod_article.title, mod_article.url, mod_article.end_url, mod_article.description, mod_article.recommendations, mod_article.popularity, mod_article.image_quarter, mod_article.image_third, mod_article.image_half, mod_article.image_wide, mod_article.facebook_shares, mod_article.facebook_comments, mod_article.delicious_saves, mod_article.twitter_links, mod_article.digg_diggs,
					core_user.id AS user_id, core_user.name AS user_name, mod_user_recommends.time
				FROM
					mod_article, core_user, mod_user_recommends, mod_user_follows' . $extra_tables . '
				WHERE
					mod_article.id = mod_user_recommends.article_id
					AND mod_user_recommends.user_id = mod_user_follows.following_id
					AND core_user.id = mod_user_recommends.user_id
			';

			//decide our where values according to feed type
			switch( $this->stream_type ):
				//nothing if unread & user streams
				case 'unread':
				case 'user':
					return array();
				case 'hybrid':
					$sql .= '
						AND mod_user_unread.article_id = mod_article.id
						AND mod_user_unread.user_id = ' . $this->user_id . '
					';
				case 'popular':
				case 'newest':
					$sql .= '
						AND mod_user_follows.user_id = ' . $this->user_id . '
					';
			endswitch;

			//end
			$sql .= '
				GROUP BY mod_article.id
				ORDER BY mod_user_recommends.time
				LIMIT ' . $this->offset . ', 32
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
				case 'user':
					if( !isset( $this->stream_id ) ) return false;
				case 'hybrid':
				case 'unread':
				case 'popular':
				case 'newest':
					if( !isset( $this->user_id ) ) return false;
					break;
				case 'source':
					if( !isset( $this->source_id ) ) return false;
			endswitch;
			//load our articles & recommendations
			$this->data = $this->load_data();
			if( !$this->data )
				return false;

			//sort according to stream type
			switch( $this->stream_type ):
				case 'hybrid':
				case 'public':
					$this->sort_poptime();
					break;
				case 'newest':
				case 'unread':
					$this->sort_time();
					break;
				case 'popular':
					$this->sort_pop();
					break;
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