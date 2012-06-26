/*
	file: inc/js/pulsefeed.js
	desc: pulsefeeds core js file
*/

//ie is shit!
if( !console ) {
	console = {
		log: function( text ) {}
	}
}

//start pf
pulsefeed.start = function() {
	console.log( '[Pulsefeed] Charging up...' ); //red alert 2 <3

	//start stuff
	message.start();
	api.start( true );
	design.start();
	queue.start();

	console.log( '[Pulsefeed] Charged.' );
}

//start pf using jquery
$( document ).ready( function() {
	pulsefeed.start();
});