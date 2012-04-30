/*
	file: inc/js/ajax.js
	desc: core ajax functions
*/
var ajax = {};

//start ajax (build links)
ajax.start = function() {
	$( 'a.ajax' ).click( function( ev ) {
		ev.preventDefault();
		ajax.load( $( this ).attr( 'href' ) );
	});

	//sort back button
	$( window ).bind( 'popstate', function( ev ) {
		var prev = ev.originalEvent.state;
		if( prev && prev.path ) {
			ajax.load( prev.path );
		}
	});
	history.replaceState( { path: window.location.href }, '' );
}

//inline ajax start
ajax.startInline = function() {
	$( 'div#ajaxbox a.ajax' ).click( function( ev ) {
		ev.preventDefault();
		ajax.load( $( this ).attr( 'href' ) );
	});
}

//load ajax page
ajax.load = function( url ) {
	//ajax request
	$.ajax({
		async: false,
		url: url + '?ajax',
		success: function( data ) {
			//set box to our page
			$( '#ajaxbox' ).html( data );
			//set history
			window.history.pushState( { path: url }, '', url );
			//ajax those links!
			ajax.startInline( true );
		}
	});
	console.log( 'Ajax fired: ' + url );
}