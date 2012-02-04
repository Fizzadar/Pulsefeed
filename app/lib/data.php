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
				$text = 'second';
			//less than an hour?
			elseif( $diff < 3600 ):
				$number = round( $diff / 60 );
				$text = 'minute';
			//less than a day?
			elseif( $diff < 3600 * 24 ):
				$number = round( $diff / 3600 );
				$text = 'hour';
			//less than a week?
			elseif( $diff < 3600 * 24 * 7 ):
				$number = round( $diff / ( 3600 * 24 ) );
				$text = 'day';
			else:
				$number = round( $diff / ( 3600 * 24 * 7 ) );
				$text = 'week';
			endif;

			return $number . ' ' . ( $number <= 1 ? $text : $text . 's' ) . ' ago';
		}

		//get domain from url (no www)
		public function domain_url( $url ) {
			$url = parse_url( $url );
			return str_replace( 'www.', '', $url['host'] );
		}
	}