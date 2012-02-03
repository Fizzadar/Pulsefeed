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
			$wide_count = 0;
			$half_count = 0;

			//while we still have items to deal with/display (item count just in case)
			while( count( $this->data['items'] ) > 0 and $item_count < 200 ):
				$item_count++;

				//lets go
				$got_item = false;
				foreach( $this->data['items'] as $key => $item ):
					//just in case!
					if( $got_item ) break;

					//try to stick two next to each other (as images if possible)
					if( isset( $this->data['items'][$key + 1] ) and $item_count > 1 ):
						$half = false;

						//two text-only items in a row?
						if( empty( $item['image_quarter'] ) and empty( $this->data['items'][$key + 1]['image_quarter'] ) )
							$half = true;

						//half followed by text only?
						if( !empty( $item['image_half'] ) and empty( $this->data['items'][$key +1]['image_quarter'] ) )
							$half = true;

						//text only followed by half?
						if( empty( $item['image_quarter'] ) and !empty( $this->data['items'][$key + 1]['image_half'] ) )
							$half = true;

						//two half images in a row
						if( !empty( $item['image_half'] ) and !empty( $this->data['items'][$key + 1]['image_half'] ) )
							$half = true;

						//two previous wides?
						if( $wide_count >= 2 and $half_count < 1 )
							$half = true;

						//template set?
						if( $half ):
							$return[] = array(
								'template' => 'item_half',
								'items' => array(
									$item,
									$this->data['items'][$key + 1]
								)
							);
							unset( $this->data['items'][$key] );
							unset( $this->data['items'][$key + 1] );
							$got_item = true;
							$wide_count = 0;
							$half_count++;
							break;
						endif;
					endif;

					//still here, single image
					$return[] = array(
						'template' => 'item_wide',
						'items' => array( $item )
					);
					unset( $this->data['items'][$key] );
					$got_item = true;
					$wide_count++;
					$half_count = 0;
					break;
				endforeach;
			endwhile;

			return array(
				'items' => $return,
				'recommends' => $this->data['recommends']
			);
		}
	}
?>