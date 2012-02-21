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
			
			if( !$this->data ) return false;
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
			$this->data['items'] = array_values( $this->data['items'] );

			//make shorter desc
			foreach( $this->data['items'] as $k => $v ):
				$this->data['items'][$k]['shorter_description'] = substr( $this->data['items'][$k]['description'], 0, 120 ) . ( strlen( $this->data['items'][$k]['description'] ) > 120 ? '...' : '' );
				$this->data['items'][$k]['time_ago'] = $mod_data->time_ago( $this->data['items'][$k]['time'] );
			endforeach;

			switch( $this->stream_type ):
				//2 col main, 1 col upcoming
				case 'hybrid':
				case 'popular':
				case 'public':
					//get 1/3 length
					$length = count( $this->data['items'] );
					$third = round( $length / 3 );

					//get col3, items from length - third to length
					for( $i = $length - $third; $i < $length; $i++ ):
						$this->data['items'][$i]['short_description'] = $this->data['items'][$i]['shorter_description'];
						$articles['col3'][] = $this->data['items'][$i];
					endfor;

					//now generate other 2 cols
					$col2 = false;
					for( $i = 0; $i < $length - $third; $i++ ):
						//choose the col
						if( $col2 ):
							$articles['col2'][] = $this->data['items'][$i];
						else:
							$articles['col1'][] = $this->data['items'][$i];
						endif;

						//switch
						$col2 = !$col2;
					endfor;
					break;

				//3 col even
				case 'unread':
				case 'newest':
				case 'discover':
				case 'source':
					$col = 1;

					//add each item, increment col (back to 1 if on 3)
					foreach( $this->data['items'] as $k => $item ):
						$articles['col' . $col][] = $item;
						$col++;
						if( $col > 3 ) $col = 1;
					endforeach;
			endswitch;

			//work out recommend time agos
			foreach( $this->data['recommends'] as $k => $recommend ):
				$this->data['recommends'][$k]['time_ago'] = $mod_data->time_ago( $this->data['recommends'][$k]['time'] );
			endforeach;

			//finally return our array
			return array(
				'features' => $features,
				'items' => $articles,
				'recommends' => $this->data['recommends']
			);
		}

		//build features
		private function build_features() {
			return array();
		}
	}
?>