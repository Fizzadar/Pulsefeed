<?php
	/*
		file: app/daemon/inc/recommend/user.php
		desc: recommend user server
	*/
	global $threads, $threadtime, $dbtime;

	//setup update data
	$threads = 15;
	$threadtime = 600;
	$dbtime = 60;

	//function used by deamon to get 'jobs'
	function dbupdate() {
		global $mod_config, $argv, $c_config;

		//new db
		$mod_db = get_db();

		//min 4 hour min between recommend checks
		$update_time = time() - ( 3600 * 4 );

		//select users to update
		$users = $mod_db->query( '
			SELECT id, recommend_time
			FROM core_user
			WHERE recommend_time < ' . $update_time . '
			ORDER BY recommend_time ASC
			LIMIT 100
		' );

		//update the same set of users update time
		$mod_db->query( '
			UPDATE core_user
			SET recommend_time = ' . time() . '
			WHERE recommend_time < ' . $update_time . '
			ORDER BY recommend_time ASC
			LIMIT 100
		' );
	
		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//return to daemon
		return $users;
	}

	//function used to recommend
	function recommend( $user ) {
		//db
		$mod_db = get_db();

		//mod_memcache
		$mod_memcache = new mod_memcache( $mod_db );

		//select users read articles
		$reads = $mod_db->query( '
			SELECT article_id
			FROM mod_user_reads
			WHERE time >= ' . $user['recommend_time'] . '
		' );

		//build query
		$sql = '
			SELECT topic_id AS id
			FROM mod_topic_articles
			WHERE ( ';
		foreach( $reads as $read )
			$sql .= 'article_id = ' . $read['article_id'] . ' OR ';
		$sql = rtrim( $sql, ' OR ' );
		$sql .= ' ) 
			GROUP BY topic_id';

		//select topics
		$topics = $mod_db->query( $sql );

		if( !$topics )
			die( mysql_error() );

		//re-get topics
		$topic_data = $mod_memcache->get( 'mod_topic', $topics );

		//add to possible topics
		$topics = array();
		$list = array();
		foreach( $topic_data as $topic ):
			$topics[$topic['id']] = array(
				'id' => $topic['id'],
				'title' => $topic['title'],
				'subscribed' => count( $mod_memcache->get( 'mod_user_topics', array( array(
					'user_id' => $user['id'],
					'topic_id' => $topic['id']
				) ) ) ) == 1 ? true : false
			);
			//parent? add to list
			if( $topic['parent_id'] > 0 )
				$list[] = array(
					'id' => $topic['parent_id']
				);
		endforeach;

		//do parents
		$topic_data = $mod_memcache->get( 'mod_topic', $list );
		foreach( $topic_data as $topic ):
			$topics[$topic['id']] = array(
				'id' => $topic['id'],
				'title' => $topic['title'],
				'subscribed' => count( $mod_memcache->get( 'mod_user_topics', array( array(
						'user_id' => $user['id'],
						'topic_id' => $topic['id']
					) ) ) ) == 1 ? true : false
				);
		endforeach;

		//pick articles per topic
		$sql = '
			SELECT article_id, topic_id, source_id, source_title, source_data, article_time, article_type, popscore
			FROM mod_topic_articles
			WHERE ( ';
		foreach( $topics as $topic )
			$sql .= 'topic_id = ' . $topic['id'] . ' OR ';
		$sql = rtrim( $sql, ' OR ' );
		$sql .= ' )
			AND expired = 0
			GROUP BY article_id
			ORDER BY popscore DESC
			LIMIT 20';

		//select articles
		$articles = $mod_db->query( $sql );

		//add each article to user articles
		$user_article = array();

		//build
		foreach( $articles as $article ):
			//basics
			$tmp = array(
				'user_id' => $user['id'],
				'article_id' => $article['article_id'],
				'article_type' => $article['article_type'],
				'unread' => 0,
				'source_id' => $article['topic_id'],
				'source_type' => 'recommend',
				'source_title' => $topics[$article['topic_id']]['title'],
				'source_data' => '{}',
				'article_time' => $article['article_time'],
				'popscore' => $article['popscore'],
				'origin_id' => $article['source_id'],
				'origin_title' => $article['source_title'],
				'origin_data' => $article['source_data']
			);

			//unread
			$tmp['unread'] = count( $mod_memcache->get( 'mod_user_hides', array(
				array(
					'user_id' => $user['id'],
					'article_id' => $article['article_id']
				)
			) ) ) == 1 ? 0 : 1;

			//boom!
			$user_article[] = $tmp;
		endforeach;

		//build the mod_source_articles query
		if( count( $user_article ) > 0 ):
			$sql = '
				INSERT IGNORE INTO mod_user_articles ( user_id, article_id, article_type, source_type, source_id, source_title, article_time, unread, source_data, origin_id, origin_title, origin_data, popscore )
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
					\'' . $bit['origin_data'] . '\',
					' . $bit['popscore'] . '
				),';

			$sql = rtrim( $sql, ',' );

			//run it
			$insert = $mod_db->query( $sql );

			//fail message
			if( !$insert ):
				echo 'user#' . $user['id'] . ' mod_user_article insert failed: ' . mysql_error() . PHP_EOL . $sql . PHP_EOL;
			else:
				echo 'user#' . $user['id'] . ' mod_user_article insert complete' . PHP_EOL;
			endif;
		endif;
	}
?>