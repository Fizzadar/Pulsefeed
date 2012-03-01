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
			global $mod_config, $mod_session;
			if( isset( $this->messagelist[$message] ) ):
				if( $mod_config['api'] )
					die( json_encode( array( 'mod_token' => $mod_session->generate(), 'result' => $this->messagelist[$message][1], 'message' => $this->messagelist[$message][0] ) ) );
				else
					$this->messages[] = $this->messagelist[$message];
			endif;
		}

		public function get() {
			//no messages?
			if( !isset( $_SESSION['mod_messages'] ) )
				$_SESSION['mod_messages'] = array();

			//store messages
			$return = $_SESSION['mod_messages'];

			//printed, now remove
			$_SESSION['mod_messages'] = array();

			//return
			return $return;
		}
	}
?>