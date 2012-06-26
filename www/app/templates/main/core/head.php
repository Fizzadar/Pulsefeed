<?php global $mod_user, $mod_token; ?>
<!DOCTYPE html>
<html>
<head>

	<!--
		  _____        _           __              _ 
		 |  __ \      | |         / _|            | |
		 | |__) |_   _| |___  ___| |_ ___  ___  __| |
		 |  ___/| | | | / __|/ _ \  _/ _ \/ _ \/ _` |
		 | |    | |_| | \__ \  __/ ||  __/  __/ (_| |
		 |_|     \__,_|_|___/\___|_| \___|\___|\__,_|
		 
		 version: <?php echo PULSEFEED_VERSION . PHP_EOL; ?>
		 hello there! welcome to the source ^^
		 <?php echo !isset( $_GET['unmin' ] ) ? 'minified for maximum warp.
		 ./?unmin to slow down <--' : 'maximized for minimum warp.
		 ./ to speed up -->'; ?>


	<!--title-->
	<title><?php echo $this->get( 'pageTitle' ) ? $this->get( 'pageTitle' ) . ' / Pulsefeed' : 'Pulsefeed'; ?></title>

	<!--meta-->
	<meta charset="UTF-8" />

	<!--facebook-->
	<meta property="og:url" content="<?php echo $this->get( 'canonical' ) ? $this->get( 'canonical' ) : $c_config['root'] . $_SERVER['REQUEST_URI']; ?>"/>
	<meta property="og:site_name" content="Pulsefeed"/>
<?php if( $this->get( 'externalHeader' ) ): ?>
	<meta property="og:title" content="<?php echo $this->get( 'pageTitle' ) ? $this->get( 'pageTitle' ) : 'Pulsefeed'; ?>"/>
	<meta property="og:type" content="article"/>
	<meta property="og:description" content="<?php echo $this->content['article']['description']; ?>" />
<?php else: ?>
	<meta property="og:title" content="<?php echo $this->get( 'pageTitle' ) ? $this->get( 'pageTitle' ) . ' / Pulsefeed' : 'Pulsefeed'; ?>"/>
	<meta property="og:type" content="website"/>
<?php endif; if( $this->get( 'externalHeader' ) or ( isset( $_GET['load'] ) and $_GET['load'] == 'source' ) ): ?>
	<meta name="robots" content="noindex" />
<?php endif; ?>

<?php if( $this->get( 'canonical' ) ): ?>
	<!--canonical-->
	<link rel="canonical" href="<?php echo $this->get( 'canonical' ); ?>" />
<?php endif; ?>

	<!--favicon-->
	<link rel="icon" href="<?php echo $c_config['root']; ?>/inc/img/favicon.png" />

	<!--style-->
<?php if( $mod_user->session_permission( 'Debug' ) or $_SERVER['HTTP_HOST'] == 'pulsefeed.dev' ): ?>
	<link rel="stylesheet" href="<?php echo $c_config['root']; ?>/inc/css/basics.css" media="all" />
	<link rel="stylesheet" href="<?php echo $c_config['root']; ?>/inc/css/core.css" media="all" />
	<link rel="stylesheet" href="<?php echo $c_config['root']; ?>/inc/css/main.css" media="all" />
<?php else: ?>
	<link rel="stylesheet" href="<?php echo $c_config['root']; ?>/inc/css/compiled.css" media="all" />
<?php endif; ?>
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic" media="all" />
<?php if( $mod_user->session_permission( 'Admin' ) ): ?>
	<link rel="stylesheet" href="<?php echo $c_config['root']; ?>/inc/css/admin.css" />
<?php endif; ?>

	<!--scripts-->
	<script type="text/javascript">
		//define pulsefeed
		var pulsefeed = {};

		//basics
		var mod_token = '<?php echo $mod_token; ?>';
		var mod_root = '<?php echo $c_config['root']; ?>';
		var mod_userid = <?php echo $mod_user->session_userid() ? $mod_user->session_userid() : 0; ?>;
	</script>
</head>