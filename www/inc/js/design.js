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

	//make all buttons go to disabled on click
	$( 'input[type=submit]' ).bind( 'click', function( ev ) {
		$( ev.target ).addClass( 'disabled' );
	});

	//set 2col once if under 1280 wide
	if( pulsefeed.streamType && $( window ).width() <= 1280 && !cookie.get( 'auto_columns' ) && !cookie.get( 'two_col' ) ) {
		//toggle
		api.columnToggle( $( '.stream_column_toggle' ) );
		//set cookie
		cookie.set( 'auto_columns', true );
		cookie.set( 'two_col', true );
	}
}

//scroll to function
design.scrollTo = function( px ) {
	//firefox likes this
	$( 'html' ).animate( { scrollTop: px }, 300 );

	//everything else likes this
	$( 'body' ).animate( { scrollTop: px }, 300 );
}