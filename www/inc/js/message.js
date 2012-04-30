/*
	file: inc/js/message
	desc: handle messages
*/
var message = {};

//start our messages
message.start = function() {
	//hide them on click
	$( 'div.message' ).click( function( el ) {
		$( this ).slideUp( 200 );
	});

	//slide up after 2s
	function messageslide() {
		$( 'div.message' ).slideUp( 200 );
	}
	setTimeout( messageslide, 2000 );
};

//add message
message.add = function( message, type ) {
	//add the message (normally warning)
	$( '#messages' ).append( '<div class="message ' + type + '"><div class="wrap">' + message + '<span class="right">hide message</span></div></div>' );

	//hide after 2 seconds
	this.start();
}