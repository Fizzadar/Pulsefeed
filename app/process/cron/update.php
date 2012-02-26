<?php
	/*
		file: app/process/cron/update.php
		desc: gets new articles (every 10 min)
	*/
	
	//config
	$children = 10;
	$childsources = 5;

	//time!
	$s_time = time();
	echo '
###############
############### ===> Pulsefeed Source Updater
###############
';
	echo 'Starting @: ' . time() . "\n";

	//load modules
	global $mod_db, $mod_config;

	//min 60 min between feed checks
	$update_time = time() - 3600;

	//select articles to update (last article_expire hours, 60 max, lowest update time first)
	$sources = $mod_db->query( '
		SELECT id, feed_url
		FROM mod_source
		WHERE update_time < ' . $update_time . '
		AND id > 0
		ORDER BY update_time ASC
		LIMIT ' . ( $children * $childsources ) . '
	' );

	//no longer needed (without causes major fail )
	$mod_db->__destruct();
	unset( $mod_db );

	//build childdata array
	$childdata = array();
	for( $i = 0; $i < $children; $i++ ):
		$childdata[$i] = array();
	endfor;

	//distribute our children
	$childcount = 0;
	foreach( $sources as $source ):
		//reset?
		if( $childcount > $children ) $childcount = 0;
		//insert
		$childdata[$childcount][] = $source;
		$childcount++;
	endforeach;

	//update function
	function update( $i, $sources ) {
		global $mod_config;

		//child db conn
		$child_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$child_db->connect();

		//go
		echo '[Child ' . $i . '] ' . count( $sources ) . ' to update' . "\n";

		//now loop the sources
		foreach( $sources as $source ):
			//load the feed
			$feed = new mod_source();
			$items = $feed->load( $source['feed_url'] );
			$articles = array();
			$skipped_count = 0;

			//loop our items
			foreach( $items as $key => $item ):
				$end_url = $item->get_end_url();
				$url = $item->get_permalink();

				//skip duplicates
				$got = $child_db->query( '
					SELECT id
					FROM mod_article
					WHERE end_url = "' . $end_url . '"
					OR url = "' . $url . '"
					OR ( title = "' . $item->get_title() . '" AND source_id = ' . $source['id'] . ' )
					LIMIT 1
				');
				if( count( $got ) != 0 ):
					$skipped_count++;
					continue;
				endif;

				//must do article first (fills image list)
				$article = $item->get_article();
				$images = $item->get_thumbs();

				//get our data
				$input = array(
					'title' => $item->get_title(),
					'url' => $item->get_permalink(),
					'end_url' => $item->get_end_url(),
					'summary' => $item->get_summary(),
					'image_quarter' => isset( $images['quarter'] ) ? $images['quarter'] : '',
					'image_third' => isset( $images['third'] ) ? $images['third'] : '',
					'image_half' => isset( $images['half'] ) ? $images['half'] : '',
					'time' => $item->get_time(),
				);
				$articles[] = $input;

				//free the ram
				unset( $article );
				unset( $images );
			endforeach;

			//free some ram
			unset( $items );
			unset( $feed );

			//clean all the items
			$articles = $child_db->clean( $articles );

			//make sure we actually have some articles left
			if( count( $articles ) < 1 ):
				$child_db->query( '
					UPDATE mod_source
					SET update_time = ' . time() . '
					WHERE id = ' . $source['id'] . '
					LIMIT 1
				' );
				echo '[Child ' . $i . '] update skipped on: ' . $source['feed_url'] . ', already got all articles' . "\n";
				continue;
			endif;

			//insert articles
			$inserted_count = 0;
			foreach( $articles as $article ):
				//insert article
				$sql = '
					INSERT INTO mod_article
					( source_id, title, url, end_url, description, time, image_quarter, image_third, image_half )
					VALUES
					( ' . $source['id'] . ', "' . $article['title'] . '", "' . $article['url'] . '", "' . $article['end_url'] . '", "' . $article['summary'] . '", ' . $article['time'] . ', "' . $article['image_quarter'] . '", "' . $article['image_third'] . '", "' . $article['image_half'] . '" )
				';
				$insert = $child_db->query( $sql );
				if( !$insert ):
					echo '[Child ' . $i . '] error inserting: ' . mysql_error() . $sql . "\n";
					continue;
				endif;

				//count it
				$inserted_count++;
				
				//get id
				$id = $child_db->insert_id();

				//auto-tagging
				$title_words = explode( ' ', $article['title'] );
				//select tags based on words, and tag

				

				//select users subscribed to source
				$users = $child_db->query( '
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
					$child_db->query( $sql );
				endif;
			endforeach;

			//finally update the source's update_time
			$child_db->query( '
				UPDATE mod_source
				SET update_time = ' . time() . ',
				articles = articles + ' . $inserted_count . '
				WHERE id = ' . $source['id'] . '
				LIMIT 1
			' );
			echo '[Child ' . $i . '] update complete on: ' . $source['feed_url'] . ', added ' . $inserted_count . ' articles, ' . $skipped_count . ' skipped' . "\n";
		endforeach;
	}

	//build our threads
	$threads = array();
	foreach( $childdata as $key => $sources ):
		$threads[$key] = new Thread( 'update' );
		$threads[$key]->start( $key, $sources );
	endforeach;

	//wait on them
	$timer = 0;
	while( !empty( $threads ) ):
		//loop threads, check stopped
		foreach( $threads as $key => $thread ):
			//timer check
			if( $timer > 600 ):
				echo 'Warning: timer above 600, killing thread#' . $key . "\n";
				$thread->stop();
			endif;
			//alive?
			if( !$thread->isAlive() ):
				unset( $threads[$key] );
			endif;
		endforeach;
		//sleep
		sleep( 1 );
		//up timer
		$timer++;
	endwhile;

	$e_time = time() - $s_time;
	echo 'total time: ' . $e_time . 's' . "\n";
?>