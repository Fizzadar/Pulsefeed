/*
	file: inc/js/frame.js
	desc: frame functions
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
	$( '#top a' ).bind( 'click', function( el ) {
		prevent_bust = -10;
	});
	$( '#top form' ).bind( 'submit', function( el ) {
		prevent_bust = -10;
	});

	//hide loader div
	$( 'iframe.externalarticle' ).bind( 'load', function( el ) {
		$( '.iframeborder #loader' ).slideUp();
		console.log( $( '.iframeborder #loader' ).css( 'display' ) );
	});
	//loading too long?
	setTimeout( function() {
		if( $( '.iframeborder #loader' ).css( 'display' ) != 'none' ) {
			$( '.iframeborder #loader .wrap' ).html( '<img src="' + mod_root + '/inc/img/icons/loader.gif" alt="" /> if this article is not loading properly, please <a target="_blank" href="' + pf_frameurl + '" onclick="prevent_bust = -10;">click here to open it in another window &rarr;</a>' );
		}
	}, 3000 );
});