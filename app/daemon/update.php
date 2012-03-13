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
	$threadtime = 3600;
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

		//insert arrays
		$source_article = array();
		$user_article = array();

		//get our subscribers
		if( $source['type'] == 'source' ):
			$subscribers = $mod_db->query( '
				SELECT user_id
				FROM mod_user_sources
				WHERE source_id = ' . $source['id'] . '
			' );
		else:
			$subscribers = array(
				array(
					'user_id' => $source['owner_id']
				)
			);
		endif;

		//loop each item
		foreach( $items as $key => $item ):
			//remove any older than update time
			if( $item['time'] < $source['update_time'] ):
				unset( $items[$key] );
				echo 'old article, skipping ' . $item['end_url'] . PHP_EOL;
			endif;

			//work out source, locate/insert where needed
			if( !isset( $item['source_id'] ) ):
				if( $source['type'] == 'source' ):
					$items[$key]['source_id'] = $item['source_id'] = $source['id'];
				else:
					//not a source updating, so lets try find one, first work out root url
					$url = parse_url( $item['end_url'] );
					$url = $url['scheme'] . '://' . $url['host'];

					//look for feeds on the url
					$test = new mod_source( $url );
					$feed = @$test->find( $url );

					//found one?
					if( $feed and isset( $feed['feed_url'] ) and !empty( $feed['feed_url'] ) ):
						echo 'source located @: ' . $url . PHP_EOL;

						//do we have it already?
						$exist = $mod_db->query( '
							SELECT id
							FROM mod_source
							WHERE feed_url = "' . $feed['feed_url'] . '"
							LIMIT 1
						' );
						//no?
						if( !$exist or count( $exist ) == 0 ):
							//create the source
							$create = $mod_db->query( '
								INSERT INTO mod_source
								( site_title, site_url, feed_url, time )
								VALUES ( "' . $feed['site_title'] . '", "' . $feed['site_url'] . '", "' . $feed['feed_url'] . '", ' . time() . ' )
							' );
							//create worked?
							if( $create ):
								$items[$key]['source_id'] = $item['source_id'] = $mod_db->insert_id();
								echo 'source inserted: ' . $url . PHP_EOL;
							endif;
						//yes!
						else:
							$items[$key]['source_id'] = $item['source_id'] = $exist[0]['id'];
						endif;
					else:
						$items[$key]['source_id'] = $item['source_id'] = 0;
						echo 'could not find feed on: ' . $url . PHP_EOL;
					endif;
				endif;
			else:
				echo 'article already has source: ' . $item['source_id'] . PHP_EOL;
			endif;

			//work out id, insert where needed (now we have source_id)
			if( !isset( $item['id'] ) ):
				//insert the article
				$insert = $mod_db->query( '
					INSERT INTO mod_article
					( title, url, end_url, description, time, image_quarter, image_third, image_half, source_id )
					VALUES(
						"' . $item['title'] . '",
						"' . $item['url'] . '",
						"' . $item['end_url'] . '",
						"' . $item['summary'] . '",
						' . $item['time'] . ',
						"' . $item['image_quarter'] . '",
						"' . $item['image_third'] . '",
						"' . $item['image_half'] . '",
						' . $item['source_id'] . '
					)
				' );
				//all good?
				if( $insert ):
					$items[$key]['id'] = $item['id'] = $mod_db->insert_id();
					echo 'inserted: #' . $item['id'] . ' : ' . $item['end_url'] . PHP_EOL;
				else:
					unset( $items[$key] );
					echo 'insert failed on: ' . $item['end_url'] . ' : ' . mysql_error() . PHP_EOL;
					continue;
				endif;
			else:
				echo 'article already has id: ' . $item['id'] . PHP_EOL;
			endif;

			//finally, add to source_article
			if( $item['source_id'] > 0 ):
				$source_article[] = array(
					'source_id' => $item['source_id'],
					'article_id' => $item['id'],
					'article_time' => $item['time']
				);
			endif;

			//finally finally, add to user_articles, gets complicated
			foreach( $subscribers as $subscriber ):
				//standard data
				$tmp = array(
					'user_id' => $subscriber['user_id'],
					'article_id' => $item['id'],
					'source_type' => $source['type'],
					'article_time' => $item['time'],
					'unread' => count( $mod_memcache->get( 'mod_user_reads', array(
						array(
							'user_id' => $subscriber['user_id'],
							'article_id' => $item['id']
						)
					) ) ) == 1 ? 0 : 1,
					'origin_id' => 0,
					'origin_title' => '',
					'origin_data' => '{}'
				);

				//now, source_id, source_title, source_data, ( +where needed: origin_id, origin_title, origin_data )
				if( $source['type'] == 'source' ):
					$tmp['source_id'] = $item['source_id']; //same as $source['id']
					$tmp['source_title'] = $source['site_title'];

					$domain = parse_url( $source['site_url'] );
					$domain = $domain['host'];
					$tmp['source_data'] = json_encode( array( 'domain' => $domain ) );
				else:
					$tmp['source_id'] = $source['id']; //do NOT use articles source_id, thats for origin ;)
					$tmp['source_title'] = $item['ex_username'];
					$tmp['source_data'] = json_encode( array( 'user_id' => $item['ex_userid'] ) );

					//origin? source_id > 0 and not the fb/tw source
					if( $item['source_id'] > 0 and $item['source_id'] != $source['id'] ):
						//get the source
						$sdata = $mod_memcache->get( 'mod_source', array(
							array(
								'id' => $item['source_id']
							)
						) );
						if( count( $sdata ) == 1 ):
							$sdata = $sdata[0];
							$domain = parse_url( $sdata['site_url'] );
							$domain = $domain['host'];

							$tmp['origin_id'] = $item['source_id'];
							$tmp['origin_title'] = $sdata['site_title'];
							$tmp['origin_data'] = json_encode( array( 'domain' => $domain ) );
						endif;
					endif;
				endif;

				//add to user_article
				$user_article[] = $tmp;
			endforeach;

			echo 'item complete: ' . $key . ' / ' . count( $items ) . PHP_EOL;
		endforeach;
//article id 920 fucks up
print_r( $user_article );
var_dump( $user_article );
echo 'DOUBLE U TEE EFF' . PHP_EOL;
die();

		//build the mod_source_articles query (after we have article ids)
		if( count( $source_article ) > 0 ):
			$sql = '
				INSERT IGNORE INTO mod_source_articles ( source_id, article_id, article_time )
				VALUES';

			foreach( $source_article as $bit )
				$sql .= ' (
					' . $bit['source_id'] . ',
					' . $bit['article_id'] . ',
					' . $bit['article_time'] . '
				),';

			$sql = rtrim( $sql, ',' );

			//run it
			$insert = $mod_db->query( $sql );

			//fail message
			if( !$insert ):
				echo 'mod_source_article insert failed: ' . mysql_error() . PHP_EOL . $sql . PHP_EOL;
			else:
				echo 'mod_source_article insert complete' . PHP_EOL;
			endif;
		endif;

		//build the mod_source_articles query
		if( count( $user_article ) > 0 ):
			$sql = '
				REPLACE INTO mod_user_articles ( user_id, article_id, source_type, source_id, source_title, article_time, unread, source_data, origin_id, origin_title, origin_data )
				VALUES';

			foreach( $user_article as $bit )
				$sql .= ' (
					' . $bit['user_id'] . ',
					' . $bit['article_id'] . ',
					"' . $bit['source_type'] . '",
					"' . $bit['source_id'] . '",
					"' . $bit['source_title'] . '",
					' . $bit['article_time'] . ',
					' . $bit['unread'] . ',
					\'' . $bit['source_data'] . '\',
					' . $bit['origin_id'] . ',
					"' . $bit['origin_title'] . '",
					\'' . $bit['origin_data'] . '\'
				),';

			$sql = rtrim( $sql, ',' );

			//run it
			$insert = $mod_db->query( $sql );

			//fail message
			if( !$insert ):
				echo 'mod_user_article insert failed: ' . mysql_error() . PHP_EOL . $sql . PHP_EOL;
			else:
				echo 'mod_user_article insert complete' . PHP_EOL;
			endif;
		endif;

		//complete
		echo 'source update complete #' . $source['id'] . PHP_EOL;

		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//exit
		exit( 0 );
	}
?>