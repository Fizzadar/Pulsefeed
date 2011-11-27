<?php
	/*
		file: app/lib/source.php
		desc: locate & load feeds, app <-> simplepie integration
	*/

	class mod_source {
		private $pie;

		//start the class
		public function __construct() {
			global $mod_config;
			//start simplepie
			$this->pie = new SimplePie();
			$this->pie->enable_cache( false );
			$this->pie->set_useragent( $mod_config['useragent'] );
		}

		//find a feed (based on url)
		public function find( $search_url ) {
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

		//load a feeds data
		public function load( $feed_url ) {
			//set the url
			$this->pie->set_feed_url( $feed_url );
			$this->pie->set_item_class( 'mod_article' );
			$this->pie->init();
			
			//return the items
			return $this->pie->get_items();
		}
	}
?>