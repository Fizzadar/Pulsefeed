//prevent frame-busting
//slightly evil? pf topbar is only tiny, in this case user exp > "evil"
/*
var prevent_bust = 0;
$( document ).ready( function() {
	$( 'a' ).click( function( el ) {
		prevent_bust = -1;
	});
	$( 'form' ).submit( function( el ) {
		prevent_bust = -1;
	});
});
window.onbeforeunload = function() {
	prevent_bust++;
}
setInterval( function() {
	if( prevent_bust > 0 ) {
		prevent_bust -= 2;
		window.top.location = mod_root + '?load=204';
	}  
}, 1 );*/