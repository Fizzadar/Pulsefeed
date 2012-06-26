<?php
	/*
		file: app/lib/feed_article.php
		desc: general class which gets content from an url, and processes thumbnails
	*/
	
	class mod_feed_article {
		private $images = array();
		private $content = false;
		private $summary = false;
		private $url = false;
		private $ready = false;
		private $endlink = false;
		private $xframe = false;
		private $type = 'text';
		private $imgdir;
		private $riptitle = '';
		private $data;

		//construct
		public function __construct( $url, $html = false ) {
			global $c_config, $mod_data;

			//already got our html?
			if( $html and !empty( $html ) ):
				$this->content = $html;
				$this->ready = true;
			endif;

			//set img root
			$this->imgdir = $c_config['core_dir'] . '/../data/';

			//set data
			$this->data = $mod_data;

			//set url
			$this->url = $url;
		}

		//get type
		public function get_type() {
			return $this->type;
		}

		//get xframe
		public function get_xframe() {
			return $this->xframe;
		}

		//get riptitle
		public function get_riptitle() {
			return $this->riptitle;
		}
		
		//get end url
		public function get_end_url() {
			if( $this->endlink ) return $this->endlink;

			//find end-url by curling it
			$curl = curl_init( $this->url );
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_HEADER, true );
			curl_setopt( $curl, CURLOPT_NOBODY, true );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 30 );
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
			curl_setopt( $curl, CURLOPT_MAXREDIRS, 3 );
			$headers = curl_exec( $curl );

			//worked?
			if( !curl_errno( $curl ) ):
				$info = curl_getinfo( $curl );
			else:
				curl_close( $curl );
				return $this->url;
			endif;

			//close curl
			curl_close( $curl );

			//work out headers
			$headers = explode( "\n", $headers );
			foreach( $headers as $key => $header ):
				$bits = explode( ': ', $header );
				if( isset( $bits[0] ) and isset( $bits[1] ) )
					$headers[trim( $bits[0] )] = trim( $bits[1] );

				unset( $headers[$key] );
			endforeach;
			//frames disabled?
			if( isset( $headers['X-Frame-Options'] ) and ( !strcasecmp( $headers['X-Frame-Options'], 'deny' ) or !strcasecmp( $headers['X-Frame-Options'], 'sameorigin' ) ) )
				$this->xframe = true;

			//split up url
			$bits = parse_url( $info['url'] );
			if( !isset( $bits['path'] ) ) $bits['path'] = '';
			if( !isset( $bits['scheme'] ) ) $bits['scheme'] = 'http';

			//remove non-wanted query bits
			if( isset( $bits['query'] ) ):
				$qbits = explode( '&', $bits['query'] );
				foreach( $qbits as $bit ):
					$q = explode( '=', $bit );
					if( in_array( $q[0], array(
						'utm_source',
						'utm_medium',
						'utm_campaign'
					) ) ):
						$bits['query'] = str_replace( '&' . $bit, '', $bits['query'] );
						$bits['query'] = str_replace( $bit, '', $bits['query'] );
					endif;
				endforeach;

				if( strlen( $bits['query'] ) > 0 ):
					$bits['query'] = '?' . $bits['query'];
				endif;
			else:
				$bits['query'] = '';
			endif;

			//not got our bits?
			if( !isset( $bits['scheme'] ) or !isset( $bits['host'] ) or !isset( $bits['path'] ) or !isset( $bits['query'] ) )
				return $this->url;

			//rebuilt return
			$return = $bits['scheme'] . '://' . $bits['host'] . $bits['path'] . $bits['query'];

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
			$contents = $this->data->get_data( $url );
			$readability = new Readability( $contents, $url );
			//works?
			if( $readability->init() ):
				$return = $readability->getContent()->innerHTML;
				$this->riptitle = trim( $readability->getTitle()->textContent );
			endif;

			$this->content = $return;
			$this->ready = true;

			return $return;
		}

		//get article, after processing images/etc
		public function get_article() {
			global $c_config;

			//end url
			$url = $this->get_end_url();

			//url matching? site plugin?
			$bits = parse_url( $url );
			$plugin_name = str_replace( 'www.', '', $bits['host'] );
			if( isset( $bits['host'] ) and file_exists( $c_config['core_dir'] . '/../app/plugins/sites/' . $plugin_name . '.php' ) )
				if( list( $this->ready, $this->content, $this->summary, $this->riptitle, $this->endlink, $this->type ) = include( $c_config['core_dir'] . '/../app/plugins/sites/' . $plugin_name . '.php' ) )
					return $this->content;

			//not ready/content?
			if( !$this->ready or !$this->content )
				$this->get_raw_article();

			//now we have our article, lets process the images/html
			$html = new simple_html_dom();
			$html->load( $this->content );
			$imgs = $html->find( 'img' );
			echo 'found ' . count( $imgs ) . ' images' . PHP_EOL;
			foreach( $imgs as $img ):
				//remove any set w/h
				$img->width = 'auto';
				$img->height = 'auto';

				//fix img src
				$urlbits2 = parse_url( $img->src );
				if( substr( $img->src, 0, 1 ) == '/' ):
					$img->src = ltrim( $img->src, '/' );
				endif;
				if( !isset( $urlbits2['host'] ) or empty( $urlbits2['host'] ) ):
					$img->src = $bits['scheme'] . '://' . $bits['host'] . '/' . $img->src;
				endif;
				if( substr( $img->src, 0, 4 ) != 'http' ):
					$img->src = 'http://' . $img->src;
				endif;

				//(try to) save the image locally
				if( $img_name = $this->save_image( $img->src ) and file_exists( $img_name ) ):
					$this->images[] = $img_name;
				else:
					echo 'image save failed: ' . $img->src . PHP_EOL;
				endif;
			endforeach;

			//remove the html from the ram
			unset( $html );

			//return our article
			return trim( $this->content );
		}

		//get/make our summary
		public function get_summary() {
			//got summary?
			if( $this->summary ) return $this->summary;

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

			//html entities
			$summary = htmlentities( $summary, ENT_QUOTES );

			//save summary
			$this->summary = trim( $summary );

			//return summary
			return $this->summary;
		}

		//get/save & generate locally image (select best/main/biggest image)
		public function get_thumbs() {
			global $mod_config;

			$return = array();

			//loop size options
			foreach( $mod_config['image_sizes'] as $conf_key => $conf ):
				//loop the images for this item, pick the best (biggest?)
				$max_size = 0;
				$thumb_img = -1;
				foreach( $this->images as $key => $image ):
					//check!
					if( !file_exists( $image ) ):
						echo 'image not found: ' . $image . PHP_EOL;
						continue;
					endif;

					//calculate size
					$s = getimagesize( $image );
					//fail?
					if( !$s ):
						echo 'size failed on: ' . $image . PHP_EOL;
						continue;
					endif;

					//work out size
					$size = $s[0] * $s[1];

					//image too small? fuck it
					if( $s[0] < ( $conf['w'] * 0.7 ) or $s[1] < ( $conf['h'] * $conf['scale'] ) ):
						//echo 'image too small: ' . $image . PHP_EOL;
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
					$thumb_name = $this->imgdir . 'thumbs/' . $conf_key . '/' . basename( $this->images[$thumb_img] );

					//no thumb? generate it!
					if( !file_exists( $thumb_name ) and file_exists( $this->images[$thumb_img] ) ):
						//generate the thumbnail
						$resize = new resize( $this->images[$thumb_img] );
						//attempt resize, save
						@$resize->resizeImage( $conf['w'], $conf['h'], 'crop' );
						@$resize->saveImage( $thumb_name, 80 );
					endif;

					//last check!
					if( !file_exists( $thumb_name ) ):
						echo 'thumbnail creation failed: ' . $thumb_name . PHP_EOL;
						continue;
					endif;

					echo 'thumbnail created: ' . $thumb_name . PHP_EOL;

					//return the thumbnail
					$return[$conf_key] = 'data/thumbs/' . $conf_key . '/' . basename( $this->images[$thumb_img] );
				endif;
			endforeach;

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
			$img_name = $this->imgdir . 'images/' . sha1( $image_url ) . '.' . $ext;

			//allowed ext?
			if( in_array( $ext, array( 'jpeg', 'jpg', 'png', 'gif' ) ) ):
				//do we already have this?
				if( !file_exists( $img_name ) ):
					//download/save our image locally
					$download = false;
					if( $img_data = $this->data->get_data( $image_url ) ):
						$download = file_put_contents( $img_name, $img_data );
						$sizetest = getimagesize( $img_name );
					endif;
					//change src if downloaded & add to images
					if( $download and $sizetest ):
						return $img_name;
					elseif( $download ): //downloaded, but no imagesize
						unlink( $img_name );
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