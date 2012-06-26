/*
	file: inc/js/api.page.js
	desc: extra page data (offsets)
*/

//load more sources
api.loadSource = function( el ) {
	$( el ).html( 'loading <img src="' + mod_root + '/inc/img/icons/loader.gif" alt="" />' );
	
	//make our request
	this.get(
		'/' + pulsefeed.sourceType + 's',
		{ offset: pulsefeed.sourceOffset },
		//success, lets distribute those articles
		function( data, el ) {
			if( data.result == 'success' ) {
				if( data.sources == null || data.sources.length == 0 ) {
					$( el ).html( 'no more ' + pulsefeed.sourceType + 's :(' );
					$( el ).removeClass( 'source_load_more' );
					$( el ).unbind( 'click' );
					$( el ).bind( 'click', function( ev ) {
						ev.preventDefault();
					});
					$( el ).addClass( 'disabled' );
					return;
				} else {
					//loop sources, add to div
					for( var i = 0; i < data.sources.length; i++ ) {
						$( '.sources' ).append( template.source( data.sources[i], pulsefeed.sourceType ) );
						//fade in
						queue.add( function( args ) {
							$( '#source_' + args.id ).animate( { opacity: 1 }, 250 );
						}, 150, { id: data.sources[i].id } );
					}
				}

				//up offset
				pulsefeed.sourceOffset++;
				//reload links
				api.start( false );
				//loading text
				$( el ).html( 'load more ' + pulsefeed.sourceType + 's &darr;' );
			} else {
				window.location = mod_root + '/' + pulsefeed.sourceType + 's?offset=' + pulsefeed.sourceOffset;
			}
		},
		//failure!
		function( data, el ) {
			window.location = mod_root + '/' + pulsefeed.sourceType + 's?offset=' + pulsefeed.sourceOffset;
		},
		el
	);
}

//work out stream api link
api.linkStream = function() {
	switch( pulsefeed.streamType ) {
		case 'public':
			return  '/public';
		case 'website':
			return '/website/' + pulsefeed.streamWebsite;
		case 'account':
			return '/account/' + pulsefeed.streamAccount;
		case 'collection':
			return '/collection/' + pulsefeed.streamCollection;
		case 'topic':
			return '/topic/' + pulsefeed.streamTopic;
		default:
			return '/user/' + pulsefeed.streamUser + '/' + pulsefeed.streamType;
	}
}

//hide current stream
api.hideStream = function() {
	$( '.col' ).animate( { height: 'toggle' }, 300, function() {
		$( '.col' ).html( '' );
	});
}

//show stream
api.showStream = function() {
	$( '.col' ).css( { height: 'auto' } );
}

//load more stream
api.loadStream = function( el, reload ) {
	$( el ).html( 'loading <img src="' + mod_root + '/inc/img/icons/loader.gif" alt="" />' );

	if( reload )
		this.hideStream();

	//build the link
	var link = this.linkStream();

	//make our request
	this.get(
		link,
		{ offset: pulsefeed.streamOffset },
		//success, lets distribute those articles
		function( data, el ) {
			if( data.result == 'success' ) {
				if( data.stream == null ) {
					$( el ).html( 'no more articles :(' );
					$( el ).removeClass( 'stream_load_more' );
					$( el ).unbind( 'click' );
					$( el ).bind( 'click', function( ev ) {
						ev.preventDefault();
					});
					$( el ).addClass( 'disabled' );
					return;
				}

				api.showStream();
				//build & load
				var stream = api.buildStream( data.stream );
				api.renderStream( stream, data.recommends );
				//increase our page offset
				pulsefeed.streamOffset++;
				//reload links
				api.start( false );
				//loading text
				$( el ).html( 'load more articles &darr;' );
			} else {
				window.location = mod_root + api.linkStream() + '?offset=' + pulsefeed.streamOffset;
			}
		},
		//failure!
		function( data, el ) {
			window.location = mod_root + api.linkStream() + '?offset=' + pulsefeed.streamOffset;
		},
		el
	);
}

//render stream
api.renderStream = function( stream, recommends ) {
	var length = 0;
	var stream_div_id = pulsefeed.streamOffset;

	$( '.stream_load_more' ).before( '<div class="block">&nbsp;</div><div class="stream_' + stream_div_id + '"><div class="col col1"></div><div class="col col2"></div><div class="col col3"></div>' );

	//work out longest length
	if( stream.col1.length > length )
		length = stream.col1.length;
	if( stream.col2.length > length )
		length = stream.col2.length;
	if( stream.col3.length > length )
		length = stream.col3.length;

	//now iterate
	for( var i = 0; i < length; i++ ) {
		//col 1
		if( stream.col1[i] != undefined ) {
			$( '.stream_' + stream_div_id + ' .col1' ).append( template.item( stream.col1[i] ) );
			//fade in
			queue.add( function( args ) {
				$( '#article_' + args.id ).animate( { opacity: 1 }, 250 );
			}, 100, { id: stream.col1[i].id } );
		}
		//col 2
		if( stream.col2[i] != undefined ) {
			$( '.stream_' + stream_div_id + ' .col2' ).append( template.item( stream.col2[i] ) );
			//fade in
			queue.add( function( args ) {
				$( '#article_' + args.id ).animate( { opacity: 1 }, 250 );
			}, 100, { id: stream.col2[i].id } );
		}
		//col 3
		if( stream.col3[i] != undefined ) {
			switch( pulsefeed.streamType ) {
				//2 col main, 1 col upcoming
				case 'hybrid':
				case 'popular':
				case 'public':
					$( '.stream_' + stream_div_id + ' .col3' ).append( template.item( stream.col3[i], true, 'h4' ) );
					break;
				default:
					$( '.stream_' + stream_div_id + ' .col3' ).append( template.item( stream.col3[i] ) );
			}

			//fade in
			queue.add( function( args ) {
				$( '#article_' + args.id ).animate( { opacity: 1 }, 250 );
			}, 100, { id: stream.col3[i].id } );
		}
	}
}

//build stream
api.buildStream = function( items ) {
	var cols = new Array();
	cols[1] = new Array();
	cols[2] = new Array();
	cols[3] = new Array();

	switch( pulsefeed.streamType ) {
		//2 col main, 1 col upcoming
		case 'hybrid':
		case 'popular':
		case 'public':
			if( items.length > 2 ) {
				//get 1/3 length
				var length = items.length;
				var third = Math.round( length / 3 );

				//get col3, items from length - third to length
				for( var i = length - third; i < length; i++ ) {
					items[i].short_description = items[i].shorter_description;
					cols[3][cols[3].length] = items[i];
				}

				//now generate other 2 cols
				var iscol2 = false;
				for( var i = 0; i < length - third; i++ ) {
					//choose the col
					if( iscol2 ) {
						cols[2][cols[2].length] = items[i];
					} else {
						cols[1][cols[1].length] = items[i];
					}

					//switch
					iscol2 = !iscol2;
				}
				break;
			}

		//3 col even
		default:
			var col = 1;

			//add each item
			for( var i = 0; i < items.length; i++ ) {
				cols[col][cols[col].length] = items[i];
				col++;
				if( col > 3 )
					col = 1;
			}
	}

	//return
	var r = {};
	r.col1 = cols[1];
	r.col2 = cols[2];
	r.col3 = cols[3];
	return r;
}