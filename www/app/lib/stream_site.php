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
			global $mod_data;

			if( !is_array( $this->data ) ) return false;
			$features_type = array( 'hybrid', 'popular', 'public' );

			//return arrays (3 cols)
			$articles = array(
				'col1' => array(),
				'col2' => array(),
				'col3' => array()
			);
			//build features?
			$features = in_array( $this->stream_type, $features_type ) ? $this->build_features() : array();

			//now re-do keys
			$this->data = array_values( $this->data );

			//decide if we're even cols
			switch( $this->stream_type ):
				//2 col main, 1 col upcoming
				case 'hybrid':
				case 'popular':
				case 'public':
					if( count( $this->data ) > 2 ):
						//get 1/3 length
						$length = count( $this->data );
						$third = round( $length / 3 );

						//get col3, items from length - third to length
						for( $i = $length - $third; $i < $length; $i++ ):
							$articles['col3'][] = $this->data[$i];
							$this->data[$i]['used'] = true;
						endfor;

						//now generate other 2 cols
						/*for( $i = 0; $i < $length - $third; $i++ ):
							//choose the col
							if( $i < ( ( $length - $third ) / 2 ) ):
								$articles['col1'][] = $this->data[$i];
							else:
								$articles['col2'][] = $this->data[$i];
							endif;
						endfor;*/
						$col = 1;
						foreach( $this->data as $item ):
							if( isset( $item['used'] ) ) continue;
						
							$articles['col' . $col][] = $item;
							$col++;
							if( $col == 3 )
								$col = 1;
						endforeach;
						break;
					endif;

				//3 col even
				default:
					$col = 1;

					//add each item, increment col (back to 1 if on 3)
					foreach( $this->data as $k => $item ):
						$articles['col' . $col][] = $item;
						$col++;
						if( $col > 3 ) $col = 1;
					endforeach;
			endswitch;

			//finally return our array
			return array(
				'features' => $features,
				'items' => $articles
			);
		}

		//build features <=== needs COMPLETE re-work (only balances 2 words currently)
		private function build_features() {
			return array();
		}
	}
?>