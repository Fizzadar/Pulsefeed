<?php
	//work out video id
	$url = parse_url( $this->get_end_url() );

	//fail text
	$fail = '';

	if( isset( $url['query'] ) ):
		$qbits = explode( '&', $url['query'] );
		//loop each query bit
		foreach( $qbits as $bit ):
			$bit = explode( '=', $bit );
			//v link? woop woop
			if( count( $bit ) == 2 and $bit[0] == 'v' ):
				$id = $bit[1];
				//get data from youtube
				if( $data = file_get_contents( 'http://gdata.youtube.com/feeds/api/videos/' . $id . '?alt=json' ) ):
					if( $data = json_decode( $data ) ):
						if( isset( $data->entry->title ) ):
							$title = false;
							foreach( $data->entry->title as $t )
								if( $t != 'text' )
									$title = $t;

							//gawt title? whoop whoop
							if( $title ):
								$title = ucwords( strtolower( $title ) );
								$return = array(
									true, //success ?
									'<iframe class="youtube_video" src="http://www.youtube.com/embed/' . $id . '?controls=0" frameborder="0" allowfullscreen></iframe>', //desc
									'<iframe class="youtube_video" src="http://www.youtube.com/embed/' . $id . '?controls=0" frameborder="0" allowfullscreen></iframe>', //summary
									$title, //title
									'http://www.youtube.com/embed/' . $id, //link
									'video' //text
								);
								//print_r( $return );
								return $return;
							else:
								$fail = 'no title found in entry->data->title';
							endif;
						else:
							$fail = 'no entry->data->title';
						endif;
					else:
						$fail = 'json encode';
					endif;
				else:
					$fail = 'failed to get json data';
				endif;
			endif;
		endforeach;
	else:
		$fail = 'no query in url';
	endif;

	//still here? :(
	echo 'youtube plugin failed: ' . $fail . ' :: ' . $this->get_end_url() . PHP_EOL;
	return false;
?>