/*
	file: inc/js/frame.js
	desc: frame functions (aka buster - debatably 'evil' but we're not doing anything bad, X-Frame-Origin is for true busters)
*/

//bust the frame busters (evil?)
var prevent_bust = 0;
window.onbeforeunload = function() {
	prevent_bust++;
}
setInterval( function() {
	if( prevent_bust > 0 ) {
		prevent_bust -= 2;
		window.top.location = mod_root + '?load=204';
	}
}, 1 );

//free our links from the grips of the buster!
$( document ).ready( function() {
	//add iframe
	if( !pf_xframe )
		$( '.iframeborder' ).append( '<iframe class="externalarticle" src="' + pf_frameurl + '"></iframe>' );
	else
		$( '.iframeborder' ).append( '<iframe class="externalarticle" src="about:blank"></iframe>' );

	//bind links to allow local stuff
	$( '#top a' ).bind( 'click', function( el ) {
		prevent_bust = -10;
	});
	$( '#top form' ).bind( 'submit', function( el ) {
		prevent_bust = -10;
	});

	//hide loader icon
	$( 'iframe.externalarticle' ).bind( 'load', function( el ) {
		$( '#top .loading_icon' ).fadeOut();
	});
});