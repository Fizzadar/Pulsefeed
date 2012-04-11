<?php
	/*
		file: app/process/cron/popcalc.php
		desc: updates the poptime for all recent articles (every 30 mins), then scales + makes pop score PER USER!
	*/

	//load modules
	global $mod_db, $mod_config, $mod_memcache;

	//remove mod_db
	$mod_db->__destruct();
	unset( $mod_db );

	//load daemon (db func, thread func, threads, thread time, db time)
	$daemon = new mod_daemon( 'dbupdate', 'popcalc', 30, 15, 60, 'popcalc' );

	//and go!
	$daemon->start();

	//popcal a user
	function popcalc( $user ) {
		global $mod_config;

		//new db
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

		//memcache
		$mod_memcache = new mod_memcache( $mod_db );

		//get users non-expired article ids
		$ids = $mod_db->query( '
			SELECT article_id AS id, source_id, source_type, origin_id
			FROM mod_user_articles
			WHERE expired = 0
			AND user_id = ' . $user['id'] . '
		' );
		if( !$ids or count( $ids ) <= 0 ):
			echo 'user #' . $user['id'] . ' has no articles, exiting' . PHP_EOL;
			exit( 0 );
		endif;

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
				$articles[$id['id']]['source_id'] = ( $id['source_type'] =='source' ) ? $id['source_id'] : $id['origin_id'];
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
			//calculate poptime
			$time = time() - $article['time'];
			$time = round( $time / 3600 );
			$time = $time ^ 3; //still very-much a guessing game

			//poptime = popularity / hours
			if( $time <= 1 ) $time = 1;
			$pop_time = $article['popularity'] / $time;
			
			//set array
			$articles[$key]['popularity_time'] = $pop_time;

			//no source? add it
			if( !isset( $sources[$article['source_id']] ) ):
				$sources[$article['source_id']] = array(
					'articleCount' => 1,
					'popTotal' => $pop_time
				);
				continue;
			endif;
			
			//now update the source info (if poptime > 0)
			if( $pop_time > 0 ):
				$sources[$article['source_id']]['articleCount']++;
				$sources[$article['source_id']]['popTotal'] += $pop_time;
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
			//no division by 0!
			if( $source['avgPop'] <= 0 )
				$source['avgPop'] = 1;

			//make source scale
			$sources[$key]['scale'] = $sources[$bigsource]['avgPop'] / ( $source['avgPop'] );
		endforeach;

		//loop articles
		foreach( $articles as $key => $article ):
			//scale articles using the source scale and # of refs
			$articles[$key]['popscore'] = $article['popscore'] = round( $article['popularity_time'] * $sources[$article['source_id']]['scale'] * 100 ) * pow( $article['refs'], 2 );

			//finally, update db with popscore
			$update = $mod_db->query( '
				UPDATE mod_user_articles
				SET popscore = ' . $article['popscore'] . '
				WHERE user_id = ' . $user['id'] . '
				AND article_id = ' . $key
			);

			//error?
			if( !$update )
				echo 'error updating user ' . $user['id'] . ' and article ' . $key . ' : ' . mysql_error() . PHP_EOL;
		endforeach;

		//echo and done this user!
		echo 'user #' . $user['id'] . ' streams updated (' . count( $articles ) . ' articles)' . PHP_EOL;
	}

	//get user function
	function dbupdate() {
		global $mod_config;

		//new db
		$mod_db = new c_db( $mod_config['dbhost'], $mod_config['dbuser'], $mod_config['dbpass'], $mod_config['dbname'] );
		$mod_db->connect();

		//min 30 min between popcalcs
		$update_time = time() - 1800;

		//select users to update
		$users = $mod_db->query( '
			SELECT *
			FROM core_user
			WHERE update_time < ' . $update_time . '
			ORDER BY update_time ASC
			LIMIT 100
		' );

		//update the same set of users update time
		$mod_db->query( '
			UPDATE core_user
			SET update_time = ' . time() . '
			WHERE update_time < ' . $update_time . '
			ORDER BY update_time ASC
			LIMIT 100
		' );

		//remove db
		$mod_db->__destruct();
		unset( $mod_db );

		//return to daemon
		return $users;
	}
?>