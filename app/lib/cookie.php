<?php
	/*
		file: app/lib/cookie.php
		desc: cookie management for pulsefeed
	*/
	
	class mod_cookie {
		private $cookie_id;

		public function __construct( $cookie_id ) {
			$this->cookie_id = $cookie_id;
		}

		public function set( $key, $value ) {
			$_COOKIE[$this->cookie_id . $key] = $value;
			return setcookie( $this->cookie_id . $key, $value, time() + ( 3600 * 24 * 365 ), '/' );
		}

		public function get( $key ) {
			return isset( $_COOKIE[$this->cookie_id . $key] ) ? $_COOKIE[$this->cookie_id . $key] : false;
		}

		public function delete( $key ) {
			unset( $_COOKIE[$this->cookie_id . $key] );
			setcookie( $this->cookie_id . $key, '', time() - 1 );
		}
	}
?>