<?php
	class mod_message {
		private $messages = array();
		private $messagelist = array();

		public function __construct( $messagelist ) {
			$this->messagelist = $messagelist;
		}

		//add messages to session at end of page
		public function __destruct() {
			$_SESSION['mod_messages'] = $this->messages;
		}

		public function add( $message ) {
			if( isset( $this->messagelist[$message] ) )
				$this->messages[] = $this->messagelist[$message];
		}

		public function display() {
			//no messages?
			if( !isset( $_SESSION['mod_messages'] ) )
				$_SESSION['mod_messages'] = array();

			//print messages
			print_r( $_SESSION['mod_messages'] );

			//printed, now remove
			$_SESSION['mod_messages'] = array();
		}
	}
?>