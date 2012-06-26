<?php
	/*
		file: app/load/source/browse.php
		desc: browse sources (collections, users, topics, websites)
	*/

	//modules
	global $mod_db, $mod_user, $mod_message, $mod_data, $mod_memcache, $mod_load;

	//start template
	$mod_template = new mod_template();

	//must be correct type
	if( !in_array( $_GET['load'], array( 'topic-browse', 'website-browse', 'collection-browse' ) ) ):
		$mod_message->add( 'NotFound' );
		die( header( 'Location: ' . $c_config['root'] ) );
	endif;

	//bits
	$columns = 'id';
	$order = 'subscribers';
	$table = 'topic';
	$offset = 0;
	$limit = 30;
	//switch type
	switch( $_GET['load'] ):
		case 'topic-browse':
			break;
		case 'website-browse':
			$table = 'website';
			break;
		case 'collection-browse':
			$table = 'collection';
			$order = 'views';
			break;
	endswitch;
	$type = $table;

	//loading my stuff?
	if( isset( $_GET['me'] ) and $table != 'collection' ):
		$columns = $table . '_id AS id';
		$table = 'user_' . $table . 's WHERE user_id = ' . $mod_user->get_userid();
		$order = 'time';
	elseif( isset( $_GET['me'] ) and $table == 'collection' ):
		$table = 'collection WHERE user_id = ' . $mod_user->get_userid();
		$order = 'time';
	endif;

	//offset
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) )
		$offset = $_GET['offset'];

	//new?
	if( isset( $_GET['new'] ) )
		$order = 'time';

	//now lets grab some data!
	$sources = $mod_db->query( '
		SELECT ' . $columns . '
		FROM mod_' . $table . '
		ORDER BY ' . $order . ' DESC
		LIMIT ' . ( $offset * $limit ) . ', ' . $limit
	);

	//build each source
	foreach( $sources as $key => $source )
		$sources[$key] = $mod_load->load_source( $source['id'], $type );

	//subscribe/own checking?
	switch( $type ):
		case 'topic':
		case 'website':
			foreach( $sources as $key => $source )
				$sources[$key]['subscribed'] = count( $mod_memcache->get( 'mod_user_' . $type . 's', array( array(
					'user_id' => $mod_user->session_userid(),
					$type . '_id' => $source['id']
				) ), true ) ) == 1;
			break;
		case 'collection':
			foreach( $sources as $key => $source )
				$sources[$key]['owned'] = $mod_user->session_userid() == $source['user_id'];
			break;
	endswitch;

	//add template bits
	$mod_template->add( 'browse_type', $type );
	$mod_template->add( 'order', $order == 'time' ? ( isset( $_GET['me'] ) ? 'subscribed' : 'newest' ) : 'popular' );
	$mod_template->add( 'sources', $sources );
	$mod_template->add( 'nextOffset', $offset + 1 );

	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'source/browse' );
	$mod_template->load( 'core/footer' );
?>