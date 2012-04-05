/*
	file: inc/js/api.search.js
	desc: api search
*/
api.searchTerm = '';
api.searchActive = 0;

//search function
api.search = function( el, off ) {
	//search is active
	api.searchActive = 1;
	
	//get search string
	var query = $( 'input[type=text]', el ).attr( 'value' );
	api.searchTerm = query;

	//make submit have load icon
	$( 'input[type=submit]', el ).css( 'background-image', 'url( ' + mod_root + '/inc/img/icons/loader.gif )' );
	//focus on input
	$( 'input[type=text]', el ).focus();
	//stop page scroll
	$( 'body' ).css( 'overflow', 'hidden' );
	
	//make our request (url, data, success, failure, element)
	api.get(
		'/search',
		{
			q: query,
			offset: off
		},
		function( data, el ) {
			//remove any 'current' results if offset =1
			if( data.nextOffset == 1 ) {
				$( '#search_results' ).html( '' );
			}

			//build results
			for( var i = 0; i < data.results.length; i++ ) {
				$( '#search_results' ).append( '<li><a href="' + mod_root + '/' + data.results[i].type + '/' + data.results[i].id + '"><span class="title">' + data.results[i].title + '</span><span class="type">' + data.results[i].type + '</span></a></li>' );
			}

			//no results?
			if( data.results.length <= 0 ) {
				$( '#search_results' ).append( '<li class="more">Nothing could be found</li>' );
			} else {
				$( '#search_results' ).append( '<li class="more"><a class="load_more_search" href="#"><strong>Load more &#187;</strong></a></li>' );
				//bind more button
				$( '.load_more_search' ).bind( 'click', function( ev ) {
					ev.preventDefault();
					$( ev.target ).parent().html( '<strong>More results:</strong>' );
					api.search( el, data.nextOffset );
				})
			}

			//display
			$( 'input[type=submit]', el ).css( 'background-image', 'url( ' + mod_root + '/inc/img/icons/search.png )' );
			$( '#search_results' ).css( 'display', 'block' );
			if( data.nextOffset == 1 ) {
				$( '#search_results' ).scrollTop( 0 );
			} else {
				$( '#search_results' ).scrollTop( $( '#search_results' ).scrollTop() + 50 );
			}
		},
		function( data, el ) {
			//uh oh! redirect to default search
			window.location = mod_root + '/search?q=' + api.searchTerm;
		},
		el
	);
}

//hide search
api.hideSearch = function() {
	if( api.searchActive )
		return;

	//remove & hide
	$( '#search_results' ).css( 'display', 'none' );
	$( '#search_results' ).html( '' );

	//start page scroll
	$( 'body' ).css( 'overflow', 'auto' );
}