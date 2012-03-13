<?php
	/*
		file: app/process/cron/popcalc.php
		desc: updates the poptime for all recent articles (every 30 mins), then scales + makes pop score
	*/

	//load modules
	global $mod_db, $mod_config;

	//get articles
	$articles = $mod_db->query( '
		SELECT id, time, popularity, source_id
		FROM mod_article
		WHERE expired = 0
		ORDER BY time DESC
	' );

	//sources array
	$sources = array();

	//update each articles poptime
	foreach( $articles as $key => $article ):
		//calculate time in hours since posting
		$time = time() - $article['time'];
		$time = round( $time / 3600 );
		$time = $time ^ 2;

		//poptime = popularity / hours
		if( $time <= 1 ) $time = 1;
		$pop_time = $article['popularity'] / $time;
		
		//set array
		$articles[$key]['popularity_time'] = $pop_time;

		//no source? add it
		if( !isset( $sources[$article['source_id']] ) )
			$sources[$article['source_id']] = array(
				'articleCount' => 0,
				'popTotal' => 0
			);
		
		//now update the source info
		$sources[$article['source_id']]['articleCount']++;
		$sources[$article['source_id']]['popTotal'] += $pop_time;
	endforeach;

	//work out average for each source
	foreach( $sources as $key => $source )
		$sources[$key]['average'] = $source['popTotal'] / $source['articleCount'];

	//locate the highest source
	$bigSource = 0;
	foreach( $sources as $key => $source )
		if( $bigSource == 0 or $source['average'] > $sources[$bigSource]['average'] )
			$bigSource = $key;
	
	//now scale all our sources (skipping source 0)
	foreach( $sources as $key => $source )
		$sources[$key]['scale'] = $sources[$bigSource]['average'] / ( $source['average'] + 1 );

	//now work out scores for each article
	foreach( $articles as $key => $article )
		$articles[$key]['popularity_score'] = round( $article['popularity_time'] * $sources[$article['source_id']]['scale'] * 10000 );

	//loop articles, update
	foreach( $articles as $article ):
		//and update!
		$update = $mod_db->query( '
			UPDATE mod_article
			SET
				popularity_score = ' . $article['popularity_score'] . '
			WHERE id = ' . $article['id'] . '
			LIMIT 1
		' );

		//update 2
		$update2 = $mod_db->query( '
			UPDATE mod_user_articles
			SET
				popscore = ' . $article['popularity_score'] . '
			WHERE article_id = ' . $article['id'] . '
		' );

		if( $update and $update2 )
			echo 'article updated: #' . $article['id'] . "\n";
	endforeach;

	echo 'updated ' . count( $articles ) . ' articles' . "\n";
?>