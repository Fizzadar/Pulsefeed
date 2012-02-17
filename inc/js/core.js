//make our message auto slide up
$( document ).ready( function() {
	$( 'div.message' ).click( function( el ) {
		$( this ).slideUp( 200 );
	});
	function messageslide() {
		$( 'div.message' ).slideUp( 200 );
	}
	setTimeout( messageslide, 2000 );
});