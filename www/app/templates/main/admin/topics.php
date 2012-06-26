<div id="header">
	<div class="wrap">
		<div class="left">
			
		</div>

		<h1>Admin: Topics</h1>
	</div><!--end wrap-->
</div><!--end header-->

<div class="wrap" id="content">
	<div class="main wide">
		<p><strong>general</strong> => <strong>parent</strong> => <strong>specific</strong>
		<p>(1 or 0) means global auto_tag on/off</p>
		
		<form action="" id="admin_add_topic">
			<h2>Add Topic</h2>
			<label for="title">Title</label>
			<input type="text" id="title" name="title" />

			<label for="parent_id">Parent #ID</label>
			<input type="text" id="parent_id" name="parent_id" value="0" />

			<label for="type">Type</label>
			<select id="type" name="type">
				<option value="general">General</option>
				<option value="parent">Parent</option>
				<option value="specific">Specific</option>
			</select>

			<label class="checkbox" for="auto_tag">Auto Tag (ignored if type = general):</label>
			<input type="checkbox" name="auto_tag" id="auto_tag" />

			<input type="submit" value="Add &#187;" />
		</form>

		<ul class="topics_general">
			<h3>Parent Topics w/o General <small class="edit"><a href="#">+</a></small></h3>
			<?php foreach( $this->get( 'topics_parent_no_parent' ) as $topic ):
				echo '<li><a href="' . $c_config['root'] . '/topic/' . $topic['id'] . '">' . $topic['title'] . '</a></li>';
			endforeach; ?>

			<h3>Specific Topics w/o Parent <small class="edit"><a href="#">+</a></small></h3>
			<?php foreach( $this->get( 'topics_specific_no_parent' ) as $topic ):
				echo '<li><a href="' . $c_config['root'] . '/topic/' . $topic['id'] . '">' . $topic['title'] . '</a> <small class="edit">(' . $topic['auto_tag'] . ')</small></li>';
			endforeach; ?>
		</ul>

		<ul class="topics">
			<h3>Generals, Parents &amp; Specifics <small class="edit"><a href="#">+</a></small></h3>
			<?php foreach( $this->get( 'topics' ) as $topic ):
				echo '<li><a href="' . $c_config['root'] . '/topic/' . $topic['id'] . '"><strong>' . $topic['title'] . '</strong></a>  <small class="edit">(' . $topic['auto_tag'] . ')</small> <a href="#">+</a><ul>';
					foreach( $topic['children'] as $child ):
						echo '<li><a href="' . $c_config['root'] . '/topic/' . $child['id'] . '">' . $child['title'] . '</a> <small class="edit">(' . $child['auto_tag'] . ')</small>';
						if( $child['type'] == 'parent' ):
							echo ' <a href="#">+</a> <ul>';
							foreach( $child['children'] as $subchild )
								echo '<li><a href="' . $c_config['root'] . '/topic/' . $subchild['id'] . '">' . $subchild['title'] . '</a> <small class="edit">(' . $subchild['auto_tag'] . ')</small>';
							echo '</ul>';
						endif;
						echo '</li>';
					endforeach;
				echo '</ul></li>';
			endforeach; ?>
		</ul>
	</div><!--end main-->
</div><!--end wrap-->

<div id="sidebars">
	<div class="wrap">
		<div class="left">
			<ul>
				<li class="title">Admin</li>
				<li><a href="<?php echo $c_config['root']; ?>/admin">Home</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/permissions">Permissions</a></li>
				<li>Topics &rarr;</li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/memcache">Memcache</a></li>
				<li><a href="<?php echo $c_config['root']; ?>/admin/users">Users</a></li>
			</ul>
		</div><!--end left-->

		<div class="right">
		</div>
	</div><!--end wrap-->
</div><!--end sidebars-->