<?php
	/*
		file: app/lib/stream_site.php
		desc: extension to mod_stream for the site
	*/

	class mod_stream_site extends mod_stream {
		public function __construct( $db, $stream_type = 'hybrid', $image_count = 8 ) {
			parent::__construct( $db, $stream_type, $image_count );
		}

		//build
		public function build() {
			if( !$this->data ) return false;

			//return array
			$return = array();
			$item_count = 0;

			//while we still have items to deal with/display (item count just in case)
			while( count( $this->data['items'] ) > 0 and $item_count < 200 ):
				$item_count++;

				//got a recommendation with a higher time than the next item?
				if( $item_count > 1 ):
					$insert = false;
					foreach( $this->data['recommends'] as $key => $recommend ):
						foreach( $this->data['items'] as $item ):
							if( $recommend['time'] > $item['time'] ):
								$return[] = array(
									'template' => 'item_recommend',
									'items' => array( $recommend )
								);
								unset( $this->data['recommends'][$key] );
								$insert = true;
								break;
							endif;
						endforeach;
						if( $insert )
							break;
					endforeach;
				endif;

				//first item try wide
				if( $item_count == 1 and isset( $this->data['items'][0] ) and !empty( $this->data['items'][0]['image_wide'] ) ):
					$return[] = array(
						'template' => 'item_wide_image',
						'items' => array( $this->data['items'][0] )
					);
					unset( $this->data['items'][0] );
					continue;
				endif;

/*
				//second item (try two half images)
				if( $item_count == 2 and false ):
					if( isset( $this->data['items'][1] ) and isset( $this->data['items'][2] ) and !empty( $this->data['items'][1]['image_half'] ) and !empty( $this->data['items'][2]['image_half'] ) ):
						$return[] = array(
								'template' => 'item_half_image',
								'items' => array(
									$this->data['items'][1],
									$this->data['items'][2]
								)
						);
						unset( $this->data['items'][1] );
						unset( $this->data['items'][2] );
						continue;
					endif;
				endif;

				//third item (4 images)
				if( false and $item_count == 3 ):
					$item_bits = array();
					//loop items, find first 4 images after first 5 posts
					foreach( $this->data['items'] as $key => $item ):
						if( $key < 5 or empty( $item['image_quarter'] ) or count( $item_bits ) >= 4 ) continue;
						$item_bits[] = $item;
					endforeach;
					//et voila
					if( count( $item_bits ) > 2 ):
						$return[] = array(
							'template' => 'item_quarter_image',
							'items' => $item_bits
						);
					endif;
					continue;
				endif;
*/

				//other items, one by one
				$got_item = false;
				foreach( $this->data['items'] as $key => $item ):
					if( $got_item ) continue;

					//try to stick two next to each other (as images if possible)
					if( isset( $this->data['items'][$key + 1] ) ):
						$template = false;

						//two text-only items in a row?
						if( empty( $item['image_quarter'] ) and empty( $this->data['items'][$key + 1]['image_quarter'] ) ):
							$template = 'item_half';
						endif;

						//two half images in a row
						if( !empty( $item['image_half'] ) and !empty( $this->data['items'][$key + 1]['image_half'] ) ):
							$template = 'item_half_image';
						endif;

						//template set?
						if( $template ):
							$return[] = array(
								'template' => $template,
								'items' => array(
									$item,
									$this->data['items'][$key + 1]
								)
							);
							unset( $this->data['items'][$key] );
							unset( $this->data['items'][$key + 1] );
							$got_item = true;
							continue;
						endif;
					endif;

					//still here, single image
					$return[] = array(
						'template' => 'item_wide',
						'items' => array( $item )
					);
					unset( $this->data['items'][$key] );
					$got_item = true;
				endforeach;
			endwhile;

			return $return;
		}
	}
?>