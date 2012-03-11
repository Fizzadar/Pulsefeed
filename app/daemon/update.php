<?php
	/*
		file: app/daemon/update.php
		desc: update server (daemon)
	*/
	global $mod_db, $argv, $mod_app;
	
	//remove mod_db
	$mod_db->__destruct();
	unset( $mod_db );

	//inc relevant update file
	if( !isset( $argv[2] ) or !in_array( $argv[2], array( 'source', 'twitter', 'facebook' ) ) )
		die();

	//data
	$threads = 20;
	$threadtime = 600;
	$dbtime = 300;

	//load inc bit
	$mod_app->load( 'daemon/inc/' . $argv[2] );

	//load daemon (db func, thread func, threads, thread time, db time)
	$daemon = new mod_daemon( 'dbupdate', 'update', $threads, $threadtime, $dbtime, 'update_' . $argv[2] );

	//and go!
	$daemon->start();



	//function pushed to each thread, update our source in this case
	function update( $source ) {
		global $mod_config;

		//new db
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

		//memcache
		$mod_memcache = new mod_memcache( $mod_db );

		//load our items
		if( $source['type'] == 'source' ):
			$mod_source = new mod_source( $source['feed_url'], $source['type'] );
		else:
			$mod_source = new mod_source( $source['site_url'], $source['type'] );
		endif;
		$items = $mod_source->load();

		//clean the items for mysql
		$items = $mod_db->clean( $items );

		//loop each item, remove any older than update_time
		foreach( $items as $key => $item ):
			if( $item['time'] < $source['update_time'] ):
				unset( $items[$key] );
				echo 'old article, skipping ' . $item['end_url'] . PHP_EOL;
			endif;
		endforeach;

		//loop each item, check to see if we have the article, if we do, assign ID, if not, insert and assign
		foreach( $items as $key => $item ):
			if( isset( $item['id'] ) ):
				$items[$key]['id'] = $item['id'];
				$items[$key]['popscore'] = $item['popularity_score'];
				$items[$key]['time'] = $item['time'];
				echo 'article already has id: ' . $item['end_url'] . PHP_EOL;
				continue;
			endif;

			$check = $mod_db->query( '
				SELECT id, popularity_score, time
				FROM mod_article
				WHERE end_url = "' . $item['end_url'] . '"
				LIMIT 1
			' );
			if( $check and count( $check ) == 1 ):
				$items[$key]['id'] = $check[0]['id'];
				$items[$key]['popscore'] = $check[0]['popularity_score'];
				$items[$key]['time'] = $check[0]['time'];
				echo 'article already in db: ' . $item['end_url'] . PHP_EOL;
			else:
				$sourceid = 0;
				if( $source['type'] == 'source' )
					$sourceid = $source['id'];

				//insert the article
				$insert = $mod_db->query( '
					INSERT INTO mod_article
					( title, url, end_url, description, time, image_quarter, image_third, image_half, source_id )
					VALUES( "' . $item['title'] . '", "' . $item['url'] . '", "' . $item['end_url'] . '", "' . $item['summary'] . '", ' . $item['time'] . ', "' . $item['image_quarter'] . '", "' . $item['image_third'] . '", "' . $item['image_half'] . '", ' . $sourceid . ' )
				' );
				if( $insert ):
					$items[$key]['id'] = $mod_db->insert_id();
					$items[$key]['popscore'] = 0;
					echo 'inserted: ' . $item['end_url'] . PHP_EOL;
				else:
					echo 'insert failed on: ' . $item['end_url'] . ' : ' . mysql_error() . PHP_EOL;
					unset( $items[$key] );
				endif;
			endif;
		endforeach;

		//add each article to the source
		$source_article = array();
		foreach( $items as $item )
			$source_article[] = array( 
				'article_id' => $item['id'],
				'source_id' => $source['id'],
				'article_time' => $item['time']
			);

		//if we're twitter / facebook
		if( $source['type'] == 'twitter' or $source['type'] == 'facebook' ):
			//loop articles
			foreach( $items as $key => $item ):
				//work out source url
				$url = parse_url( $item['end_url'] );
				$url = $url['scheme'] . '://' . $url['host'];

				//look for feeds on the url
				$test = new mod_source( $url );
				$feed = @$test->find( $url );

				//found one?
				if( $feed and isset( $feed['feed_url'] ) and !empty( $feed['feed_url'] ) ):
					$exist = $mod_db->query( '
						SELECT id
						FROM mod_source
						WHERE feed_url = "' . $feed['feed_url'] . '"
						LIMIT 1
					' );
					if( !$exist or count( $exist ) == 0 ):
						//create the source
						$create = $mod_db->query( '
							INSERT INTO mod_source
							( site_title, site_url, feed_url, time )
							VALUES ( "' . $feed['site_title'] . '", "' . $feed['site_url'] . '", "' . $feed['feed_url'] . '", ' . time() . ' )
						' );
						//create worked?
						if( $create ):
							$id = $mod_db->insert_id();
							$source_article[] = array(
								'article_id' => $item['id'],
								'source_id' => $id,
								'article_time' => $item['time']
							);
							echo 'new source added: ' . $feed['site_title'] . PHP_EOL;
							$items[$key]['original_source'] = array(
								'title' => $feed['site_title'],
								'id' => $id,
								'domain' => $feed['site_url']
							);
						endif;
					else:
						$source_article[] = array(
							'article_id' => $item['id'],
							'source_id' => $exist[0]['id'],
							'article_time' => $item['time']
						);
						echo 'original source added to article: ' . $item['id'] . PHP_EOL;
						$items[$key]['original_source'] = array(
							'title' => $feed['site_title'],
							'id' => $exist[0]['id'],
							'domain' => $feed['site_url']
						);
					endif;
				else:
					echo 'could not find feed on: ' . $url . PHP_EOL;
				endif;
			endforeach;
		endif;

		//build the mod_source_articles query
		if( count( $source_article ) > 0 ):
			$sql = '
				INSERT IGNORE INTO mod_source_articles ( source_id, article_id, article_time )
				VALUES';

			foreach( $source_article as $bit )
				$sql .= ' ( ' . $bit['source_id'] . ', ' . $bit['article_id'] . ', ' . $bit['article_time'] . ' ),';

			$sql = rtrim( $sql, ',' );

			//run it
			$mod_db->query( $sql );
		endif;

		//now, mod_user_articles
		$user_article = array();
		switch( $source['type'] ):
			case 'source':
				//get domain
				$domain = parse_url( $source['site_url'] );
				$domain = $domain['host'];

				//get subscribed users
				$subscribed = $mod_db->query( '
					SELECT user_id
					FROM mod_user_sources
					WHERE source_id = ' . $source['id'] . '
				' );
				//for each user, add the articles
				foreach( $subscribed as $user ):
					foreach( $items as $item ):
						$user_article[] = array(
							'user_id' => $user['user_id'],
							'article_id' => $item['id'],
							'source_type' => 'source',
							'source_id' => $source['id'],
							'source_title' => $source['site_title'],
							'source_data' => json_encode( array( 'domain' => $domain ) ),
							'article_time' => $item['time'],
							'article_popscore' => $item['popscore'],
							'unread' => count( $mod_memcache->get( 'mod_user_reads', array(
								array(
									'user_id' => $user['user_id'],
									'article_id' => $item['id']
								)
							) ) ) == 1 ? 0 : 1,
							'article_popmultiply' => 1
						);
					endforeach;
				endforeach;
				break;

			case 'twitter':
			case 'facebook':
				foreach( $items as $item ):
					$unread = count( $mod_memcache->get( 'mod_user_reads', array(
						array(
							'user_id' => $source['owner_id'],
							'article_id' => $item['id']
						)
					) ) ) == 1 ? 0 : 1;

					$user_article[] = array(
						'user_id' => $source['owner_id'],
						'article_id' => $item['id'],
						'source_type' => $source['type'],
						'source_id' => $source['id'],
						'source_title' => $item['ex_username'],
						'source_data' => json_encode( array( 'user_id' => $item['ex_userid'] ) ),
						'article_time' => $item['time'],
						'article_popscore' => $item['popscore'],
						'unread' => $unread,
						'article_popmultiply' => 2
					);

					//original source?
					if( isset( $item['original_source'] ) and $item['original_source']['id'] > 0 ):
						$domain = parse_url( $item['original_source']['domain'] );
						$domain = $domain['host'];

						$user_article[] = array(
							'user_id' => $source['owner_id'],
							'article_id' => $item['id'],
							'source_type' => 'original',
							'source_id' => $item['original_source']['id'],
							'source_title' => $item['original_source']['title'],
							'source_data' => json_encode( array( 'domain' => $domain ) ),
							'article_time' => $item['time'],
							'article_popscore' => $item['popscore'],
							'unread' => $unread,
							'article_popmultiply' => 0
						);
					endif;
				endforeach;

				break;
		endswitch;

		//build the mod_source_articles query
		if( count( $user_article ) > 0 ):
			$sql = '
				REPLACE INTO mod_user_articles ( user_id, article_id, source_type, source_id, source_title, article_time, article_popscore, unread, source_data, article_popmultiply )
				VALUES';

			foreach( $user_article as $bit )
				$sql .= ' ( ' . $bit['user_id'] . ', ' . $bit['article_id'] . ', "' . $bit['source_type'] . '", "' . $bit['source_id'] . '", "' . $bit['source_title'] . '", ' . $bit['article_time'] . ', ' . $bit['article_popscore'] . ', ' . $bit['unread'] . ', \'' . $bit['source_data'] . '\', ' . $bit['article_popmultiply'] . ' ),';

			$sql = rtrim( $sql, ',' );

			//run it
			$mod_db->query( $sql );
		endif;

		//we're done! now unlock the source, update tme
		$mod_db->query( '
			UPDATE mod_source
			SET update_time = ' . time() . '
			WHERE id = ' . $source['id'] . '
			LIMIT 1
		' );

		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//exit
		exit( 0 );
	}
?>