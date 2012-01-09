<?php
	global $c_config, $mod_data, $mod_user;
	$items = $this->get( 'currentStreamItems' );
	$item = $items[0];
?>

<div class="item smallstatus">
	<img src="https://graph.facebook.com/1618950042/picture" alt="" />
	<a href="<?php echo $c_config['root']; ?>/user/<?php echo $item['user_id']; ?>"><strong><?php echo $item['user_name']; ?></strong></a> 
	recommended an article: 
	<a href="<?php echo $c_config['root']; ?>/article/<?php echo $item['id']; ?>"><strong><?php echo $item['title']; ?></strong></a>
	<span class="meta"><span>&rarr; <?php echo $mod_data->time_ago( $item['time'] ); ?></span></span>
</div>