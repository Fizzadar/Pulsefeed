	<div id="header">
		<div class="wrap">
			<div class="left">
				<a href="<?php echo $c_config['root']; ?>">&larr; back to stream</a>
			</div>

			<div class="right">
			</div>

			<h1><?php echo $this->content['article']['title']; ?></h1>
		</div><!--end wrap-->
	</div><!--end header-->

	<div class="wrap" id="content">
		<div class="main" id="stream">
			<div class="item article level_1">
				<span class="content">
					<?php echo $this->content['article']['content']; ?>
				</span>
				<?php if( $this->content['article']['not_full'] ): ?>
					<p><strong>Note:</strong> this article is not in full (<a class="edit" href="#">why?</a>), <a target="_blank" href="<?php echo $this->content['article']['url']; ?>"><strong>please read the full article here</strong> &rarr;</a></p>
				<?php else: ?>
					<p><small><strong>Note:</strong> this article <em>may</em> not be complete (<a class="edit" href="#">why?</a>), <a target="_blank" href="<?php echo $this->content['article']['url']; ?>">full article here &rarr;</a></small></p>
				<?php endif; ?>
			</div>

		</div><!--end main-->
	</div><!--end content-->

	<div id="sidebars">
		<div class="wrap">

			<div class="left">
				<ul>
					<li class="title">Article Info</li>
					<li>
						<span class="type shown">date</span><br />
						<?php echo date( 'jS F, Y', $this->content['article']['time'] ); ?>
					</li>
					<li>
						<span class="type shown">source</span><br />
						<img src="http://www.google.com/s2/favicons?domain=<?php echo $this->content['article']['site_domain']; ?>" />
						<a href="<?php echo $this->content['article']['site_url']; ?>"><?php echo $this->content['article']['site_title']; ?></a>
					</li>
				</ul>
			</div><!--end left-->

			<div class="right">
				<div class="biglinks">
					<a href="<?php echo $this->content['article']['url']; ?>" class="biglink">
						<span>View original article &rarr;</span>
						<?php echo $this->content['article']['trim_url']; ?>
					</a>

					<a href="#" class="biglink">
						<span>Recommend this article</span>
						<?php echo $this->content['article']['recommendations']; ?> already have
					</a>

					<a href="#" class="biglink">
						<span>Add to collection</span>
						Keep this article archived for later
					</a>
				</div><!--end biglinks-->
				
				<div class="bottom">
					<small>
						Powered by <a href="#">Fanatical Dev</a>
					</small>
				</div><!--end bottom-->
			</div><!--end right-->

		</div><!--end wrap-->
	</div><!--end sidebars-->