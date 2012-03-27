<?php
	/*
		file: app/process/user/load.php
		desc: load users data (avatars)
	*/

	//modules
	global $mod_db, $mod_cookie, $mod_user, $mod_message;

	//no login?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//get our oauths
	$oauths = $mod_user->get_oauths();

	//loop the oauths
	$oauth = false;
	foreach( $oauths as $auth ):
		//firstly, replace sources where needed
		if( $auth['provider'] == 'twitter' or $auth['provider'] == 'facebook' ):
			$urldata = json_encode( array( 'oid' => $auth['o_id'], 'token' => $auth['token'], 'secret' => $auth['secret'] ) );
		
			$mod_db->query( '
				INSERT INTO mod_source
				( site_title, feed_url, site_url, owner_id, private, subscribers, type, time )
				VALUES (
					"User #' . $mod_user->get_userid() . ' on ' . $auth['provider'] . '",
					"' . $auth['provider'] . '/' . $mod_user->get_userid() . '/' . $auth['o_id'] . '",
					\'' . $urldata. '\',
					' . $mod_user->get_userid() . ',
					1,
					1,
					"' . $auth['provider'] . '",
					' . time() . '
				) ON DUPLICATE KEY UPDATE
				site_url = \'' . $urldata . '\'
			' );

			//add to sources
			if( $id = $mod_db->insert_id() ):
				$mod_db->query( '
					INSERT IGNORE INTO mod_user_sources
					( user_id, source_id )
					VALUES( 
						' . $mod_user->get_userid() . ', 
						' . $id . ' 
					)
				' );
			endif;
		endif;

		//twitter > facebook
		if( !$oauth and $auth['provider'] == 'twitter' ):
			$oauth = $auth;
		elseif( !$oauth and $auth['provider'] == 'facebook' ):
			$oauth = $auth;
		endif;
	endforeach;

	//work out avatar
	if( $oauth ):
		if( $oauth['provider'] == 'twitter' ):
			$data = @file_get_contents( 'http://api.twitter.com/1/users/show.json?user_id=' . $oauth['o_id'] );
			$data = json_decode( $data );
			$avatar = $data->profile_image_url;
		elseif( $oauth['provider'] == 'facebook' ):
			$avatar = 'http://graph.facebook.com/' . $oauth['o_id'] . '/picture';
		endif;
	endif;

	//set avatar
	if( isset( $avatar ) ):
		$mod_user->set_data( array(
			'avatar_url' => $avatar
		));
	endif;
?>