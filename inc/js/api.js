/*
	file: inc/js/api.js
	desc: core api functions
*/
var api = {};

//api startup
api.start = function() {
	//bind like buttons
	$( '.like_form' ).bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.like( ev.target );
	});
	$( '.like_form_external' ).bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.likeExternal( ev.target );
	});
	//bind hide buttons
	$( '.hide_form' ).bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.read( ev.target );
	});
	//bind subscribe buttons
	$( '.source_subscribe' ).bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.subscribe( ev.target );
	});
	//bind follow buttons
	$( '.user_follow' ).bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.follow( ev.target );
	});
	//pf stream?
	if( pulsefeed.stream ) {
		$( '.stream_load_more' ).bind( 'click', function( ev ) {
			ev.preventDefault();
			api.loadStream( ev.target );
		});
	}
	//load more sources
	$( '..source_load_more' ).bind( 'click', function( ev ) {
		ev.preventDefault();
		api.loadSource();
	});
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
			mod_token = data.mod_token;
			if( data.result == 'success' ) {
				success( data, this );
				console.log( '[Pulsefeed] API Request Success: ' + data.message );
			} else {
				failure( data, this );
				console.log( '[Pulsefeed] API Request Failed: ' + data.message );
			}
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