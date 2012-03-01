/*
	file: inc/js/pulsefeed.js
	desc: pulsefeeds core js file
*/
var pulsefeed = {};
pulsefeed.stream = false;

//start pf
pulsefeed.start = function() {
	console.log( '[Pulsefeed] Charging up...' );

	//start stuff
	message.start();
	api.start();
	design.start();
	queue.start();

	console.log( '[Pulsefeed] Charged.' );
}

//start pf using jquery
$( document ).ready( function() {
	pulsefeed.start();
});