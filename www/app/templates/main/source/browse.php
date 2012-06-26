<?php
	//modules
	global $mod_user, $mod_token, $mod_cookie;

	//include item template
	$this->load( 'functions/source_template' );
?>

<script type="text/javascript">
	pulsefeed.sourceType = '<?php echo $this->get( 'browse_type' ); ?>';
	pulsefeed.sourceOffset = <?php echo $this->get( 'nextOffset' ); ?>;
</script>

<div id="header">
	<div class="wrap">
		<div class="left">
			<?php echo $this->get( 'browse_type' ) == 'website' ? '<a href="' . $c_config['root'] . '/websites/add" class="button green">+ Add Websites</a>' : ''; ?>
		</div>

		<h1>Browse <?php echo ucfirst( $this->get( 'browse_type' ) ); ?>s</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main wide">
		<span class="edit"><?php echo ( ( isset( $_GET['me'] ) and $this->get( 'browse_type' ) == 'collection' ) ? 'owned' : $this->get( 'order' ) ) . ' ' . $this->get( 'browse_type' ); ?>s</span>

		<div class="sources">
			<?php foreach( $this->get( 'sources' ) as $source )
					source_template( $source, $this->get( 'browse_type' ) ); ?>
		</div><!--end sources-->

		<a class="morelink source_load_more" href="?offset=<?php echo $this->get( 'nextOffset' ); ?>">load more <?php echo $this->get( 'browse_type' ); ?>s &darr;</a>
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left">
			<ul>
				<li class="title">Order</li>
				<li><?php echo ( isset( $_GET['new'] ) or isset( $_GET['me'] ) ) ? '<a href="' . $c_config['root'] . '/' . $this->get( 'browse_type' ) . 's">Popular</a>' : 'Popular &rarr;'; ?></li>
				<li><?php echo isset( $_GET['new'] ) ? 'Newest &rarr;' : '<a href="' . $c_config['root'] . '/' . $this->get( 'browse_type' ) . 's/new">Newest</a>'; ?></li>
				<li><?php echo isset( $_GET['me'] ) ? ( $this->get( 'browse_type' ) == 'collection' ? 'Owned' : 'Subscribed' ) . ' &rarr;' : '<a href="' . $c_config['root'] . '/' . $this->get( 'browse_type' ) . 's/me">' . ( $this->get( 'browse_type' ) == 'collection' ? 'Owned' : 'Subscribed' ) . '</a>'; ?></li>
			</ul>

			<ul>
				<li class="title">Browse</li>
				<?php foreach( array( 'topic', 'website', 'collection' ) as $type )
					echo '<li>' . ( $this->get( 'browse_type' ) == $type ? ucfirst( $type ) . 's &rarr;' : '<a href="' . $c_config['root'] . '/' . $type . 's' . ( isset( $_GET['me'] ) ? '/me' : '' ) . ( isset( $_GET['new'] ) ? '/new' : '' ) . '">' . ucfirst( $type ) . 's</a>' ) . '</li>'; ?>
			</ul>
		</div><!--end left-->

		<div class="fix">
			<a href="#" class="top_link">
				&uarr; top
			</a>
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->