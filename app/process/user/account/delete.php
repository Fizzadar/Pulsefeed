<?php
	/*
		file: process/user/account/delete.php
		desc: delete an oauth or openid
	*/

	//modules
	global $mod_db, $mod_user, $mod_session, $mod_message;

	//redir
	$redir = $c_config['root'] . '/settings';

	//token?
	if( !isset( $_POST['mod_token'] ) or !$mod_session->validate( $_POST['mod_token'] ) ):
		$mod_message->add( 'InvalidToken' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//post data
	if( !isset( $_POST['type'] ) or !in_array( $_POST['type'], array( 'oauth', 'openid' ) ) ):
		$mod_message->add( 'InvalidPost' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//extended post data
	switch( $_POST['type'] ):
		case 'oauth':
			if( !isset( $_POST['o_id'] ) or !is_numeric( $_POST['o_id'] ) or !isset( $_POST['provider'] ) ):
				$mod_message->add( 'InvalidPost' );
				die( header( 'Location: ' . $redir ) );
			endif;
			break;
		case 'openid':
			if( !isset( $_POST['open_id'] ) ):
				$mod_message->add( 'InvalidPost' );
				die( header( 'Location: ' . $redir ) );
			endif;
			break;
	endswitch;

	//logged in?
	if( !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//now, get all auth accts
	$oauths = $mod_user->get_oauths();
	$openids = $mod_user->get_openids();

	//we have none/one left?
	if( ( count( $oauths ) + count( $openids ) ) <= 1 ):
		$mod_message->add( 'LastAccount' );
		die( header( 'Location: ' . $redir ) );
	endif;

	//all gut? lets delete
	switch( $_POST['type'] ):
		case 'oauth':
			$delete = $mod_user->delete_oauth( $_POST['provider'], $_POST['o_id'] );
			//also delete any source
			$mod_db->query( '
				DELETE FROM mod_source
				WHERE feed_url = "' . $_POST['provider'] . '/' . $mod_user->get_userid() . '/' . $_POST['o_id'] . '"
				LIMIT 1
			' );
			break;
		case 'openid':
			$delete = $mod_user->delete_openid( $_POST['open_id'] );
			break;
	endswitch;

	//redirect
	if( $delete ):
		$mod_message->add( 'AccountDeleted' );
		header( 'Location: ' . $redir );
	else:
		$mod_message->add( 'UnkownError' );
		header( 'Location: ' . $redir );
	endif;
?>