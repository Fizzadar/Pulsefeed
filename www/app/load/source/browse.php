<?php
	/*
		file: app/load/sources.php
		desc: browser sources/subscribe to them
	*/

	//modules
	global $mod_db, $mod_user, $mod_message, $mod_data, $mod_memcache;

	//offset
	$offset = 0;
	if( isset( $_GET['offset'] ) and is_numeric( $_GET['offset'] ) )
		$offset = $_GET['offset'];

	//order
	$order = 'mod_source.subscribers';
	if( isset( $_GET['new'] ) )
		$order = 'mod_source.time';

	//my sources/no login?
	if( isset( $_GET['me'] ) and !$mod_user->check_login() ):
		$mod_message->add( 'MustLogin' );
		die( header( 'Location: ' . $c_config['root'] . '/sources' ) );
	endif;

	//start template
	$mod_template = new mod_template();

	//get popular sources
	$sources = $mod_db->query( '
		SELECT mod_source.id, mod_source.site_title, mod_source.site_url, mod_source.update_time, mod_source.subscribers
		FROM mod_source
		WHERE mod_source.id > 0
		AND mod_source.type = "source"
		ORDER BY ' . $order . ' DESC
		LIMIT ' . ( $offset * 16 ) . ', 16
	' );

	//build list of sources
	$subscribed = array();
	if( $mod_user->check_login() ):
		$list = array();
		foreach( $sources as $source )
			$list[] = array(
				'user_id' => $mod_user->get_userid(),
				'source_id' => $source['id']
			);
		$subscribed = $mod_memcache->get( 'mod_user_sources', $list );
	endif;

	//loop sources, add stuff
	foreach( $sources as $key => $source ):
		$sources[$key]['site_url_trim'] = substr( $source['site_url'], 0, 20 ) . ( strlen( $source['site_url'] ) > 20 ? '...' : '' );
		$sources[$key]['site_domain'] = $mod_data->domain_url( $source['site_url'] );
		$sources[$key]['time_ago'] = $mod_data->time_ago( $source['update_time'] );

		//subscribed?
		foreach( $subscribed as $subscribe ):
			if( $subscribe['source_id'] == $source['id'] ):
				$sources[$key]['subscribed'] = 1;
				break;
			endif;
		endforeach;

		//get recent articles <= cached like fuck!
		$arts = $mod_db->query( '
			SELECT article_id
			FROM mod_source_articles
			WHERE source_id = ' . $source['id'] . '
			ORDER BY article_time DESC
			LIMIT 3
		', true, 43200 ); //12 hours
		if( $arts and count( $arts ) > 0 ):
			//build memcache list
			$list = array();
			foreach( $arts as $article )
				$list[] = array(
					'id' => $article['article_id']
				);

			//get articles from memcache
			$data = $mod_memcache->get( 'mod_article', $list );
			//reverse id them
			$tmp = array();
			foreach( $data as $d )
				$tmp[$d['id']] = $d;
			$data = $tmp;

			//build list
			$articles = array();
			foreach( $arts as $article ):
				if( !isset( $data[$article['article_id']] ) )
					continue;

				$articles[] = $data[$article['article_id']];
			endforeach;
		else:
			$articles = array();
		endif;

		//add
		$sources[$key]['articles'] = $articles;
	endforeach;
	$mod_template->add( 'sources', $sources );

	$mod_template->add( 'nextOffset', $offset + 1 );
	$mod_template->add( 'sourceOrder', isset( $_GET['me'] ) ? 'mod_source.articles' : $order );
	$mod_template->add( 'sourceType', isset( $_GET['me'] ) ? 'me' : ( isset( $_GET['new'] ) ? 'new' : 'popular' ) );

	//templates
	$mod_template->load( 'core/header' );
	$mod_template->load( 'source/browse' );
	$mod_template->load( 'core/footer' );
?>