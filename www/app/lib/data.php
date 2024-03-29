<?php
	/*
		file: app/lib/data.php
		desc: helper functions/class
	*/
	
	class mod_data {
		//time ago string generation
		public function time_ago( $then ) {
			$now = time();
			//time difference
			$diff = $now - $then;

			$number = 0;
			$text = 'nanosecond';

			//in the future?!
			if( $diff < 0 ):
				return 'in the future';
			//less than a minute?
			elseif( $diff < 60 ):
				$number = $diff;
				$text = 's';
			//less than an hour?
			elseif( $diff < 3600 ):
				$number = round( $diff / 60 );
				$text = 'm';
			//less than a day?
			elseif( $diff < 3600 * 24 ):
				$number = round( $diff / 3600 );
				$text = 'h';
			//less than a week?
			elseif( $diff < 3600 * 24 * 7 ):
				$number = round( $diff / ( 3600 * 24 ) );
				$text = 'd';
			//less than a year?
			elseif( $diff < 3600 * 24 * 365 ):
				$number = round( $diff / ( 3600 * 24 * 7 ) );
				$text = 'w';
			else:
				$number = round( $diff / ( 3600 * 24 * 365 ) );
				$text = 'y';
			endif;

			return $number . $text . ' ago';
		}

		//get domain from url (no www)
		public function domain_url( $url ) {
			$url = parse_url( $url );

			if( !isset( $url['host'] ) )
				return '';

			return str_replace( 'www.', '', $url['host'] );
		}

		//shorten string for tooltip
		public function str_tooltip( $string ) {
			return substr( $string, 0, 18 ) . ( strlen( $string ) > 18 ? '...' : '' );
		}

		//get remote data
		function get_data( $url, $post_data = '', $content_type = '' ) {
			$curl = curl_init();

			//post?
			if( !empty( $post_data ) ):
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
			endif;

			//special content type?
			if( !empty( $content_type ) ):
				curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: ' . $content_type ) );
			endif;

			//options
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 30 );
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
			curl_setopt( $curl, CURLOPT_MAXREDIRS, 3 );

			//what do we get
			$data = curl_exec( $curl );

			//return
			if( $data and !empty( $data ) ):
				return $data;
			else:
				return false;
			endif;
		}
	}