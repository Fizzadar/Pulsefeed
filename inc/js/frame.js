/*
	file: inc/js/frame.js
	desc: frame functions
*/

//bust the frame busters (evil?)
var prevent_bust = 0;
window.onbeforeunload = function() {
	//prevent_bust++;
}
setInterval( function() {
	if( prevent_bust > 0 ) {
		prevent_bust -= 2;
		window.top.location = mod_root + '?load=204';
	}
}, 1 );

//free our links from the grips of the buster!
$( document ).ready( function() {
	$( '#top a' ).bind( 'click', function( el ) {
		prevent_bust = -10;
	});
	$( '#top form' ).bind( 'submit', function( el ) {
		prevent_bust = -10;
	});
});