<?php
	//command line only
	//if( !defined( 'STDIN' ) )
		//die( 'Command line only' );

	//no time limits
	set_time_limit( 0 );
	//ignore abort
	ignore_user_abort( true );

	//load modules
	global $mod_db;

	//min 15 min between feed checks
	$update_time = time() - ( 30 * 15 );

	//get the least-updated sources
	$sources = $mod_db->query( '
		SELECT id, feed_url, hack
		FROM mod_sources
		WHERE update_time < ' . $update_time . '
		ORDER BY update_time ASC
		LIMIT 15
	' );

	if( count( $sources ) < 1 )
		echo 'No sources to be updated';
		
	//now loop the sources
	foreach( $sources as $source ):
		//load the feed
		$feed = new mod_source();
		$items = $feed->load( $source['feed_url'] );
		$articles = array();
		$skipped_count = 0;

		//loop our items
		foreach( $items as $item ):
			//skip duplicates
			$got = $mod_db->query( '
				SELECT id
				FROM mod_articles
				WHERE url = "' . $item->get_permalink() . '"
				LIMIT 1'
			);
			if( count( $got ) != 0 ):
				$skipped_count++;
				continue;
			endif;

			//get our data
			$input = array(
				'title' => $item->get_title(),
				'url' => $item->get_permalink(),
				'article' => $item->get_article( $source['hack'] ),
				'summary' => $item->get_summary(),
				'thumbnail' => $item->get_thumb(),
				'time' => $item->get_time(),
			);
			$articles[] = $input;
		endforeach;
		//clean all the items
		$articles = $mod_db->clean( $articles );

		//make sure we actually have some articles left
		if( count( $articles ) < 1 ):
			$mod_db->query( '
				UPDATE mod_sources
				SET update_time = ' . time() . '
				WHERE id = ' . $source['id'] . '
				LIMIT 1
			' );
			echo 'update <strong>skipped</strong> on: ' . $source['feed_url'] . ', already got all articles<br />';
			continue;
		endif;

		//insert articles
		foreach( $articles as $article ):
			//insert article
			$insert = $mod_db->query( '
				INSERT INTO mod_articles
				( source_id, title, url, description, content, time, image )
				VALUES
				( ' . $source['id'] . ', "' . $article['title'] . '", "' . $article['url'] . '", "' . $article['summary'] . '", "' . $article['article'] . '", ' . $article['time'] . ', "' . $article['thumbnail'] . '" )
			' );
			//get id
			$id = $mod_db->insert_id();

			//select users subscribed to source
			$users = $mod_db->query( '
				SELECT user_id
				FROM mod_user_sources
				WHERE source_id = ' . $source['id'] . '
				LIMIT 1
			' );
			if( count( $users ) > 0 ):
				//update unread
				$sql = '
					INSERT INTO mod_user_unread
					( user_id, article_id )
					VALUES';
				foreach( $users as $user )
					$sql .= '( ' . $user['user_id'] . ', ' . $id . ' ),';
				//remove ,
				$sql = rtrim( $sql, ',' );
				$mod_db->query( $sql );
			endif;
		endforeach;

		//finally update the source's update_time
		$mod_db->query( '
			UPDATE mod_sources
			SET update_time = ' . time() . '
			WHERE id = ' . $source['id'] . '
			LIMIT 1
		' );
		echo 'update <strong>complete</strong> on: ' . $source['feed_url'] . ', added <strong>' . count( $articles ) . '</strong> articles, ' . $skipped_count . ' skipped<br />';
	endforeach;
?>