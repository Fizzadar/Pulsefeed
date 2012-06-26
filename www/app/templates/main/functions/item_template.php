<?php
	//item template function
	function item_template( $that, $item, $uid, $header = 'h3', $no_image = false, $long = false ) {
		global $mod_user, $mod_token, $c_config, $mod_cookie;

		//no images cookie?
		if( $mod_cookie->get( 'hide_images' ) )
			$no_image = true;

		//been image?
		$been_image = false;

		//is tweet?
		$is_tweet = false;
		$is_post = false;
		foreach( $item['refs'] as $ref )
			if( $ref['source_type'] == 'twitter' and isset( $ref['source_data']['text'] ) )
				$is_tweet = $ref;
			elseif( $ref['source_type'] == 'facebook' and isset( $ref['source_data']['text'] ) )
				$is_post = $ref;

?><div class="item" id="article_<?php echo $item['id']; ?>">
	<<?php echo $header; ?>>
		<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" rel="nofollow" class="article_link"><?php echo $item['title']; ?></a>
	</<?php echo $header; ?>>

	<?php if( !empty( $item['image_wide_big'] ) and $mod_cookie->get( 'two_col' ) ): $been_image = true; ?>
		<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link" rel="nofollow">
			<img class="thumb wide_big" src="<?php echo $c_config['root'] . '/' . $item['image_wide_big']; ?>" alt="<?php echo $item['title']; ?>" <?php echo $no_image ? 'style="display:none;"' : ''; ?>/>
		</a>
	<?php elseif( !empty( $item['image_wide'] ) ): $been_image = true; ?>
		<a href="<?php echo $c_config['root'] . '/article/' . $item['id']; ?>" class="article_link" rel="nofollow">
			<img class="thumb wide" src="<?php echo $c_config['root'] . '/' . $item['image_wide']; ?>" alt="<?php echo $item['title']; ?>" <?php echo $no_image ? 'style="display:none;"' : ''; ?>/>
		</a>
	<?php endif; ?>

	<p<?php echo ( $been_image or $is_tweet or $is_post ) ? '' : ' class="wide"'; ?>>
		<?php echo html_entity_decode( $item['short_description'] ); ?>
		<span class="extended hidden"> <?php echo html_entity_decode( $item['extended_description'] ); ?></span>
	<?php switch( $item['type'] ):
			case 'video':
				break;
			default:
				echo '... <a href="' . $c_config['root'] . '/article/' . $item['id'] . '" class="article_link" rel="nofollow">read article&nbsp;&rarr;</a>';
	endswitch; ?></a></p>

	<?php if( !$been_image and $is_tweet ): ?>
	<div class="tweet">
		<img src="http://tweeter.fdev.in/<?php echo $is_tweet['source_id']; ?>" alt="" />
		<a href="<?php echo $c_config['root']; ?>/account/twitter/<?php echo $is_tweet['source_id']; ?>">@<?php echo $is_tweet['source_title']; ?></a>
		<p><?php echo $is_tweet['source_data']['text']; ?></p>
	</div>
	<?php elseif( !$been_image and $is_post ): ?>
	<div class="tweet">
		<img src="http://graph.facebook.com/<?php echo $is_post['source_id']; ?>/picture" alt="" />
		<a href="<?php echo $c_config['root']; ?>/account/facebook/<?php echo $is_post['source_id']; ?>"><?php echo $is_post['source_title']; ?></a>
		<p><?php echo $is_post['source_data']['text']; ?></p>
	</div>
	<?php endif; ?>

	<ul class="meta">
		<?php foreach( $item['refs'] as $ref ):
				$link = $c_config['root'] . '/';
				switch( $ref['source_type'] ):
					case 'website':
					case 'public':
						$link .= 'website' . '/' . $ref['source_id'];
						break;
					case 'like':
						$link .= 'user' . '/' . $ref['source_id'];
						break;
					case 'facebook':
					case 'twitter':
						$link .= 'account/' . $ref['source_type'] . '/' . $ref['source_id'];
						break;
					case 'topic':
						$link .= 'topic/' . $ref['source_id'];
						break;
					case 'share':
						$link .= 'user/'. $ref['source_id'];
						break;
					default:
						$link = '#';
				endswitch;
				
			?><li class="tip hover big"><span>
					<?php
						switch( $ref['source_type'] ):
							case 'twitter':
							case 'facebook':
								//avatar
								echo '<img src="' . ( $ref['source_type'] == 'twitter' ? 'http://tweeter.fdev.in/' . $ref['source_id'] : 'http://graph.facebook.com/' . $ref['source_id'] . '/picture' ) . '" class="avatar" />';

								//text
								echo '<em class="big">"' . ( isset( $ref['source_data']['text'] ) ? html_entity_decode( $ref['source_data']['text'] ) : 'No post / tweet located' ) . '</em>';
								
								//link to post
								if( isset( $ref['source_data']['postid'] ) ):
									echo '<a target="_blank" href="';
									echo $ref['source_type'] == 'twitter' ? 'http://twitter.com/' . $ref['source_title'] . '/status/' . $ref['source_data']['postid'] : 'http://facebook.com/' . $ref['source_id'] . '/posts/' . $ref['source_data']['postid'];
									echo '" class="button right ' . $ref['source_type'] . '">View ' . ( $ref['source_type'] == 'twitter' ? 'Tweet' : 'Post' ) . '</a>';
									echo '<a target="_blank" href="';
									echo $ref['source_type'] == 'twitter' ? 'http://twitter.com/' . $ref['source_title'] : 'http://facebook.com/' . $ref['source_id'];
									echo '" class="button right green">Profile</a>';
								endif;
								break;
							default:
					?>
						<ul>
							<li><small class="edit">author</small> <?php echo empty( $item['author'] ) ? 'Unknown' : $item['author']; ?></li>
							<li><small class="edit">date</small> <?php echo date( 'jS F', $item['time'] ); ?></li>
						</ul>
						<?php if( $ref['source_type'] == 'website' ): ?>
							<form action="<?php echo $c_config['root']; ?>/process/website-<?php echo $ref['subscribed'] ? 'un' : ''; ?>subscribe" method="post" id="subunsub" class="website_subscribe">
								<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
								<input type="hidden" name="website_id" value="<?php echo $ref['source_id']; ?>" />
								<input type="submit" value="<?php echo $ref['subscribed'] ? 'Unsubscribe' : '+ Subscribe'; ?>" class="button <?php echo $ref['subscribed'] ? 'red' : 'green'; ?>" />
							</form>
						<?php elseif( $ref['source_type'] == 'topic' ): ?>
							<form action="<?php echo $c_config['root']; ?>/process/topic-<?php echo $ref['subscribed'] ? 'un' : ''; ?>subscribe" method="post" id="subunsub" class="topic_subscribe">
								<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
								<input type="hidden" name="topic_id" value="<?php echo $ref['source_id']; ?>" />
								<input type="submit" value="<?php echo $ref['subscribed'] ? 'Unsubscribe' : '+ Subscribe'; ?>" class="button <?php echo $ref['subscribed'] ? 'red' : 'green'; ?>" />
							</form>
						<?php elseif( $ref['source_type'] == 'share' ): ?>
							<form action="<?php echo $c_config['root']; ?>/process/<?php echo $ref['subscribed'] ? 'un' : ''; ?>follow" method="post" id="subunsub" class="user_follow">
								<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
								<input type="hidden" name="user_id" value="<?php echo $ref['source_id']; ?>" />
								<input type="submit" value="<?php echo $ref['subscribed'] ? 'Unfollow' : '+ Follow'; ?>" class="button <?php echo $ref['subscribed'] ? 'red' : 'green'; ?>" />
							</form>
						<?php endif;

						endswitch;

						switch( $ref['source_type'] ):
							case 'twitter':
							case 'facebook':
							case 'website':
							case 'topic':
								echo '<img src="' . $c_config['root'] . '/inc/img/icons/share/' . $ref['source_type'] . '.png" />';
								break;
						endswitch;
					?>
					<strong><a href="<?php echo $link; ?>"><?php switch( $ref['source_type'] ):
						case 'twitter':
							echo '@';
							break;
						case 'topic':
							echo 'Topic: ';
							break;
						endswitch; echo $ref['source_title']; ?></a></strong>

					<small><?php
						switch( $ref['source_type'] ):
							case 'public':
								echo 'Public source';
								break;
							case 'website':
							case 'topic':
								if( $that->get( 'userid' ) == $mod_user->session_userid() ):
									echo $ref['subscribed'] ? 'You are subscribed' : 'Not Subscribed';
								else:
									echo $that->get( 'username' ) . ' is subscribed';
								endif;
								break;
							case 'share':
								if( $that->get( 'userid' ) == $mod_user->session_userid() ):
									echo $ref['subscribed'] ? 'You are subscribed' : 'Not Subscribed';
								else:
									echo $that->get( 'username' ) . ' is subscribed';
								endif;
								break;
							case 'facebook':
								echo 'You are subscribed';
								break;
							case 'twitter':
								echo ( $that->get( 'userid' ) == $mod_user->session_userid() ? 'You follow' : $that->get( 'username' ) . ' follows' ) . ' them';
								break;
							default:
								echo 'Unknown';
						endswitch;
					?></small><span></span></span>

				<a class="link" href="<?php echo $link; ?>">
				<?php
					switch( $ref['source_type'] ):
						case 'website':
						case 'public':
							echo '<img class="icon" src="http://favicon.fdev.in/' . $ref['source_data']['domain'] . '" alt="" /> ';
							break;
						case 'twitter':
							echo '<img class="icon" src="http://tweeter.fdev.in/' . $ref['source_id'] . '" alt="" /> @';
							break;
						case 'facebook':
							echo '<img class="icon" src="http://graph.facebook.com/' . $ref['source_id'] . '/picture" alt="" /> ';
							break;
						case 'topic':
							echo ' #';
							break;
					endswitch;

					echo $ref['source_title']; ?></a></li>
			<?php
		endforeach; ?>
	</ul><!--end meta-->

	<div class="meta">
	<?php if( $mod_user->session_login() ): ?>
		<!--hide-->
		<?php if( isset( $item['unread'] ) and $item['unread'] == 1 and $that->get( 'userid' ) == $mod_user->session_userid() ): ?>
		<form action="<?php echo $c_config['root']; ?>/process/article-hide" method="post" class="hide_form">
			<input type="hidden" name="article_id" value="<?php echo $item['id']; ?>" />
			<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			<input type="submit" value="Hide" class="meta" />
		</form> - 
		<?php endif; ?>

		<!--remove from collection-->
		<?php if( $that->get( 'title' ) == 'collection' and $that->get( 'userid' ) == $mod_user->session_userid() ): ?>
		<form action="<?php echo $c_config['root']; ?>/process/article-uncollect" method="post" class="uncollect_form">
			<input type="hidden" name="article_id" value="<?php echo $item['id']; ?>" />
			<input type="hidden" name="collection_id" value="<?php echo $that->get( 'collection_id' ); ?>" />
			<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			<input type="submit" value="Remove" class="meta" />
		</form> - 
		<?php endif; ?>
		
		<!--collect-->
		<span class="collect"><a class="collect_button tip mini always" href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>/collect" data-articleid="<?php echo $item['id']; ?>">Collect</a></span> - 


	<?php endif; ?>

		<!--share-->
		<form action="<?php echo $c_config['root']; ?>/process/article-share" method="post" class="share_form">
			<input type="hidden" name="article_id" value="<?php echo $item['id']; ?>" />
			<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			<input type="hidden" name="twitter_links" value="<?php echo number_format( $item['twitter_links'] ); ?>" />
			<input type="hidden" name="facebook_shares" value="<?php echo number_format( $item['facebook_shares'] ); ?>" />
			<input type="hidden" name="article_title" value="<?php echo urlencode( $item['title'] ); ?>" />
			<input type="hidden" name="article_url" value="<?php echo urlencode( $item['end_url'] ); ?>" />
			<input type="submit" value="Share" class="meta" />
			<span class="share"></span>
		</form>

		<span class="time"> - <?php echo $item['time_ago']; ?></span>
	</div><!--end meta-->
</div><!--end item--><?php } ?>