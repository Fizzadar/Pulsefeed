<?php
	/*
		file: app/process/cron/popcalc.php
		desc: updates the poptime for all recent articles (every 30 mins), then scales + makes pop score PER USER!
	*/

	//load modules
	global $mod_db, $mod_config, $mod_memcache;

	//get users
	$users = $mod_db->query( '
		SELECT id
		FROM core_user
	' );

	//loop users
	foreach( $users as $user ):
		//get users non-expired article ids
		$ids = $mod_db->query( '
			SELECT article_id AS id, source_id, source_type, origin_id
			FROM mod_user_articles
			WHERE user_id = ' . $user['id'] . '
		' );
		if( !$ids or count( $ids ) <= 0 )
			continue;
		
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
			$time = $time ^ 2;

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
			
			//now update the source info
			$sources[$article['source_id']]['articleCount']++;
			$sources[$article['source_id']]['popTotal'] += $pop_time;
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
	endforeach;

	//echo & we're done!
	echo 'popcalc complete, updated ' . count( $users ) . ' users' . PHP_EOL;
?>