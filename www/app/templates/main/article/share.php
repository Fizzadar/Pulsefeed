<?php 
	/*
		file: app/templates/main/article/collect.php
		desc: minimal collection list for no-js
	*/
	
	global $mod_token, $mod_cookie;

?>

<div id="header">
	<div class="wrap">
		<div class="left noborder">
			<a class="button blue" href="<?php echo $mod_cookie->get( 'RecentStream' ) ? $mod_cookie->get( 'RecentStream' ) : $c_config['root']; ?>">&larr; Back to Stream</a>
		</div>

		<h1>Share Article: <?php echo $this->content['article']['title']; ?></h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main">
		<ul class="shares">
			<li><span><?php echo $this->content['article']['twitter_links']; ?></span><a class="button twitter" target="_blank" href="https://twitter.com/share?via=pulsefeed&url=<?php echo urlencode( $this->content['article']['end_url'] ); ?>&text=<?php echo urlencode( $this->content['article']['title'] ); ?>">Twitter</a></li>
			<li><span><?php echo $this->content['article']['facebook_shares']; ?></span><a class="button facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode( $this->content['article']['end_url']); ?>&t=<?php echo urlencode( $this->content['article']['title'] ); ?>">Facebook</a></li>
		</ul>

		<?php if( $this->get( 'shared' ) ): ?>
		<br /><br /><br />
		<span class="inline edit">article shared with followers:  <form class="inline" method="post" action="<?php echo $c_config['root']; ?>/process/article-unshare">
			<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			<input type="hidden" name="article_id" value="<?php echo $this->content['article']['id']; ?>" />
			<input class="edit" type="submit" value="unshare" />
		</form></span></p>
		<?php endif; ?>
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