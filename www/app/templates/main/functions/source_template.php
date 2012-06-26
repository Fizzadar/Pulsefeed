<?php
	//source template
	function source_template( $source, $type = 'topic' ) {
		global $c_config, $mod_token;

?><div class="source">
		<h2><a href="<?php echo $c_config['root'] . '/' . $type . '/' . $source['id']; ?>">
			<?php echo $type == 'website' ? '<img class="favicon" src="http://favicon.fdev.in/' . $source['site_domain'] . '" alt="" /> ' : ''; ?>
			<?php echo $source['title']; ?>
		</a> 
			<?php echo $type == 'website' ? '<a href="' . $source['site_url'] . '" class="edit" target="_blank">' . $source['site_url'] . '</a>' : ''; ?>
			<?php echo $type == 'collection' ? '<a href="' . $c_config['root'] . '/user/' . $source['user_id'] . '" class="edit">' . ( $source['owned'] ? 'your collection' : $source['username'] ) . '</span></a>' : ''; ?>
		</h2>
		<div class="images">
			<div class="img one">
			<?php if( isset( $source['articles'][0] ) ): ?>
				<a href="<?php echo $c_config['root'] . '/article/' . $source['articles'][0]['id']; ?>">
					<span class="title"><?php echo $source['articles'][0]['title']; ?></span><img src="<?php echo $c_config['root'] . '/' . $source['articles'][0]['image_thumb']; ?>" alt="" />
				</a>
			<?php endif; ?>
			</div>
			<div class="img two">
			<?php if( isset( $source['articles'][1] ) ): ?>
				<a href="<?php echo $c_config['root'] . '/article/' . $source['articles'][1]['id']; ?>">
					<span class="title"><?php echo $source['articles'][1]['title']; ?></span><img src="<?php echo $c_config['root'] . '/' . $source['articles'][1]['image_thumb']; ?>" alt="" />
				</a>
			<?php endif; ?>
			</div>
			<div class="img three">
			<?php if( isset( $source['articles'][2] ) ): ?>
				<a href="<?php echo $c_config['root'] . '/article/' . $source['articles'][2]['id']; ?>">
					<span class="title"><?php echo $source['articles'][2]['title']; ?></span><img src="<?php echo $c_config['root'] . '/' . $source['articles'][2]['image_thumb']; ?>" alt="" />
				</a>
			<?php endif; ?>
			</div>
		</div>
		<ul class="meta">
			<?php switch( $type ):
				case 'topic':
				case 'website': ?>
				<li><small class="edit">subscribers</small><?php echo $source['subscribers']; ?></li>
				<li><small class="edit">articles</small><?php echo $source['article_count']; ?></li>
			<?php break;
				case 'collection': ?>
				<li><small class="edit">views</small><?php echo $source['views']; ?></li>
				<li><small class="edit">articles</small><?php echo $source['article_count']; ?></li>
			<?php break;
				endswitch; ?>
		</ul>
		<?php switch( $type ):
			case 'topic':
			case 'website': ?>
			<form method="post" action="<?php echo $c_config['root']; ?>/process/<?php echo $type; ?>-<?php echo $source['subscribed'] ? 'unsubscribe' : 'subscribe'; ?>" class="<?php echo $type; ?>_subscribe">
				<input type="submit" class="button <?php echo $source['subscribed'] ? 'red' : 'green'; ?>" value="<?php echo $source['subscribed'] ? 'Unsubscribe' : '+ Subscribe'; ?>" />
				<input type="hidden" name="<?php echo $type; ?>_id" value="<?php echo $source['id']; ?>" />
				<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			</form>
		<?php break;
			case 'collection': ?>
			<form method="post" action="<?php echo $c_config['root']; ?>/process/collection-delete">
				<a href="<?php echo $c_config['root'] . '/collection/' . $source['id']; ?>" class="button blue">View</a>
				<?php echo $source['owned'] ? '<input type="submit" class="button red" value="Delete" />' : ''; ?>
				<input type="hidden" name="<?php echo $type; ?>_id" value="<?php echo $source['id']; ?>" />
				<input type="hidden" name="mod_token" value="<?php echo $mod_token; ?>" />
			</form>
		<?php break;
			endswitch; ?>

	</div><!--end source-->
<?php } ?>