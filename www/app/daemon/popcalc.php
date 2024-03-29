<?php
	/*
		file: app/process/cron/popcalc.php
		desc: updates the poptime for all recent articles (every 30 mins), then scales + makes pop score PER USER!
	*/

	//load modules
	global $mod_db, $mod_config, $mod_memcache, $threads, $threadtime, $dbtime, $argv, $mod_app;

	//remove mod_db
	$mod_db->__destruct();
	unset( $mod_db );

	//inc relevant update file
	if( !isset( $argv[2] ) or !in_array( $argv[2], array( 'topic', 'user' ) ) )
		die();

	//data
	$threads = 30;
	$threadtime = 15;
	$dbtime = 60;

	//load inc bit
	$mod_app->load( 'daemon/inc/popcalc/' . $argv[2] );

	//load daemon (db func, thread func, threads, thread time, db time)
	$daemon = new mod_daemon( 'dbupdate', 'popcalc', $threads, $threadtime, $dbtime, 'popcalc_' . $argv[2], 1000 );

	//and go!
	$daemon->start();



	//popcalc an item
	function popcalc( $item ) {
		global $mod_config;

		//child db conn
		$mod_db = get_db();

		//memcache
		$mod_memcache = new mod_memcache( $mod_db );

		switch( $item['type'] ):
			case 'user':
				//get users non-expired article ids
				$ids = $mod_db->query( '
					SELECT article_id AS id, source_id, source_type, origin_id
					FROM mod_user_articles
					WHERE expired = 0
					AND user_id = ' . $item['id'] . '
				' );
				if( !$ids or count( $ids ) <= 0 ):
					echo 'user #' . $item['id'] . ' has no articles, exiting' . PHP_EOL;
					exit( 0 );
				endif;
				break;
			case 'topic':
				//get users non-expired article ids
				$ids = $mod_db->query( '
					SELECT article_id AS id, source_id, "source" AS source_type
					FROM mod_topic_articles
					WHERE expired = 0
					AND topic_id = ' . $item['id'] . '
				' );
				if( !$ids or count( $ids ) <= 0 ):
					echo 'topic #' . $item['id'] . ' has no articles, exiting' . PHP_EOL;
					exit( 0 );
				endif;
				break;
		endswitch;

		//now get the articles
		$articledata = $mod_memcache->get( 'mod_article', $ids );

		//make each key = article id
		$articles = array();
		foreach( $articledata as $article ):
			$articles[$article['id']] = array(
				'refs' => 0,
				'popularity' => $article['popularity'],
				'time' => $article['time']
			);
		endforeach;

		//now add our ref count to each article
		foreach( $ids as $id ):
			if( !isset( $articles[$id['id']] ) )
				continue;
			
			//add ref
			$articles[$id['id']]['refs']++;

			//if no source id or source id = 0
			if( !isset( $articles[$id['id']]['source_id'] ) or $articles[$id['id']]['source_id'] == 0 )
				$articles[$id['id']]['source_id'] = ( $id['source_type'] == 'source' ) ? $id['source_id'] : $id['origin_id'];
		endforeach;

		//sources index
		$sources = array(
			0 => array(
				'articleCount' => 1,
				'popTotal' => 0
			)
		);
		$bigsource = 0;

		//now loop articles
		foreach( $articles as $key => $article ):
			//calculate time in hours
			$time = time() - $article['time'];
			$time = round( $time / 3600 );

			//poptime = popularity / hours
			if( $time <= 1 ) $time = 1;
			$pop_time = $article['popularity'] / $time;
			
			//set array
			$articles[$key]['popularity_time'] = $article['popularity_time'] = $pop_time * 1000;

			//no source? add it
			if( !isset( $sources[$article['source_id']] ) ):
				$sources[$article['source_id']] = array(
					'articleCount' => 1,
					'popTotal' => $article['popularity_time']
				);
				continue;
			endif;
			
			//now update the source info (if poptime > 0)
			if( $pop_time > 0 ):
				$sources[$article['source_id']]['articleCount']++;
				$sources[$article['source_id']]['popTotal'] += $article['popularity_time'];
			endif;
		endforeach;

		//loop sources
		foreach( $sources as $key => $source ):
			//calculate average poptime
			$sources[$key]['avgPop'] = $source['avgPop'] = $source['popTotal'] / $source['articleCount'];

			//check if biggest source
			if( $bigsource == 0 or ( $source['avgPop'] > $sources[$bigsource]['avgPop'] and $key != 0 ) )
				$bigsource = $key;
		endforeach;

		//re-loop sources
		foreach( $sources as $key => $source ):
			//big source? return a scale of 1
			if( $key == $bigsource ):
				$sources[$key]['scale'] = 1;
				continue;
			endif;

			//no division by 0!
			if( $source['avgPop'] <= 0 )
				$source['avgPop'] = 1;

			//make source scale
			$sources[$key]['scale'] = ( $sources[$bigsource]['avgPop'] / $source['avgPop'] ) * 0.8;
		endforeach;

		//loop articles
		foreach( $articles as $key => $article ):
			//no point updating this
			if( $article['popularity_time'] == 0 )
				continue;

			/*
				the magic formula, a guessing game
			*/
			//scale articles using the source scale and # of refs
			$articles[$key]['popscore'] = $article['popscore'] = round( $article['popularity_time'] * $sources[$article['source_id']]['scale'] * log( $article['refs'] ) );

			//finally, update db with popscore
			switch( $item['type'] ):
				case 'user':
					$update = $mod_db->query( '
						UPDATE mod_user_articles
						SET popscore = ' . $article['popscore'] . '
						WHERE user_id = ' . $item['id'] . '
						AND article_id = ' . $key
					);
					//error?
					if( !$update )
						echo 'error updating user ' . $item['id'] . ' and article ' . $key . ' : ' . mysql_error() . PHP_EOL;
					break;
				case 'topic':
					$update = $mod_db->query( '
						UPDATE mod_topic_articles
						SET popscore = ' . $article['popscore'] . '
						WHERE topic_id = ' . $item['id'] . '
						AND article_id = ' . $key
					);
					//error?
					if( !$update )
						echo 'error updating topic ' . $item['id'] . ' and article ' . $key . ' : ' . mysql_error() . PHP_EOL;
					break;
				endswitch;
		endforeach;

		//echo and done this user!
		echo $item['type'] . ' #' . $item['id'] . ' streams updated (' . count( $articles ) . ' articles)' . PHP_EOL;
	}
?>