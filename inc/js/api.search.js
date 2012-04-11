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
			//remove any 'current' results (even if getting 'more', clear the current bunch)
			$( '#search_results' ).html( '' );

			//loop each type (if have results)
			if( data.sources.length > 0 ) {
				$( '#search_results' ).append( '<li class="title">Sources</li>' );

				for( var i = 0; i < data.sources.length; i++ ) {
					$( '#search_results' ).append( '<li class="search_source"><a href="' + mod_root + '/source/' + data.sources[i].id + '"><span class="title"><img src="http://favicon.fdev.in/' + data.sources[i].domain + '"/> ' + data.sources[i].title + '</span><span class="type">source / ' + data.sources[i].url + '</span></a></li>' );
				}
			}

			if( data.users.length > 0 ) {
				$( '#search_results' ).append( '<li class="title">Users</li>' );

				for( var i = 0; i < data.users.length; i++ ) {
					$( '#search_results' ).append( '<li class="search_user"><a href="' + mod_root + '/source/' + data.users[i].id + '"><span class="title">' + ( data.users[i].avatar.length > 0 ? '<img src="' + data.users[i].avatar + '"/>' : '' ) + data.users[i].title + '</span><span class="type">user</span></a></li>' );
				}
			}

			if( data.articles.length > 0 ) {
				$( '#search_results' ).append( '<li class="title">Articles</li>' );

				for( var i = 0; i < data.articles.length; i++ ) {
					$( '#search_results' ).append( '<li class="search_article"><a href="' + mod_root + '/source/' + data.articles[i].id + '"><span class="title">' + ( data.articles[i].source ? '<img src="http://favicon.fdev.in/' + data.articles[i].source.domain + '"/>' : '' ) + data.articles[i].title + '</span><span class="type">article' + ( data.articles[i].source ? ' from ' + data.articles[i].source.title : '' ) + '</span></a></li>' );
				}
			}

			//no results?
			resultslength = data.sources.length + data.users.length + data.articles.length;
			if( resultslength <= 0 ) {
				$( '#search_results' ).append( '<li class="title">We couldn\'t find anything :(</li>' );
			} else {
				$( '#search_results' ).append( '<li class="more"><a href="#" class="load_more_search"><strong>load more results &darr;</strong></a></li>' );
				//bind more button
				$( '.load_more_search' ).bind( 'click', function( ev ) {
					ev.preventDefault();
					api.search( el, data.nextOffset );
				})
			}

			//display
			$( 'input[type=submit]', el ).css( 'background-image', 'url( ' + mod_root + '/inc/img/icons/search.png )' );
			$( '#search_results' ).css( 'display', 'block' );
			$( '#search_results' ).scrollTop( 0 );
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
	//$( '#search_results' ).html( '' );

	//start page scroll
	$( 'body' ).css( 'overflow', 'auto' );
}