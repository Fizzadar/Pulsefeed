<?php 
	/*
		file: app/templates/main/search.php
		desc: minimal search design for no-js
	*/
	
	global $mod_data, $mod_user, $mod_cookie, $mod_token;

?>

<div id="header">
	<div class="wrap">
		<div class="left noborder">
			
		</div>

		<h1>Search: <?php echo $_GET['q']; ?></h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main">
		<ul class="search_results">
			<?php echo count( $this->get( 'sources' ) ) > 0 ? '<li><h3>Sources</h3></li>' : ''; ?>
			<?php foreach( $this->get( 'sources' ) as $result ): ?>
				<li>
					<strong><a href="<?php echo $c_config['root']; ?>/<?php echo $result['type']; ?>/<?php echo $result['id']; ?>"><?php echo $result['title']; ?></a></strong><br />
					<span class="edit">source</span>
				</li>
			<?php endforeach; ?>

			<?php echo count( $this->get( 'users' ) ) > 0 ? '<li><h3>Users</h3></li>' : ''; ?>
			<?php foreach( $this->get( 'users' ) as $result ): ?>
				<li>
					<strong><a href="<?php echo $c_config['root']; ?>/<?php echo $result['type']; ?>/<?php echo $result['id']; ?>"><?php echo $result['title']; ?></a></strong><br />
					<span class="edit">user</span>
				</li>
			<?php endforeach; ?>

			<?php echo count( $this->get( 'articles' ) ) > 0 ? '<li><h3>Articles</h3></li>' : ''; ?>
			<?php foreach( $this->get( 'articles' ) as $result ): ?>
				<li>
					<strong><a href="<?php echo $c_config['root']; ?>/<?php echo $result['type']; ?>/<?php echo $result['id']; ?>"><?php echo $result['title']; ?></a></strong><br />
					<span class="edit">article</span>
				</li>
			<?php endforeach; ?>

			<?php if( count( $this->get( 'result_count' ) ) <= 0 ): ?>
				<li>Unfortunately nothing could be found, please try another term: </li>
				<form id="search" action="<?php echo $c_config['root']; ?>/search" method="GET">
					<input type="text" id="q" name="q" value="<?php echo ( isset( $_GET['q'] ) and !empty( $_GET['q'] ) ) ? $_GET['q'] : 'Search Pulsefeed...'; ?>" onclick="if( this.value == 'Search Pulsefeed...' ) { this.value = ''; }" onblur="if( this.value == '' ) { this.value = 'Search Pulsefeed...'; }" />
					<input type="submit" id="submit" value="Search &rarr;" />
				</form>
			<?php endif; ?>

			<br />
			<a class="greenbutton" href="<?php echo $c_config['root']; ?>/search?q=<?php echo $_GET['q']; ?>&offset=<?php echo $this->get( 'nextOffset' ); ?>">Next page &rarr;</a>
		</ul>
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left noborder">
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->