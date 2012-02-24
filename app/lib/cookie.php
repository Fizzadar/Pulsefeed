<?php
	/*
		file: app/lib/cookie.php
		desc: cookie management for pulsefeed
	*/
	
	class mod_cookie {
		private $cookie_id;
		private $cookie_domain;

		public function __construct( $cookie_id ) {
			global $c_config;
			$this->cookie_id = $cookie_id;
			$this->cookie_domain = '.' . $c_config['host'];
		}

		public function set( $key, $value ) {
			$_COOKIE[$this->cookie_id . $key] = $value;
			return setcookie( $this->cookie_id . $key, $value, time() + ( 3600 * 24 * 365 ), '/', $this->cookie_domain );
		}

		public function get( $key ) {
			return isset( $_COOKIE[$this->cookie_id . $key] ) ? $_COOKIE[$this->cookie_id . $key] : false;
		}

		public function delete( $key ) {
			unset( $_COOKIE[$this->cookie_id . $key] );
			setcookie( $this->cookie_id . $key, '', time() - 1, '/', $this->cookie_domain );
		}
	}
?>