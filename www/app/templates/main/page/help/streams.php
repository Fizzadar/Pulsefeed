<?php $this->add( 'helpPage', 'Streams' ); ?>

<?php $this->load( 'page/help/header' ); ?>

<div class="wrap" id="content">
	<div class="main">
		<p>Streams are split into two sections: users an general.</p>

		<div class="left half">
			<h2>User Streams</h2>
			<p>Every user has a set of streams, some of which are publically available:</p>
			<ul>
				<li><strong>Hybrid</strong>: contains your unread articles only, is only available to the user, ordered by popularity</li>
				<li><strong>Unread</strong>: just like the hybrid stream, except ordered by date (newest first)</li>
				<li><strong>Popular</strong>: contains all articles, is publically available and ordered by popularity</li>
				<li><strong>Newest</strong>: just like the popular stream, except ordered by date</li>
			</ul>
		</div><!--end left half-->

		<div class="right half">
			<h2>General Streams</h2>
			<p>All general streams are publically available:</p>
			<ul>
				<li><strong>Topic Streams</strong>: streams of articles by topics</li>
				<li><strong>Source Streams</strong>: these contain articles belonging to one website/source</li>
				<li><strong>The Public Stream</strong>: this contains the most popular articles across the entire site</li>
			</ul>
		</div><!--end right half-->
	</div><!--end main-->
</div><!--end wrap-->

<?php $this->load( 'page/help/sidebar' ); ?>