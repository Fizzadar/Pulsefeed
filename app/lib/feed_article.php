<?php
	/*
		file: app/lib/feed_article.php
		desc: general class which gets content from an url, and processes thumbnails
	*/
	
	class mod_feed_article {
		private $images = array();
		private $content = false;
		private $url = false;
		private $ready = false;
		private $endlink = false;
		public $riptitle = '';

		//construct
		public function __construct( $url, $html = false ) {
			//already got our html?
			if( $html and !empty( $html ) ):
				$this->content = $html;
				$this->ready = true;
			endif;

			//set url
			$this->url = $url;
		}

		//get end url
		public function get_end_url() {
			if( $this->endlink ) return $this->endlink;

			$return = $this->url;

			//find end-url by curling it
			$curl = curl_init( $this->url );
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
			1. instapper's site patterns?
			2. hNews?
			3. readability
		*/
		private function get_raw_article() {
			//do we have content? return it!
			if( $this->content and $this->ready )
				return $this->content;

			//get url
			$url = $this->get_end_url();
			$return = false;

			//try instapper

			//try hnews

			//try readability; get file contents
			$contents = file_get_contents( $url );
			$readability = new Readability( $contents, $url );
			//works?
			if( $readability->init() ):
				$return = $readability->getContent()->innerHTML;
				$this->riptitle = $readability->getTitle()->textContent;
			endif;

			$this->content = $return;
			$this->ready = true;

			return $return;
		}

		//get article, after processing images/etc
		public function get_article() {
			global $c_config;

			if( !$this->ready or !$this->content )
				$this->get_raw_article();

			//now we have our article, lets process the images/html
			$html = new simple_html_dom();
			$html->load( $this->content );
			foreach( $html->find( 'img' ) as $img ):
				//remove any set w/h
				$img->width = 'auto';
				$img->height = 'auto';

				//fix img src
				if( substr( $img->src, 0, 1 ) == '/' ):
					$urlbits = parse_url( $this->get_end_url() );
					$img->src = rtrim( $urlbits['host'], '/' ) . $img->src;
				endif;

				//(try to) save the image locally
				if( $img_name = $this->save_image( $img->src ) and file_exists( $img_name ) ):
					$this->images[] = $img_name;
				endif;
			endforeach;

			//remove the html from the ram
			unset( $html );

			//return our article
			return $this->content;
		}

		//get/make our summary
		public function get_summary() {
			//got content?
			if( !$this->ready or !$this->content )
				$this->get_raw_article();

			//get article, strip tags, split into words
			$article = strip_tags( $this->content );
			//$article = htmlentities( $article );
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

		//get/save & generate locally image (select best/main/biggest image)
		public function get_thumbs() {
			//config
			$config = array(
				'quarter' => array(
					'w' => 187,
					'h' => 51,
				),
				'third' => array(
					'w' => 281,
					'h' => 77,
				),
				'half' => array(
					'w' => 374,
					'h' => 102,
				),
			);

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
					$s = getimagesize( $image );
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
			$image_url = str_replace( ' ', '%20', $image_url );

			//local name
			$img_name = 'data/images/' . sha1( $image_url ) . '.' . $ext;

			//allowed ext?
			if( in_array( $ext, array( 'jpeg', 'jpg', 'png', 'gif' ) ) ):
				//do we already have this?
				if( !file_exists( $img_name ) ):
					//download/save our image locally
					$download = false;
					if( $img_data = file_get_contents( $image_url ) )
						$download = file_put_contents( $img_name, $img_data );
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