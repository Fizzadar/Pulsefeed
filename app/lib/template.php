<?php
	class mod_template extends c_template {
		public function __construct() {
			global $mod_config;
			//construct with our configed template
			parent::__construct( 'app/templates/'. $mod_config['template'] . '/' );
		}

		public function load( $template ) {
			global $mod_config;
			//ajax page? no cores
			if( $mod_config['ajax'] && substr( $template, 0, 5 ) == 'core/' )
				return;
			//load template
			parent::load( $template );
		}

		public function __destruct() {
			global $mod_config;
			//if api page, dump the content as json
			if( $mod_config['api'] )
				echo json_encode( parent::$content );
		}
	}
?>