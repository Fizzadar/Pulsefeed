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
							$this->data[$i]['short_description'] = $this->data[$i]['shorter_description'];
							$articles['col3'][] = $this->data[$i];
						endfor;

						//now generate other 2 cols
						$col2 = false;
						for( $i = 0; $i < $length - $third; $i++ ):
							//choose the col
							if( $col2 ):
								$articles['col2'][] = $this->data[$i];
							else:
								$articles['col1'][] = $this->data[$i];
							endif;

							//switch
							$col2 = !$col2;
						endfor;
						break;
					endif;

				//3 col even
				case 'unread':
				case 'newest':
				case 'discover':
				case 'source':
				case 'account':
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

		//build features
		private function build_features() {
			//list of topics (big words)
			$topics = array();
			//the actual features
			$features = array();
			//return array
			$return = array();

			//build list of topics and ids
			foreach( $this->data as $key => $item ):
				$words = explode( ' ', $item['title'] );
				foreach( $words as $word ):
					if( strlen( $word ) >= 5 ):
						if( !isset( $topics[$word] ) ):
							$topics[$word] = array(
								'count' => 1,
								'ids' => array(
									$key
								)
							);
						else:
							$topics[$word]['count']++;
							$topics[$word]['ids'][] = $key;
						endif;
					endif;
				endforeach;
			endforeach;

			//remove all shit topics (min 3)
			foreach( $topics as $key => $topic )
				if( $topic['count'] < 3 )
					unset( $topics[$key] );

			//match common topics
			foreach( $topics as $word1 => $topic1 ):
				foreach( $topics as $word2 => $topic2 ):
					//ignore same/reverse words
					if( $word1 == $word2 or isset( $features[$word2 . '_' . $word1] ) )
						continue;

					//loop ids from each topic
					foreach( $topic1['ids'] as $id1 ):
						foreach( $topic2['ids'] as $id2 ):
							//matching id's!
							if( $id1 == $id2 ):
								if( !isset( $features[$word1 . '_' . $word2] ) ):
									$features[$word1 . '_' . $word2] = array(
										$id1
									);
								else:
									$features[$word1 . '_' . $word2][] = $id1;
								endif;
							endif;
						endforeach;
					endforeach;
				endforeach;
			endforeach;

			//finally, build them features
			foreach( $features as $words => $ids ):
				//min 3 topics
				if( count( $ids ) < 3 )
					continue;

				//build the feature
				$tmp = array(
					'topics' => explode( '_', $words ),
					'articles' => array()
				);
				foreach( $ids as $id ):
					$tmp['articles'][] = $this->data[$id];
					unset( $this->data[$id] );
				endforeach;
				
				//sort the articles
				usort( $tmp['articles'], array( 'mod_stream', 'sortPopscore' ) );

				//make sure first item has an image (well, try)
				if( empty( $tmp['articles'][0]['image_half'] ) and empty( $tmp['articles'][0]['image_quarter'] ) ):
					foreach( $tmp['articles'] as $article ):
						if( !empty( $article['image_half'] ) ):
							$tmp['articles'][0]['image_half'] = $article['image_half'];
							break;
						elseif( !empty( $article['image_quarter'] ) ):
							$tmp['articles'][0]['image_quarter'] = $article['image_quarter'];
							break;
						endif;
					endforeach;
				endif;

				//add to return
				$return[] = $tmp;
			endforeach;

			//return
			return $return;
		}
	}
?>