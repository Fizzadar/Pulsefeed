<?php
	/*
		file: app/lib/article.php
		desc: extension on simplepie's item class to get the data we want (and extract using htmldom if needed - cheeky)
	*/

	class mod_article extends SimplePie_Item {
		private $images = array();
		private $endlink;
		private $article;

		//return the time in unix epoch format
		public function get_time() {
			return $this->get_date( 'U' );
		}

		//to-do: add get_endlink() to get the end like (i.e mashable.com > feedproxy.google.com)
		
		//get/save & generate locally image (select best/main/biggest image)
		public function get_thumbs() {
			//config
			$config = array(
				'quarter' => array(
					'w' => 200,
					'h' => 110,
				),
				'third' => array(
					'w' => 267,
					'h' => 150,
				),
				'half' => array(
					'w' => 400,
					'h' => 220,
				),
				'wide' => array(
					'w' => 800,
					'h' => 220,
				),
			);

			//if we've got an attached thumbnail, add it to our images (after saving!)
			if( $e = $this->get_enclosure() and $e->get_thumbnail() and $e_thumb = $this->save_image( $e->get_thumbnail() ) )
				$this->images[] = $e_thumb;

			$return = array();

			//loop size options
			foreach( $config as $conf_key => $conf ):
				//loop the images for this item, pick the best (biggest?)
				$max_size = 0;
				$thumb_img = -1;
				foreach( $this->images as $key => $image ):
					//calculate size
					$s = getimagesize( $image );
					$size = $s[0] * $s[1];
					//image too small? fuck it
					if( $size < ( $conf['w'] * $conf['h'] ) or $s[0] < $conf['w'] or $s[1] < $conf['h'] ):
						continue;
					endif;

					//check some color shit (find most colorful)
					//--COMINGSOON

					//check max size
					if( $size > $max_size ):
						$thumb_img = $key;
						$max_size = $size;
					endif;
				endforeach;

				//got an image?
				if( $thumb_img > -1 ):
					$thumb_name = 'data/thumbs/' . $conf_key . '/' . basename( $this->images[$thumb_img] );
					//no thumb? generate it!
					if( !file_exists( $thumb_name ) ):
						//generate the thumbnail
						$resize = new resize( $this->images[$thumb_img] );
						@$resize->resizeImage( $conf['w'], $conf['h'], 'crop' );
						@$resize->saveImage( $thumb_name );
					endif;
					//return the thumbnail
					$return[$conf_key] = $thumb_name;
				endif;
			endforeach;

			//still here? obv no thumb
			return $return;
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

				//(try to) save the image locally
				if( $img_name = $this->save_image( $img->src ) ):
					$this->images[] = $img_name;
					$img->src = $c_config['root'] . '/' . $img_name;
				endif;

				//testing/removing annoying feedburner shit
				if( preg_match( '/feeds.feedburner.com/', $img->src ) )
					$img->src= null;
			endforeach;

			//work links
			foreach( $html->find( 'a' ) as $link ):
				$link->target = '_blank';
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
			$wordcount = 0;
			foreach( $words as $id => $word ):
				$word = trim( $word );
				if( $wordcount < 60 and !empty( $word ) ):
					$summary .= $word . ' ';
					$wordcount++;
				endif;
			endforeach;
			$summary = trim( $summary );

			//summary smaller than article?
			if( strlen( $summary ) < strlen( $article ) )
				$summary .= '...';

			//return summary
			return $summary;
		}

		//save a remote image locally
		private function save_image( $image_url ) {
			//work out the image extension
			$ext = basename( $image_url ); //name only
			$ext = explode( '?', $ext ); //split at query string
			$ext = $ext[0]; //set to bit before query string
			$ext = explode( '.', $ext ); //split at dot
			$ext = $ext[count( $ext ) - 1]; //get item after last dot (extension)
			//local name
			$img_name = 'data/images/' . sha1( $image_url ) . '.' . $ext;

			//allowed ext?
			if( in_array( $ext, array( 'jpeg', 'jpg', 'png', 'gif' ) ) ):
				//do we already have this?
				if( !file_exists( $img_name ) ):
					//download/save our image locally
					$download = false;
					if( $img_data = @file_get_contents( $image_url ) )
						$download = @file_put_contents( $img_name, $img_data );
					//change src if downloaded & add to images
					if( $download ):
						return $img_name;
					endif;
				else:
					//file exists already, lets go
					return $img_name;
				endif;
			endif;

			//still here? aww
			return false;
		}
	}
?>