<?php
	/*
		file: app/process/cron/cleanup.php
		desc: cleans up old unread markers ($mod_config['article_expire'] hours old) (every day)
	*/

	//load modules
	global $mod_db, $mod_config;

	//a week ago
	$expire_unread = time() - ( 3600 * 24 * 7 );
	//72 hours ago
	$expire_stream = time() - ( 3600 * 72 );

	//get old articles which arent yet expired
	$articles = $mod_db->query( '
		SELECT id
		FROM mod_article
		WHERE time < ' . $expire_unread . '
		AND expired_unread = 0
	' );

	//remove unreads
	foreach( $articles as $article ):
		//delete unreads
		$mod_db->query( '
			DELETE FROM mod_user_unread
			WHERE article_id = ' . $article['id'] . '
		' );
		echo 'Unreads deleted for article #' . $article['id'] . "\n";
	endforeach;

	//expire old articles
	$mod_db->query( '
		UPDATE mod_article
		SET expired_unread = 1
		WHERE expired_unread = 0
		AND time < ' . $expire_unread . '
	' );

	//expire 48 hour articles
	$mod_db->query( '
		UPDATE mod_article
		SET expired_stream = 1
		WHERE expired_stream = 0
		AND time < ' . $expire_stream . '
	' );

	//count how many invite codes we got
	$invitecount = $mod_db->query( '
		SELECT COUNT( invite_code ) AS count
		FROM mod_invites
		WHERE user_id = 0
	' );
	$invitecount = $invitecount[0]['count'];

	if( $invitecount < 100 ):
		//add new codes
		$sql = '
			INSERT INTO mod_invites
			( invite_code )
			VALUES
		';
		for( $i = $invitecount; $i < 100; $i++ ):
			$sql .= '( "' . substr( sha1( mt_rand() . time() ), 0, 6 ) . '" ),';
		endfor;
		$sql = rtrim( $sql, ',' );
		$mod_db->query( $sql );
	endif;
	echo 'Invite codes set to 100' . "\n";
?>