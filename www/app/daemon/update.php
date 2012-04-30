<?php
	/*
		file: app/daemon/update.php
		desc: update server (daemon)
	*/
	global $mod_db, $argv, $mod_app, $threads, $threadtime, $dbtime;
	
	//remove mod_db
	$mod_db->__destruct();
	unset( $mod_db );

	//inc relevant update file
	if( !isset( $argv[2] ) or !in_array( $argv[2], array( 'source', 'twitter', 'facebook' ) ) )
		die();

	//data
	$threads = 10;
	$threadtime = 300;
	$dbtime = 60;

	//load inc bit
	$mod_app->load( 'daemon/inc/update/' . $argv[2] );

	//load daemon (db func, thread func, threads, thread time, db time)
	$daemon = new mod_daemon( 'dbupdate', 'update', $threads, $threadtime, $dbtime, 'update_' . $argv[2], 1000 );

	//and go!
	$daemon->start();



	//function pushed to each thread, update our source in this case
	function update( $source ) {
		global $mod_config;

		//get mcache
		$mod_mcache = get_memcache();

		//db
		$mod_db = get_db( $mod_mcache );

		//mod_memcache
		$mod_memcache = new mod_memcache( $mod_db );

		//load our items
		if( $source['type'] == 'source' ):
			$mod_source = new mod_source( $source['feed_url'], $source['type'] );
		else:
			$mod_source = new mod_source( $source['auth_data'], $source['type'] );
		endif;
		$items = $mod_source->load();

		//false items? baw
		if( !$items ):
			//only sources (feeds) fail
			if( $source['type'] == 'source' ):
				//update failcount
				$mod_db->query( '
					UPDATE mod_source
					SET fail_count = fail_count + 1
					WHERE id = ' . $source['id'] . '
					LIMIT 1
				' );
			endif;

			//and we're done
			echo 'source loading failed' . PHP_EOL;
			exit( 0 );
		endif;

		//clean the items for mysql
		$items = $mod_db->clean( $items );

		//insert arrays
		$source_article = array();
		$user_article = array();
		$topic_article = array();

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
					'user_id' => $source['user_id']
				)
			);
		endif;

		//loop each item
		foreach( $items as $key => $item ):
			//remove any older than update time
			if( $item['time'] < $source['update_time'] ):
				echo 'old article, skipping (' . $key . ' / ' . count( $items ) . ') ' . $item['end_url'] . PHP_EOL;
				continue; //we're done
			endif;

			//work out source, locate/insert where needed
			if( $source['type'] == 'source' ):
				//article may appear on multiple sources, in this case we're adding it to this source
				$items[$key]['source_id'] = $item['source_id'] = $source['id'];
				$items[$key]['source_title'] = $item['source_title'] = $source['site_title'];
				$items[$key]['source_url'] = $item['source_url'] = $source['site_url'];
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
						SELECT id, site_title, site_url
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
							$items[$key]['source_title'] = $item['source_title'] = $feed['site_title'];
							$items[$key]['source_url'] = $item['source_url'] = $feed['site_url'];
							echo 'source inserted: ' . $url . PHP_EOL;
						endif;
					//yes!
					else:
						$items[$key]['source_id'] = $item['source_id'] = $exist[0]['id'];
						$items[$key]['source_title'] = $item['source_title'] = $exist[0]['site_title'];
						$items[$key]['source_url'] = $item['source_url'] = $exist[0]['site_url'];
						echo 'source added: ' . $item['source_id'] . PHP_EOL;
					endif;
				else:
					$items[$key]['source_id'] = $item['source_id'] = 0;
					echo 'could not find feed on: ' . $url . PHP_EOL;
				endif;
			endif;

			//source url set?
			if( isset( $item['source_url'] ) and isset( $item['source_title'] ) and isset( $item['source_id'] ) ):
				$tmp = parse_url( $item['source_url'] );
				$item['source_domain'] = $tmp['host'];
			endif;

			//work out id, insert where needed (now we have source_id)
			if( !isset( $item['id'] ) ):
				//insert the article (update time = article_time + 1800, give articles an 30min to update (in sync with their original post time, NOT our time))
				$insert = $mod_db->query( '
					INSERT INTO mod_article
					( title, url, end_url, description, time, image_quarter, image_third, image_half, update_time, source_id, source_title, source_data, type, xframe )
					VALUES(
						"' . $item['title'] . '",
						"' . $item['url'] . '",
						"' . $item['end_url'] . '",
						"' . $item['summary'] . '",
						' . $item['time'] . ',
						"' . $item['image_quarter'] . '",
						"' . $item['image_third'] . '",
						"' . $item['image_half'] . '",
						' . ( $item['time'] + 1800 ) . ',
						' . ( isset( $item['source_domain'] ) ? $item['source_id'] : 0 ) . ',
						"' . ( isset( $item['source_domain'] ) ? $item['source_title'] : '' ) . '",
						\'' . ( isset( $item['source_domain'] ) ? json_encode( array( 'domain' => $item['source_domain'] ) ) : '{}' ) . '\',
						"' . $item['type'] . '",
						' . ( $item['xframe'] ? 1 : 0 ) . '
					)
				' );
				//all good?
				if( $insert ):
					$items[$key]['id'] = $item['id'] = $mod_db->insert_id();
					echo 'inserted: #' . $item['id'] . ' : ' . $item['end_url'] . PHP_EOL;

					//topic/tag time
					$topics = array();

					//get source topics (cache = 24hr)
					if( $item['source_id'] > 0 ):
						$source_topics = $mod_db->query( '
							SELECT topic_id
							FROM mod_topic_sources
							WHERE auto_tag = 1
							AND source_id = ' . $item['source_id'] . '
						', true, 86400 );

						//add to topics
						foreach( $source_topics as $topic ):
							$topics[] = $topic['topic_id'];
						endforeach;
					endif;

					//get auto topics (cache = 24hr)
					$auto_topics = $mod_db->query( '
						SELECT id, title
						FROM mod_topic
						WHERE auto_tag = 1
					', true, 86400 );

					//split title
					$words = explode( ' ', $item['title'] );

					//match auto topics, add to topics
					foreach( $auto_topics as $topic )
						if( !in_array( $topic['id'], $topics ) )
							foreach( $words as $word )
								if( strcasecmp( trim( $word ), trim( $topic['title'] ) ) == 0 )
									$topics[] = $topic['id'];

					//add to topic_articles (article_id, topic_id, time, source_id, source_title, source_data, article_time)
					foreach( $topics as $topic ):
						$tmp = array(
							'article_id' => $item['id'],
							'topic_id' => $topic,
							'time' => time(),
							'source_id' => $item['source_id'],
							'source_title' => '',
							'source_data' => '{}',
							'article_time' => $item['time']
						 );

						//do we have the source?
						if( $item['source_id'] > 0 ):
							$domain = parse_url( $item['source_url'] );
							$domain = $domain['host'];

							$tmp['source_title'] = $item['source_title'];
							$tmp['source_data'] = json_encode( array( 'domain' => $domain ) );
						endif;

						$topic_article[] = $tmp;
					endforeach;
					echo 'article autotagged #' . $item['id'] . PHP_EOL;
				else:
					echo 'insert failed on: ' . $item['end_url'] . ' : ' . mysql_error() . PHP_EOL;
					continue;
				endif;

				//for mod_source when checking for articles
				@$mod_mcache->set( md5( $item['end_url'] ), $item['id'] );
				@$mod_mcache->set( md5( $item['url'] ), $item['id'] );
				@$mod_mcache->set( md5( $item['title'] ), $item['id'] );
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
					'unread' => count( $mod_memcache->get( 'mod_user_hides', array(
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
					$tmp['source_id'] = $source['id'];
					$tmp['source_title'] = $source['site_title'];

					$domain = parse_url( $source['site_url'] );
					$domain = $domain['host'];
					$tmp['source_data'] = json_encode( array( 'domain' => $domain ) );
				else:
					$tmp['source_id'] = $item['ex_userid']; //source ignored
					$tmp['source_title'] = $item['ex_username'];
					$tmp['source_data'] = json_encode( array( 'user_id' => $item['ex_userid'] ) );

					//origin defined? (source 0 by default here)
					if( $item['source_id'] > 0 ):
						$tmp['origin_id'] = $item['source_id'];
						$tmp['origin_title'] = $item['source_title'];

						$domain = parse_url( $item['source_url'] );
						$domain = $domain['host'];
						$tmp['origin_data'] = json_encode( array( 'domain' => $domain ) );
					endif;
				endif;

				//add to user_article
				$user_article[] = $tmp;
			endforeach;

			echo 'item complete: ' . $key . ' / ' . count( $items ) . ' : ' . $item['end_url'] . PHP_EOL;
		endforeach;

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
				INSERT IGNORE INTO mod_user_articles ( user_id, article_id, source_type, source_id, source_title, article_time, unread, source_data, origin_id, origin_title, origin_data )
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

		//build the topic_articles query
		if( count( $topic_article ) > 0 ):
			$sql = '
				INSERT IGNORE INTO mod_topic_articles ( article_id, topic_id, time, source_id, source_title, source_data, article_time )
				VALUES';

			foreach( $topic_article as $bit )
				$sql .= ' (
					' . $bit['article_id'] . ',
					' . $bit['topic_id'] . ',
					' . $bit['time'] . ',
					' . $bit['source_id'] . ',
					"' . $bit['source_title'] . '",
					\'' . $bit['source_data'] . '\',
					' . $bit['article_time'] . '
				),';

			$sql = rtrim( $sql, ',' );

			//run
			$insert = $mod_db->query( $sql );

			//message
			if( !$insert ):
				echo 'mod_topic_article insert failed: ' . mysql_error() . PHP_EOL . $sql . PHP_EOL;
			else:
				echo 'mod_topic_article insert complete' . PHP_EOL;
			endif;
		endif;

		//complete
		echo 'source update complete #' . ( $source['id'] > 0 ? $source['id'] : $source['type'] ) . PHP_EOL;

		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//exit
		exit( 0 );
	}
?>