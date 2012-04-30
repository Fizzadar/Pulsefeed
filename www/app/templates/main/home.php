<?php
	global $mod_user;
?>

<div class="wrap" id="home">
	<div class="main">
		<div class="welcome">
			<h1>Pulsefeed is <?php echo $this->get( 'introText' ); ?></h1>
			<p>We use your favorite topics, social accounts &amp; websites to build you a personalized magazine which is full of fresh, interesting content. And it takes less than minute to setup...</p>

			<a class="button twitter big" href="<?php echo $c_config['root']; ?>/process/tw-out">Sign in with Twitter</a>
			<a class="button facebook big" href="<?php echo $c_config['root']; ?>/process/fb-out">Sign in with Facebook</a>
			<a class="button green big" href="<?php echo $c_config['root']; ?>/login">Other Accounts</a>

			<!--<div class="bigtext">Pulsefeed for: <a><strong>iPhone</strong></a> - <a><strong>iPad</strong></a> - <a><strong>Android</strong></a></div>-->
		</div><!--end welcome-->

		<div class="home first">
			<h2>Features</h2>

			<div class="left third">
				<h4>Source</h4>
				<p>Follow topics, subscribe to websites/rss, link with Twitter &amp; Facebook. Pulsefeed combines all these sources into one stream.</p>

				<h4>Recommended</h4>
				<p>As you read, Pulsefeed learns the topics you like. We will recommend articles via the stream which we believe you will enjoy.</p>

				<h4>Share</h4>
				<p>Follow other users streams to include articles they recommend. Super easy sharing via Twitter, Facebook, Google+, etc.</p>

				<a class="button green big" href="<?php echo $c_config['root']; ?>/login">Get Started Now &rarr;</a> 
			</div>

			<img class="right twothird" src="<?php echo $c_config['root']; ?>/inc/img/home/screenshot.jpg" alt="" />
		</div><!--end home-->

		<div class="home">
			<h2>News Sources</h2>
			<div class="col">
				<h3>Topics</h3>
				<p>Subscribe to topics you're interested in, and we'll pick the best content by learning what you read &amp; like.</p>
				<a class="button blue">Browse Topics &rarr;</a>
			</div>
			<div class="col">
				<h3>Social Accounts</h3>
				<p>Link your Twitter &amp; Facebook accounts to include content your friends / contacts post about or share.</p>
				<a class="button blue" href="<?php echo $c_config['root']; ?>/login">Add Social Account &rarr;</a>
			</div>
			<div class="col">
				<h3>Websites &amp; RSS</h3>
				<p>Add any specific RSS feeds from websites you know and love to be automatically included in your stream.</p>
				<a class="button blue" href="<?php echo $c_config['root']; ?>/sources">See Popular Sources &rarr;</a>
			</div>
		</div><!--end home-->

		<div class="home">
			<h2>Latest on Pulsefed</h2>
			<div class="col">
				<h3>Trending Topics</h3>
				<ul class="topics">
					<li>
						<a class="title" href="#">#Technology</a>
						<span>Sources:</span>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/techcrunch.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/google.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/techdirt.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/extrememech.com" /></a>
					</li>

					<li>
						<a class="title" href="#">#Facebook</a>
						<span>Sources:</span>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/facebook.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/google.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/techdirt.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/extrememech.com" /></a>
					</li>

					<li>
						<a class="title" href="#">#Fashion</a>
						<span>Sources:</span>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/facebook.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/google.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/techdirt.com" /></a>
						<a class="tip"><span><strong>TechCrunch</strong><small>Public Source</small><span></span></span><img src="http://favicon.fdev.in/extrememech.com" /></a>
					</li>
				</ul>
			</div>
			<div class="col">
				<h3>Popular Content</h3>
				<ul class="articles">
					<li>
						<a class="title" href="#">This is an awesome blog post title</a>
						<span>Posted on <a href="#"><strong>TechCrunch</strong></a> - 2h ago</span>
					</li>
					<li>
						<a class="title" href="#">Biz Dev is a clever name for dirty work</a>
						<span>Posted on <a href="#"><strong>Forbes.com: News</strong></a> - 2h ago</span>
					</li>
					<li>
						<a class="title" href="#">Thefuture.fm Has Solved a Problem That Turntable, YouTube and Spotify Couldn’t</a>
						<span>Posted on <a href="#"><strong>Forbes.com: News</strong></a> - 2h ago</span>
					</li>
				</ul>
			</div>
			<div class="col">
				<h3>Top Sources</h3>
			</div>
		</div><!--end home-->
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars" class="hightop notop">
	<div class="wrap">

		<div class="left">
			<ul>
				<li class="title">Streams</li>
				<li><a href="<?php echo $c_config['root']; ?>/public">All/Public</a></li>
			</ul>

			<ul>
				<li class="title">Pulsefeed Icon</li>
				<li><img src="<?php echo $c_config['root']; ?>/inc/img/favicon.png" class="favicon home" /></li>
			</ul>
		</div><!--end left-->

		<div class="right">
			<div class="biglinks home">
				<a href="<?php echo $c_config['root']; ?>/login" class="biglink">
					<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/login.png" alt="" /> Login to Pulsefeed</span>
					no need to regsiter, just login!
				</a>
				<!--and the rest-->
				<a href="http://blog.pulsefeed.com/" class="biglink">
					<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/blog.png" alt="" /> View our Blog</span>
					get the latest updates on pulsefeed
				</a>

				<a href="http://twitter.com/pulsefeed" class="biglink">
					<span><img src="<?php echo $c_config['root']; ?>/inc/img/icons/sidebar/twitter.png" alt="" /> Follow @pulsefeed</span>
					follow us on twitter
				</a>
			</div>
		</div>

		<div class="fix">
			<a href="#" class="top_link">
				&uarr; top
			</a>
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->