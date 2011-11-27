<?php
	/*
		file: app/lib/article.php
		desc: extension on simplepie's item class to get the data we want (and extract using htmldom if needed - cheeky)
	*/

	class mod_article extends SimplePie_Item {
		private $images = array();
		private $article;

		//return the time in unix epoch format
		public function get_time() {
			return $this->get_date( 'U' );
		}

		//get/save & generate locally image (select best/main/biggest image)
		public function get_thumb() {
			//if we've got an attached thumbnail, add it to our images
			if( $e = $this->get_enclosure() and $e->get_thumbnail() )
				$this->images[] = $e->get_thumbnail();

			//loop the images for this item, pick the best (biggest?)
			$max_size = 0;
			$thumb_img = -1;
			foreach( $this->images as $key => $image ):
				$size = getimagesize( $image );
				$size = $size[0] * $size[1];
				if( $size > $max_size ):
					$thumb_img = $key;
					$max_size = $size;
				endif;
			endforeach;

			//got an image?
			if( $thumb_img > -1 ):
				$thumb_name = 'data/thumbs/' . basename( $this->images[$thumb_img] );
				//no thumb? generate it!
				if( !file_exists( $thumb_name ) ):
					//generate the thumbnail
					$resize = new resize( $this->images[$thumb_img] );
					$resize->resizeImage( 200, 100, 'crop' );
					$resize->saveImage( $thumb_name );
				endif;
				//return the thumbnail
				return $thumb_name;
			endif;

			//still here? obv no thumb
			return false;
		}

		//get/rip the article
		public function get_article( $hack = '' ) {
			global $c_config;

			//already got the article?
			if( !$this->article ):
				//do we have a hack?
				if( !empty( $hack ) and file_exists( 'app/lib/hacks/' . $hack . '.php' ) )
					$this->article = include( 'app/lib/hacks/' . $hack . '.php' );
				else
					$this->article = $this->get_content();
			endif;

			//now we have our article, lets process the images/html
			$html = new simple_html_dom();
			$html->load( $this->article );
			foreach( $html->find( 'img' ) as $img ):
				//remove any set w/h
				$img->width = 'auto';
				$img->height = 'auto';

				//work out the image extension
				$ext = basename( $img->src ); //name only
				$ext = explode( '?', $ext ); //split at query string
				$ext = $ext[0]; //set to bit before query string
				$ext = explode( '.', $ext ); //split at dot
				$ext = $ext[count( $ext ) - 1]; //get item after last dot (extension)
				//local name
				$img_name = 'data/images/' . sha1( $img->src ) . '.' . $ext;

				//allowed ext?
				if( in_array( $ext, array( 'jpeg', 'jpg', 'png', 'gif' ) ) ):
					//do we already have this?
					if( !file_exists( $img_name ) ):
						//download/save our image locally
						$download = false;
						$download = @file_put_contents( $img_name, @file_get_contents( $img->src ) );
						//change src if downloaded & add to images
						if( $download ):
							$this->images[] = $img_name;
							$img->src = $c_config['root'] . '/' . $img_name;
						endif;
					else:
						//change src & add to available images
						$this->images[] = $img_name;
						$img->src = $c_config['root'] . '/' . $img_name;
					endif;
				endif;

				//testing
				if( preg_match( '/feeds.feedburner.com/', $img->src ) )
					$img->src= '';
			endforeach;

			//set article to our edited/fixed html
			$this->article = $html;

			//return our article
			return $this->article;
		}

		//get/make our summary
		public function get_summary() {
			//already got the content?
			if( !$this->article )
				$this->article = $this->get_article();

			//get article, strip tags, split into words
			$article = strip_tags( $this->article );
			$words = explode( ' ', $article );

			//build our summary
			$summary = '';
			foreach( $words as $id => $word )
				if( $id < 30 )
					$summary .= $word . ' ';
			$summary = trim( $summary );

			//summary smaller than article?
			if( strlen( $summary ) < strlen( $article ) )
				$summary .= '...';

			//return summary
			return $summary;
		}
	}
?>