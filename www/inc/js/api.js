/*
	file: inc/js/api.js
	desc: core api functions
*/
var api = {};

//api startup
api.start = function( full ) {
	//hide stuff on background click
	$( 'html' ).unbind().bind( 'click', function( ev ) {
		//stream
		$( '.item .meta .collect_button' ).removeClass( 'active' );
		$( '.item .meta form.share_form input[type=submit]' ).removeClass( 'active' );
		$( '.item .meta ul.collections' ).remove();
		$( '.item .meta ul.shares' ).remove();

		//external
		$( 'ul#external ul.collections' ).remove();
		$( 'ul#external .collect_button_external' ).removeClass( 'active' );
		$( 'ul#external ul.shares' ).remove();
		$( 'ul#external .share_form_external button' ).removeClass( 'active' );

		//search
		$( '#search_results' ).css( 'display', 'none' );
	});

	//bind hide buttons
	$( '.hide_form' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.read( ev.target );
	});
	//bind subscribe website buttons
	$( '.website_subscribe' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.subscribeWebsite( ev.target );
	});
	//bind subscribe topic buttons
	$( '.topic_subscribe' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.subscribeTopic( ev.target );
	});
	//bind follow buttons
	$( '.user_follow' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.follow( ev.target );
	});
	//bind collect buttons
	$( '.collect_button' ).unbind().bind( 'click', function( ev ) {
		ev.preventDefault();
		ev.stopPropagation();
		api.collect( ev.target );
	});
	//bind uncollect buttons
	$( '.uncollect_form' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.uncollect( ev.target );
	});
	//bind share buttons
	$( '.share_form' ).unbind().bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.share( ev.target );
	});
	$( '.share_form input[type=submit]' ).unbind().bind( 'click', function( ev ) {
		ev.stopPropagation();
	});


	//full load?
	if( full ) {
		//pf stream?
		$( '.stream_load_more' ).bind( 'click', function( ev ) {
			ev.preventDefault();
			api.loadStream( ev.target, false );
		});
		//load more sources
		$( '.source_load_more' ).bind( 'click', function( ev ) {
			ev.preventDefault();
			api.loadSource( ev.target );
		});
		//bind search form
		$( 'form#search' ).bind( 'submit', function( ev ) {
			ev.preventDefault();
			api.search( ev.target, 0 );
		});
		//external collect button
		$( '.collect_button_external' ).unbind().bind( 'click', function( ev ) {
			ev.preventDefault();
			ev.stopPropagation();
			api.collectExternal( ev.target );
		});
		//external share button
		$( '.share_form_external' ).unbind().bind( 'submit', function( ev ) {
			ev.preventDefault();
			api.share( ev.target );
		});
		$( '.share_form_external button' ).unbind().bind( 'click', function( ev ) {
			ev.stopPropagation();
		});
		//option: image toggle
		$( '.stream_images_toggle' ).unbind().bind( 'click', function( ev ) {
			ev.preventDefault();
			api.imageToggle( ev.target );
		});
		//option: column toggle
		$( '.stream_column_toggle' ).unbind().bind( 'click', function( ev ) {
			ev.preventDefault();
			api.columnToggle( ev.target );
		});
		//option: order toggle
		$( '.stream_order_toggle' ).unbind().bind( 'click', function( ev ) {
			ev.preventDefault();
			api.orderToggle( ev.target );
		});
		//option: message toggle
		$( '.stream_message_toggle' ).unbind().bind( 'click', function( ev ) {
			ev.preventDefault();
			api.messageToggle( ev.target );
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