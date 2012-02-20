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
			return str_replace( 'www.', '', $url['host'] );
		}

		//shorten string for tooltip
		public function str_tooltip( $string ) {
			return substr( $string, 0, 18 ) . ( strlen( $string ) > 18 ? '...' : '' );
		}
	}