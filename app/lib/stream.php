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
			'popular', //24 hour popular, recommendations, sorted by pop + time
			'new', //all, recommendations, sorted by time
			'public', //24 hour all articles, sorted by pop + time
			'discover', //non-subscribed + popular recommendations listed as articles + popular unsubscribed articles, sorted by pop + time
		);
		private $stream_type; //stream type
		private $user_id; //userid
		private $db; //stores our db
		private $data = false; //stores the stream data

		//setup
		public function __construct( $db, $user_id = 0, $stream_type = 'hybrid' ) {
			//invalid stream type?
			if( !in_array( $stream_type, $this->stream_types ) )
				return false;

			//check our db
			if( !method_exists( $db, 'query' ) )
				return false;
			
			//set our db accessor
			$this->db = $db;

			//set the stream type & userid
			$this->stream_type = $stream_type;
			$this->user_id = $user_id;
		}

		//sort data by pop_time
		public function sort_poptime() {
			if( !$this->data ) return false;
		}

		//sort data by pop
		public function sort_pop() {
			if( !$this->data ) return false;
		}

		//sort data by time
		public function sort_time() {
			if( !$this->data ) return false;
		}

		//load our data
		public function load_data() {
			
		}
	}
?>