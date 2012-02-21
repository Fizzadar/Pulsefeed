<?php
	/*
		file: app/lib/template.php
		desc: custom template class, on top of c_template, manages which template to use
	*/
	
	class mod_template extends c_template {
		public function __construct() {
			global $mod_config;
			//construct with our configed template
			parent::__construct( 'app/templates/'. $mod_config['template'] . '/' );
		}

		public function load( $template ) {
			global $mod_config;
			//api page? nothing
			if( $mod_config['api'] )
				return;
			//load template
			parent::load( $template );
		}

		public function __destruct() {
			global $mod_config;
			//if api page, dump the content as json
			if( $mod_config['api'] )
				echo json_encode( $this->content );
		}
	}
?>