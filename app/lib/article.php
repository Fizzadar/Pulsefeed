<?php
	/*
		file: app/lib/article.php
		desc: extension on simplepie's item class to get the data we want (and extract using htmldom if needed - cheeky)
	*/

	class mod_article extends SimplePie_Item {
		private $images = array();
		private $endlink = false;
		private $article = false;

		//return the time in unix epoch format
		public function get_time() {
			$date = $this->get_date( 'U' );
			return is_numeric( $date ) ? $date : time(); //time wrong? use now
		}

		//get/save & generate locally image (select best/main/biggest image)
		public function get_thumbs() {
			//config
			$config = array(
				'quarter' => array(
					'w' => 200,
					'h' => 100,
				),
				'half' => array(
					'w' => 430,
					'h' => 150,
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
					//check!
					if( !file_exists( $image ) )
						continue;

					//calculate size
					$s = @getimagesize( $image );
					//fail?
					if( !$s )
						continue;

					//work out size
					$size = $s[0] * $s[1];

					//image too small? fuck it
					if( $s[0] < ( $conf['w'] * 0.8 ) or $s[1] < ( $conf['h'] * 0.8 ) ):
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
					if( !file_exists( $thumb_name ) and file_exists( $this->images[$thumb_img] ) ):
						//generate the thumbnail
						$resize = new resize( $this->images[$thumb_img] );
						//attempt resize, save
						$resize->resizeImage( $conf['w'], $conf['h'], 'crop' );
						$resize->saveImage( $thumb_name, 80 );
					endif;

					//last check!
					if( !file_exists( $thumb_name ) )
						continue;

					//return the thumbnail
					$return[$conf_key] = $thumb_name;
				endif;
			endforeach;

			//now delete the images (since we only use thumbs)
			foreach( $this->images as $image )
				if( file_exists( $image ) )
					unlink( $image );

			//return the shit
			return $return;
		}

		//get end url
		public function get_end_url() {
			if( $this->endlink ) return $this->endlink;

			$return = $this->get_permalink();

			//find end-url by curling it
			$curl = curl_init( $this->get_permalink() );
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_exec( $curl );

			//worked?
			if( !curl_errno( $curl ) )
				$return = curl_getinfo( $curl, CURLINFO_EFFECTIVE_URL );
			curl_close( $curl );

			//return whatever we got
			$this->endlink = $return;
			return $return;
		}

		//gets the raw article from the site
		/*
			1. try pure feed article
				working out if it's full or not:
					a. check content == description
					b. check link to self (i.e more/continue links) <= uses end_url, ofc
					c. check length
			2. instapper's site patterns?
			3. hNews?
			4. readability
		*/
		private function get_raw_article() {
			//start with our normal feed content
			$return = $this->get_content();
			$url = $this->get_end_url();

			//are we happy?
			$happy = true;

			//feed content == summary? no good; no happy
			if( $return == $this->get_description() )
				$happy = false;

			//links to self? no good; no happy
			$html = new simple_html_dom();
			$html->load( $return );
			foreach( $html->find( 'a' ) as $link ):
				if( $link->href == $url ):
					$happy = false;
				endif;
			endforeach;
			
			//length under 30 words? no good; no happy
			$words = explode( ' ', $return );
			if( count( $words ) <= 30 ):
				$happy = false;
			endif;

			//are we unhappy? lets rip this thing! (only if we have an url)
			if( !$happy and !empty( $url ) ):
				//try instapper

				//try hnews

				//try readability; get file contents
				$contents = file_get_contents( $this->get_end_url() );
				$readability = new Readability( $contents, $url );
				//works?
				if( $readability->init() ):
					return $readability->getContent()->innerHTML;
				endif;
			endif;

			return $return;
		}

		//get article, after processing images/etc
		public function get_article() {
			global $c_config;

			//already got the article?
			if( !$this->article ):
				$this->article = $this->get_raw_article();
			endif;

			//now we have our article, lets process the images/html
			$html = new simple_html_dom();
			$html->load( $this->article );
			foreach( $html->find( 'img' ) as $img ):
				//remove any set w/h
				$img->width = 'auto';
				$img->height = 'auto';

				//fix img src
				if( substr( $img->src, 0, 1 ) == '/' ):
					$img->src = rtrim( $this->get_feed()->get_permalink(), '/' ) . $img->src;
				endif;

				//(try to) save the image locally
				if( $img_name = $this->save_image( $img->src ) and file_exists( $img_name ) ):
					$this->images[] = $img_name;
				endif;
			endforeach;

			//set article to our edited/fixed html
			$this->article = $html;

			//remove the html from the ram
			unset( $html );

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
			//work out/convert url
			$image_url = parse_url( $image_url );

			//work out the image extension
			$ext = basename( $image_url['path'] ); //name only
			$ext = explode( '.', $ext ); //split at dot
			$ext = $ext[count( $ext ) - 1]; //get item after last dot (extension)

			//no bits we need?
			if( !isset( $image_url['scheme'] ) or !isset( $image_url['host'] ) or !isset( $image_url['path'] ) )
				return false;
				
			//convert back to url
			$image_url = $image_url['scheme'] . '://' . $image_url['host'] . $image_url['path'];

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