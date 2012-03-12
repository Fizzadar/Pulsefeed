<?php
	/*
		file: app/lib/feed.php
		desc: extension on simplepie's item class to get the data we want
	*/

	class mod_feed extends SimplePie_Item {
		//return the time in unix epoch format
		public function get_time() {
			$date = $this->get_date( 'U' );
			return is_numeric( $date ) ? $date : time(); //time wrong? use now
		}

		//get mod_feed_article item for this item
		public function get_item() {
			//get feed content
			$content = $this->get_content();

			//content the same as description?
			if( $content == $this->get_description() ):
				$content = false;
			endif;

			//content links itself?
			$html = new simple_html_dom();
			$html->load( $content );
			foreach( $html->find( 'a' ) as $link )
				if( $link->src == $this->get_permalink() or is_numeric( strpos( $this->get_permalink(), $link->src ) ) )
					$content = false;

			//return mod feed article
			return new mod_feed_article( $this->get_permalink(), $content );
		}

		//get_title trim
		public function get_title() {
			return trim( parent::get_title() );
		}
	}
?>