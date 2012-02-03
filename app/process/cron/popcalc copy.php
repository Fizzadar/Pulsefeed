<?php
	/*
		file: app/process/cron/popcalc.php
		desc: updates the poptime for all recent articles (every 30 mins), then scales + makes pop score
	*/

	//no time limits
	set_time_limit( 300 );
	//ignore abort
	ignore_user_abort( true );

	//load modules
	global $mod_db, $mod_config;

	//select articles within the last week
	$expire_time = time() - ( 3600 * 24 * 7 );

	//get articles
	$articles = $mod_db->query( '
		SELECT id, time, popularity, source_id
		FROM mod_article
		WHERE time > ' . $expire_time . '
		ORDER BY time DESC
	' );

	//sources array
	$sources = array();

	//update each articles poptime
	foreach( $articles as $key => $article ):
		//calculate time in hours since posting
		$time = time() - $article['time'];
		$time = round( $time / 3600 );

		//get inverse percentage of hours / config hours
		//$percentage = 1 - ( ( ( $time / 2 ) ^ 2 ) / 48 );

		//poptime = popularity / hours
		$pop_time = $article['popularity'] / $time;

		//time bigger than 48? = 60% off [old]
		if( $time > 48 )
			$pop_time = $pop_time * 0.6;

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

	//no work out scores for each article
	foreach( $articles as $key => $article ):
		//get average poptime for an article for this source
		$average = $sources[$article['source_id']]['popTotal'] / $sources[$article['source_id']]['articleCount'];
		//cant divide by 0
		if( $average == 0 ) $average = 1;

		//get percentage in relation to the sources articles
		$percentage = abs( $article['popularity_time'] ) / abs( $average );
		if( $percentage == 0 ) $percentage = 0.01;

		//calculate time in hours since posting
		$time = time() - $article['time'];
		$time = round( $time / 3600 );

		//now make the score (starts at 1000) - 100/hour
		$articles[$key]['popularity_score'] = round( $percentage * 500 ) - ( 100 * $time );
	endforeach;

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

		if( $update )
			echo 'article updated: #' . $article['id'] . "\n";
	endforeach;

	echo 'updated ' . count( $articles ) . ' articles' . "\n";
?>