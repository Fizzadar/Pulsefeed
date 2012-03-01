<?php
	/*
		file: app/lib/template.php
		desc: custom template class, on top of c_template, manages which template to use
	*/
	
	class mod_template extends c_template {
		public function __construct() {
			global $mod_config, $mod_token;
			//construct with our configed template
			parent::__construct( 'app/templates/'. $mod_config['template'] . '/' );
			//add token
			if( !$mod_config['api'] or $mod_config['iapi'] ):
				$this->add( 'mod_token', $mod_token );
			endif;
		}

		public function load( $template ) {
			global $mod_config;

			//api page? nothing
			if( $mod_config['api'] )
				return;

			//ajax page + core template?
			if( $mod_config['ajax'] and substr( $template, 0, 4 ) == 'core' )
				return;

			//load template
			parent::load( $template );
		}

		public function __destruct() {
			global $mod_config;
			
			//if api page, dump the content as json
			if( $mod_config['api'] ):
				$this->add( 'result', 'success' );
				$this->add( 'message', 'data loaded' );
				echo json_encode( $this->content );
			endif;
		}
	}
?>