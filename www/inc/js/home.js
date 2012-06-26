/*
	file: inc/js/home.js
	desc: arrows on homepage
*/
var home_pos = 0;

//window & jquery ready
window.onload = function() {
	$( document ).ready( function() {
		$( 'a.home_arrow_right' ).bind( 'click', function( ev ) {
			ev.preventDefault();
			home_pos++;
			move_home_bg();
			$( 'a.home_arrow_right' ).addClass( 'hidden' );
			$( 'a.home_arrow_left' ).removeClass( 'hidden' );
		});
		$( 'a.home_arrow_left' ).bind( 'click', function( ev ) {
			ev.preventDefault();
			home_pos--;
			move_home_bg();
			$( 'a.home_arrow_left' ).addClass( 'hidden' );
			$( 'a.home_arrow_right' ).removeClass( 'hidden' );
		});
	});
}

var move_home_bg = function() {
	$( 'div.home_bg' ).animate( { scrollLeft: $( window ).width() * home_pos }, 300 );
}