<?php
	global $mod_user;

	//how many home bgs?
	$home_bgs = 2;
	//choose one
	$home_bg = isset( $_GET['home_bg'] ) ? $_GET['home_bg'] : mt_rand( 1, $home_bgs );
?>

<a href="<?php echo $c_config['root'] . '/?home_bg=' . ( $home_bg - 1 ); ?>" class="home_arrow_left hidden">
	<img src="<?php echo $c_config['root']; ?>/inc/img/home/arrow_left.png" alt="" class="home_arrow left" />
</a>
<a href="<?php echo $c_config['root'] . '/?home_bg=' . ( $home_bg + 1 ); ?>" class="home_arrow_right">
	<img src="<?php echo $c_config['root']; ?>/inc/img/home/arrow_right.png" alt="" class="home_arrow right" />
</a>

<div class="home_bg">
	<ul class="home_bg">
		<li><img src="<?php echo $c_config['root']; ?>/inc/img/home/big_pic_<?php echo $home_bg; ?>.jpg" alt="" class="home_bg" /></li>
		<?php for( $i = 1; $i <= $home_bgs; $i++ ): if( $i == $home_bg ) continue; ?>
			<li><img src="<?php echo $c_config['root']; ?>/inc/img/home/big_pic_<?php echo $i; ?>.jpg" alt="" class="home_bg" /></li>
		<?php endfor; ?>
	</ul>
</div>

<div class="wrap" id="home">
	<div class="main">
		<div class="welcome notop noborder">
			
		<?php if( isset( $_GET['reset'] ) ): ?>
			<h1>Account Reset: Verify</h1>
			<p>
				To complete the reset of your account, please login again.
			</p>
			<a class="button big red" href="#">&larr; Cancel Reset</a>
		<?php elseif( isset( $_GET['delete'] ) ): ?>
			<h1>Account Delete: Verify</h1>
			<p>
				To completely delete your account, please login again. <strong>This cannot be undone</strong>. You can also <a href="#">reset your account</a>. Please consider <a href="#">leaving feedback</a> if you are not happy with Pulsefeed.
			</p>
			<a class="button big red" href="#">&larr; Cancel Delete</a>
		<?php else: ?>
			<h1>Pulsefeed is <?php echo $this->get( 'introText' ); ?></h1>
			<p>
				We use your favorite topics, social accounts &amp; websites to build you a personalized magazine which is full of fresh, interesting content. And it takes less than minute to setup...
			</p>
		<?php endif; ?>

			<a class="button twitter big" href="<?php echo $c_config['root']; ?>/process/tw-out">Sign in with Twitter</a>
			<a class="button facebook big" href="<?php echo $c_config['root']; ?>/process/fb-out">Sign in with Facebook</a>
			<a class="button green big" href="<?php echo $c_config['root']; ?>/login">Other Accounts</a>

			<!--<div class="bigtext"><p>Pulsefeed for: <a><strong>iPhone</strong></a> - <a><strong>iPad</strong></a> - <a><strong>Android</strong></a></p></div>-->
		</div><!--end welcome-->
	</div><!--end main-->
</div><!--end wrap-->

<style type="text/css">
	div#footer {
		display:none;
	}
</style>
<script type="text/javascript" src="<?php echo $c_config['root']; ?>/inc/js/home.js"></script>