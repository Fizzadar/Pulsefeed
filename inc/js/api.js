/*
	file: inc/js/api.js
	desc: core api functions
*/
var api = {};

//api startup
api.start = function( full ) {
	//bind like buttons
	$( '.like_form' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.like( ev.target );
	});
	//bind hide buttons
	$( '.hide_form' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.read( ev.target );
	});
	//bind subscribe buttons
	$( '.source_subscribe' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.subscribe( ev.target );
	});
	//bind follow buttons
	$( '.user_follow' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.follow( ev.target );
	});
	//bind collect buttons
	$( '.collect_button' ).unbind().bind( 'click', function( ev ) {
		ev.preventDefault();
		api.collect( ev.target );
	});
	//bind hide buttons
	$( '.uncollect_form' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.uncollect( ev.target );
	});


	//full load?
	if( full ) {
		//pf stream?
		if( pulsefeed.stream ) {
			$( '.stream_load_more' ).bind( 'click', function( ev ) {
				ev.preventDefault();
				api.loadStream( ev.target, false );
			});
		}
		//load more sources
		if( pulsefeed.sbrowser ) {
			$( '.source_load_more' ).bind( 'click', function( ev ) {
				ev.preventDefault();
				api.loadSource( ev.target );
			});
		}
		//bind search form
		$( 'form#search' ).bind( 'submit', function( ev ) {
			ev.preventDefault();
			api.search( ev.target, 0 );
		});
		//hide on click out of search
		$( 'form#search input' ).bind( 'blur', function( ev ) {
			api.searchActive = 0;
			setTimeout( 'api.hideSearch()', 150 ); //done in case searchActive swiched back immediately #hacky!
		});
		//external like
		$( '.like_form_external' ).unbind().bind( 'submit', function( ev ) {
			ev.preventDefault();
			api.likeExternal( ev.target );
		});
		//external collect button
		$( '.collect_button_external' ).unbind().bind( 'click', function( ev ) {
			ev.preventDefault();
			api.collectExternal( ev.target );
		});
	}
}

//request
api.request = function( type, url, data, success, failure, element ) {
	//do request
	$.ajax({
		type: type,
		url: mod_root + url + '?iapi',
		data: data,
		context: element,
		dataType: 'json',
		success: function( data ) {
			if( !data || !data.mod_token || !data.result ) {
				failure( data, this );
				console.log( '[Pulsefeed] API Request Failed: invalid data returned!' );
			} else {
				mod_token = data.mod_token;
				if( data.result == 'success' ) {
					success( data, this );
					console.log( '[Pulsefeed] API Request Success: ' + data.message );
				} else {
					failure( data, this );
					console.log( '[Pulsefeed] API Request Failed: ' + data.message );
				}
			}
		},
		error: function( data, text, error ) {
			failure( data, this );
			console.log( '[Pulsefeed] API Request Failed Miserably: ' + text );
		}
	});

	//log
	console.log( '[Pulsefeed] API Request Fired: ' + url );
}

//make get request
api.get = function( url, data, success, failure, element ) {
	//request
	this.request( 'GET', url, data, success, failure, element );
}

//make post request
api.post = function( url, data, success, failure, element ) {
	//add mod token
	data.mod_token = mod_token;
	//request
	this.request( 'POST', url, data, success, failure, element );
}