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
	if( !isset( $argv[2] ) or !in_array( $argv[2], array( 'website', 'twitter', 'facebook' ) ) )
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
		if( $source['type'] == 'website' ):
			$mod_source = new mod_source( $source['feed_url'], $source['type'] );
		else:
			$mod_source = new mod_source( $source['auth_data'], $source['type'], $source['since_id'] );
		endif;
		$items = $mod_source->load();

		//if we have auth issues, label in db
		if( ( $items == 'deauthed' or $items == 'authexpire' ) and $source['type'] != 'website' ):
			//update account
			$mod_db->query( '
				UPDATE mod_account
				SET ' . ( $items == 'deauthed' ? 'deauthed' : 'expired' ) . ' = 1
				WHERE user_id = ' . $source['user_id'] . '
				AND type = "' . $source['type'] . '"
				AND o_id = ' . $source['o_id'] . '
				LIMIT 1
			' );

			//and we're done
			echo 'source loading failed' . PHP_EOL;
			exit( 0 );
		endif;

		//false items? baw
		if( !is_array( $items ) ):
			//only sources (feeds) fail
			if( $source['type'] == 'website' ):
				//update failcount
				$mod_db->query( '
					UPDATE mod_website
					SET fail_count = fail_count + 1
					WHERE id = ' . $source['id'] . '
					LIMIT 1
				' );
			endif;

			//and we're done
			echo 'source loading failed' . PHP_EOL;
			exit( 0 );
		endif;

		//no items?
		if( count( $items ) == 0 ):
			echo 'no items located' . PHP_EOL;
			exit( 0 );
		endif;

		//twitter? update last_twitter_id
		if( $source['type'] == 'twitter' ):
			$max_id = 0;
			foreach( $items as $item )
				if( $item['ex_postid'] >= $max_id )
					$max_id = $item['ex_postid'];

			//update
			$mod_db->query( '
				UPDATE mod_account
				SET latest_tweet_id = ' . $max_id . '
				WHERE user_id = ' . $source['user_id'] . '
				AND type = "' . $source['type'] . '"
				AND o_id = ' . $source['o_id'] . '
				LIMIT 1
			' );
		endif;

		//facebook? update latest_post_time
		if( $source['type'] == 'facebook' ):
			$max_time = 0;
			foreach( $items as $item )
				if( $item['time'] >= $max_time )
					$max_time = $item['time'];

			//update
			$mod_db->query( '
				UPDATE mod_account
				SET latest_post_time = ' . $max_time . '
				WHERE user_id = ' . $source['user_id'] . '
				AND type = "' . $source['type'] . '"
				AND o_id = ' . $source['o_id'] . '
				LIMIT 1
			' );
		endif;

		//clean the items for mysql
		$items = $mod_db->clean( $items );

		//insert arrays
		$website_article = array();
		$user_article = array();
		$topic_article = array();

		//get our subscribers
		if( $source['type'] == 'website' ):
			$subscribers = $mod_db->query( '
				SELECT user_id
				FROM mod_user_websites
				WHERE website_id = ' . $source['id'] . '
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
			if( $source['type'] == 'source' and $item['time'] < $source['update_time'] ):
				echo 'old article, skipping (' . $key . ' / ' . count( $items ) . ') ' . $item['end_url'] . PHP_EOL;
				continue; //we're done
			endif;

			//work out source, locate/insert where needed
			if( $source['type'] == 'website' ):
				//article may appear on multiple sources, in this case we're adding it to this source
				$items[$key]['source_id'] = $item['source_id'] = $source['id'];
				$items[$key]['source_title'] = $item['source_title'] = $source['site_title'];
				$items[$key]['source_url'] = $item['source_url'] = $source['site_url'];
				$items[$key]['source_subscribers'] = $item['source_subscribers'] = $source['subscribers'];
			else:
				//not a source updating, so lets try find one, first work out root url
				$url = parse_url( $item['end_url'] );
				$url = $url['scheme'] . '://' . $url['host'];

				//look for feeds on the url
				$test = new mod_source( $url );
				$feed = @$test->find( $url );

				//found one?
				if( $feed and isset( $feed['feed_url'] ) and !empty( $feed['feed_url'] ) and isset( $feed['site_url'] ) and !empty( $feed['site_url'] ) ):
					echo 'source located @: ' . $url . PHP_EOL;

					//do we have it already?
					$exist = $mod_db->query( '
						SELECT id, site_title, site_url, subscribers
						FROM mod_website
						WHERE feed_url = "' . $feed['feed_url'] . '"
						LIMIT 1
					' );
					//no?
					if( !$exist or count( $exist ) == 0 ):
						$feed = $mod_db->clean( $feed );
					
						//create the source
						$create = $mod_db->query( '
							INSERT INTO mod_website
							( site_title, site_url, feed_url, time )
							VALUES ( "' . $feed['site_title'] . '", "' . $feed['site_url'] . '", "' . $feed['feed_url'] . '", ' . time() . ' )
						' );
						//create worked?
						if( $create ):
							$items[$key]['source_id'] = $item['source_id'] = $mod_db->insert_id();
							$items[$key]['source_title'] = $item['source_title'] = $feed['site_title'];
							$items[$key]['source_url'] = $item['source_url'] = $feed['site_url'];
							$items[$key]['source_subscribers'] = $item['source_subscribers'] = 0;
							echo 'source inserted: ' . $url . PHP_EOL;
						endif;
					//yes!
					else:
						$items[$key]['source_id'] = $item['source_id'] = $exist[0]['id'];
						$items[$key]['source_title'] = $item['source_title'] = $exist[0]['site_title'];
						$items[$key]['source_url'] = $item['source_url'] = $exist[0]['site_url'];
						$items[$key]['source_subscribers'] = $item['source_subscribers'] = $exist[0]['subscribers'];
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
				$item['source_domain'] = isset( $tmp['host'] ) ? $tmp['host'] : '';
			endif;

			//work out id, insert where needed (now we have source_id)
			if( !isset( $item['id'] ) ):
				//insert the article
				$insert = $mod_db->query( '
					INSERT INTO mod_article
					( title, url, end_url, description, author, time, image_thumb, image_tall, image_wide, image_wide_big, source_id, source_title, source_data, type, xframe )
					VALUES(
						"' . $item['title'] . '",
						"' . $item['url'] . '",
						"' . $item['end_url'] . '",
						"' . $item['summary'] . '",
						"' . $item['author'] . '",
						' . $item['time'] . ',
						"' . $item['image_thumb'] . '",
						"' . $item['image_tall'] . '",
						"' . $item['image_wide'] . '",
						"' . $item['image_wide_big'] . '",
						' . ( isset( $item['source_domain'] ) ? $item['source_id'] : 0 ) . ',
						"' . ( isset( $item['source_domain'] ) ? $item['source_title'] : '' ) . '",
						\'' . ( isset( $item['source_domain'] ) ? json_encode( array( 'domain' => $item['source_domain'], 'subscribers' => $item['source_subscribers'] ) ) : '{}' ) . '\',
						"' . $item['type'] . '",
						' . ( $item['xframe'] ? 1 : 0 ) . '
					)
				' );
				//all good?
				if( $insert ):
					$items[$key]['id'] = $item['id'] = $mod_db->insert_id();
					echo 'inserted: #' . $item['id'] . ' : ' . $item['end_url'] . PHP_EOL;

					//if we have a source id, we can tag
					if( $item['source_id'] > 0 ):
						//topic/tag time
						$topics = array();

						//get topics (cached, daily)
						$source_topics = $mod_db->query( '
							(
								SELECT
									mod_topic.id, mod_topic.title, mod_topic.subscribers, mod_topic.type, mod_topic.parent_id, mod_topic2.title AS parent_title, 0 AS auto_tag
								FROM
									mod_topic LEFT JOIN mod_topic AS mod_topic2 ON mod_topic2.id = mod_topic.parent_id
								WHERE
									mod_topic.auto_tag = 1
								
							) UNION (
							
								SELECT
									mod_topic.id, mod_topic.title, mod_topic.subscribers, mod_topic.type, mod_topic.parent_id, mod_topic2.title AS parent_title,
									( mod_topic_websites.auto_tag and mod_topic.parent_id != mod_topic_websites.topic_id ) AS auto_tag
								FROM
									mod_topic_websites,
									mod_topic LEFT JOIN mod_topic AS mod_topic2 ON mod_topic2.id = mod_topic.parent_id
								WHERE
									mod_topic_websites.website_id = ' . $item['source_id'] . '
									AND ( mod_topic.id = mod_topic_websites.topic_id OR mod_topic.parent_id = mod_topic_websites.topic_id )
							)
							ORDER BY id
						', true, 86400 );
						//arrays
						$possible_topics = array();
						$auto_topics = array();

						//loop source topics
						foreach( $source_topics as $topic ):
							//add to possible
							$possible_topics[$topic['id']] = array(
								'id' => $topic['id'],
								'title' => $topic['title'],
								'parent' => $topic['parent_id'],
								'subscribers' => $topic['subscribers'],
								'used' => false
							);

							//parent? better add that
							if( $topic['parent_id'] > 0 and !isset( $possible_topics[$topic['parent_id']] ) ):
								$possible_topics[$topic['parent_id']] = array(
									'id' => $topic['parent_id'],
									'title' => $topic['parent_title'],
									'parent' => 0,
									'subscribers' => $topic['subscribers'],
									'used' => false
								);
							endif;

							//general, auto_tag?
							if( $topic['auto_tag'] == 1 ):
								$topics[] = array(
									'id' => $topic['id'],
									'title' => $topic['title'],
									'parent' => $topic['parent_id'],
									'subscribers' => $topic['subscribers']
								);
								//set to used
								$possible_topics[$topic['id']]['used'] = true;
							endif;
						endforeach;

						//title words
						$words = explode( ' ', $item['title'] );

						//foreach possible_topic, test
						foreach( $possible_topics as $key => $topic ):
							if( $possible_topics[$key]['used'] ) continue;

							foreach( $words as $word ):
								//match or substring?
								if( strcasecmp( $topic['title'], trim( substr( $word, 0, strlen( $topic['title'] ) ) ) ) == 0 ):
									$topics[] = array(
										'id' => $topic['id'],
										'title' => $topic['title'],
										'parent' => $topic['parent'],
										'subscribers' => $topic['subscribers']
									);
									$possible_topics[$key]['used'] = true;
									//add parent if one
									if( $topic['parent'] > 0 and !$possible_topics[$topic['parent']]['used'] ):
										$topics[] = array(
											'id' => $topic['parent'],
											'title' => $possible_topics[$topic['parent']]['title'],
											'parent' => $possible_topics[$topic['parent']]['parent'],
											'subscribers' => $possible_topics[$topic['parent']]['subscribers']
										);
										$possible_topics[$topic['parent']]['used'] = true;
										//parent has parent?
										if( $possible_topics[$topic['parent']]['parent'] > 0 and !$possible_topics[$possible_topics[$topic['parent']]['parent']]['used'] ):
											$topics[] = array(
												'id' => $possible_topics[$topic['parent']]['parent'],
												'title' => $possible_topics[$possible_topics[$topic['parent']]['parent']]['title'],
												'parent' => 0,
												'subscribers' => $possible_topics[$possible_topics[$topic['parent']]['parent']]['subscribers']
											);
											$possible_topics[$possible_topics[$topic['parent']]['parent']]['used'] = true;
										endif;
									endif;
								endif;
							endforeach;
						endforeach;

						//add to topic_articles (article_id, topic_id, time, source_id, source_title, source_data, article_time)
						foreach( $topics as $topic ):
							$tmp = array(
								'article_id' => $item['id'],
								'article_type' => $item['type'],
								'topic_id' => $topic['id'],
								'source_id' => $item['source_id'],
								'source_title' => '',
								'source_data' => '{}',
								'article_time' => $item['time']
							 );

							//do we have the source?
							if( $item['source_id'] > 0 ):
								$domain = parse_url( $item['source_url'] );
								$domain = isset( $domain['host'] ) ? $domain['host'] : '';

								$tmp['source_title'] = $item['source_title'];
								$tmp['source_data'] = json_encode( array( 'domain' => $domain, 'subscribers' => $item['source_subscribers'] ) );
							endif;
							//add to topic articles
							$topic_article[] = $tmp;

							//select topic subscribers
							$topic_subscribers = $mod_db->query( '
								SELECT user_id
								FROM mod_user_topics
								WHERE topic_id = ' . $topic['id'] . ( 
									$topic['parent'] > 0 ? ' OR topic_id = ' . $topic['parent'] : '' 
							), true, 3600 );

							//add basic info
							unset( $tmp['topic_id'] );
							$tmp['source_type'] = 'topic';
							$tmp['source_id'] = $topic['id'];
							$tmp['source_title'] = $topic['title'];
							$tmp['source_data'] = json_encode( array( 'subscribers' => $topic['subscribers'] ) );
							$tmp['origin_id'] = 0;
							$tmp['origin_title'] = '';
							$tmp['origin_data'] = '{}';

							//add to user_article
							foreach( $topic_subscribers as $subscriber ):
								$tmp['user_id'] = $subscriber['user_id'];
								$tmp['unread'] = count( $mod_memcache->get( 'mod_user_hides', array(
									array(
										'user_id' => $subscriber['user_id'],
										'article_id' => $item['id']
									)
								) ) ) == 1 ? 0 : 1;

								//origin?
								if( $item['source_id'] > 0 and isset( $domain ) ):
									$tmp['origin_id'] = $item['source_id'];
									$tmp['origin_title'] = $item['source_title'];
									$tmp['origin_data'] = json_encode( array( 'domain' => $domain ) );
								endif;

								//finally add
								$user_article[] = $tmp;
							endforeach;
						endforeach;
						echo 'article autotagged ' . count( $topics ) . ' times #' . $item['id'] . PHP_EOL;
					endif; //if source id > 0
				else:
					echo 'insert failed on: ' . $item['end_url'] . ' : ' . mysql_error() . PHP_EOL;
					continue;
				endif; //if insert

				//for mod_source when checking for articles
				@$mod_mcache->set( md5( $item['end_url'] ), $item['id'] );
				@$mod_mcache->set( md5( $item['url'] ), $item['id'] );
				@$mod_mcache->set( md5( $item['title'] ), $item['id'] );
			else:
				echo 'article already has id: ' . $item['id'] . PHP_EOL;
			endif;

			//finally, add to source_article
			if( $item['source_id'] > 0 ):
				$website_article[] = array(
					'website_id' => $item['source_id'],
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
					'article_type' => $item['type'],
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
				if( $source['type'] == 'website' ):
					$tmp['source_id'] = $source['id'];
					$tmp['source_title'] = $source['site_title'];

					$domain = parse_url( $source['site_url'] );
					$domain = $domain['host'];
					$tmp['source_data'] = json_encode( array( 'domain' => $domain, 'subscribers' => $item['source_subscribers'] ) );

				//facebook or twitter (so far)
				else:
					$tmp['source_id'] = $item['ex_userid']; //source ignored
					$tmp['source_title'] = $item['ex_username'];
					$tmp['source_data'] = json_encode( array( 'text' => $item['ex_text'], 'postid' => $item['ex_postid'] ) );

					//origin defined? (source 0 by default here)
					if( $item['source_id'] > 0 ):
						$tmp['origin_id'] = $item['source_id'];
						$tmp['origin_title'] = $item['source_title'];

						$domain = parse_url( $item['source_url'] );
						$domain = isset( $domain['host'] ) ? $domain['host'] : false;
						$tmp['origin_data'] = json_encode( array( 'domain' => $domain ? $domain : '' ) );
					endif;
				endif;

				//add to user_article
				$user_article[] = $tmp;
			endforeach;

			echo 'item complete: ' . $key . ' / ' . count( $items ) . ' : ' . $item['end_url'] . PHP_EOL;
		endforeach;

		//build the mod_source_articles query (after we have article ids)
		if( count( $website_article ) > 0 ):
			//store article counts per website
			$tmp = array();

			$sql = '
				INSERT IGNORE INTO mod_website_articles ( website_id, article_id, article_time )
				VALUES';

			foreach( $website_article as $bit ):
				$sql .= ' (
					' . $bit['website_id'] . ',
					' . $bit['article_id'] . ',
					' . $bit['article_time'] . '
				),';

				//article counter
				if( !isset( $tmp[$bit['website_id']] ) )
					$tmp[$bit['website_id']] = 1;
				else
					$tmp[$bit['website_id']]++;

			endforeach;

			$sql = rtrim( $sql, ',' );

			//run it
			$insert = $mod_db->query( $sql );

			//fail message
			if( !$insert ):
				echo 'mod_source_article insert failed: ' . mysql_error() . PHP_EOL . $sql . PHP_EOL;
			else:
				echo 'mod_source_article insert complete' . PHP_EOL;

				//update website article counts
				foreach( $tmp as $website_id => $count ):
					$website = $mod_memcache->get( 'mod_website', array( array(
						'id' => $website_id
					) ) );

					if( $website and count( $website ) == 1 )
						$mod_memcache->set( 'mod_website', array( array(
							'id' => $website_id,
							'articles' => $website[0]['articles'] + $count
						) ), false );

				endforeach;

			endif;
		endif;

		//build the mod_source_articles query
		if( count( $user_article ) > 0 ):
			$sql = '
				INSERT IGNORE INTO mod_user_articles ( user_id, article_id, article_type, source_type, source_id, source_title, article_time, unread, source_data, origin_id, origin_title, origin_data )
				VALUES';

			foreach( $user_article as $bit )
				$sql .= ' (
					' . $bit['user_id'] . ',
					' . $bit['article_id'] . ',
					"' . $bit['article_type'] . '",
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
			//store article counts per topic
			$tmp = array();

			$sql = '
				INSERT IGNORE INTO mod_topic_articles ( article_id, topic_id, source_id, source_title, source_data, article_time, article_type )
				VALUES';

			foreach( $topic_article as $bit ):
				$sql .= ' (
					' . $bit['article_id'] . ',
					' . $bit['topic_id'] . ',
					' . $bit['source_id'] . ',
					"' . $bit['source_title'] . '",
					\'' . $bit['source_data'] . '\',
					' . $bit['article_time'] . ',
					"' . $bit['article_type'] . '"
				),';

				//article counter
				if( !isset( $tmp[$bit['topic_id']] ) )
					$tmp[$bit['topic_id']] = 1;
				else
					$tmp[$bit['topic_id']]++;

			endforeach;

			$sql = rtrim( $sql, ',' );

			//run inserts
			$insert = $mod_db->query( $sql );

			//message
			if( !$insert ):
				echo 'mod_topic_article insert failed: ' . mysql_error() . PHP_EOL . $sql . PHP_EOL;
			else:
				echo 'mod_topic_article insert complete' . PHP_EOL;

				//update topic article counts
				foreach( $tmp as $topic_id => $count ):
					$topic = $mod_memcache->get( 'mod_topic', array( array(
						'id' => $topic_id
					) ) );

					if( $topic and count( $topic ) == 1 )
						$mod_memcache->set( 'mod_topic', array( array(
							'id' => $topic_id,
							'articles' => $topic[0]['articles'] + $count
						) ), false );

				endforeach;
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