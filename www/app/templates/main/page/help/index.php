<div id="header">
	<div class="wrap">
		<div class="left">
			
		</div>

		<h1>Help</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main">
		<div class="left half">
			<h2>Sections</h2>
			<p>Our help section is broken into a few selections (on the left), each explaining a separate 'bit' of the app. Below is a short description of each item, please see the relevant section for more in-depth detail:</p>
			<ul>
				<li><strong><a href="<?php echo $c_config['root']; ?>/help/streams">Streams</a></strong>: most of the pages are 'streams', these are simply lists of articles</li>
				<li><strong><a href="<?php echo $c_config['root']; ?>/help/accounts">Accounts</a></strong>: Twitter &amp; Facebook accounts can contribute articles to your streams</li>
				<li><strong><a href="<?php echo $c_config['root']; ?>/help/topics">Topics</a></strong>: all articles are filtered into topics. These can be subscribed to</li>
				<li><strong><a href="<?php echo $c_config['root']; ?>/help/collections">Collections</a></strong>: collections allow you to group articles together for saving</li>
				<li><strong><a href="<?php echo $c_config['root']; ?>/help/sources">Sources</a></strong>: sources are simply webistes &amp; RSS feeds, most articles come from sources</li>
			</ul>
		</div><!--end left half-->

		<div class="right half">
			<h2>Contact Us</h2>
			<p>Depending on your request, we have a few options here:</p>
			<ul>
				<li><a href="#"><strong>Make a suggestion</strong></a>: have your say in the development of Pulsefeed</li>
				<li><a href="#"><strong>Request direct help</strong></a>: if something's not working, contact us directly</li>
			</ul>
		</div><!--end right half-->
	</div><!--end main-->
</div><!--end wrap-->

<?php $this->load( 'page/help/sidebar' ); ?>