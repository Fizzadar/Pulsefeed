/*
	file: inc/js/design.js
	desc: design commands
*/
var design = {};

//start
design.start = function() {
	//top links
	$( '.top_link' ).bind( 'click', function( ev ) {
		ev.preventDefault();
		design.scrollTo( 0 );
	});
	//article links
	$( '.article_link' ).bind( 'click', function( ev ) {
		localStorage.pulsefeedprevScroll = $( 'body' ).scrollTop();
	});
	//scroll if we must
	if( pulsefeed.stream && localStorage.pulsefeedprevScroll > 0 ) {
		this.scrollTo( localStorage.pulsefeedprevScroll - 200 );
		localStorage.pulsefeedprevScroll = 0;
	}
}

//scroll to function
design.scrollTo = function( px ) {
	$( 'body' ).animate( { scrollTop: px }, 300 );
}