/*
	file: inc/js/api.frame.js
	desc: article page (frame) api functions
*/

//article collect
api.collectExternal = function( el, noloop ) {
	//return here if re-clicking the active one
	if( $( '.collections', $( el ).parent() ).length > 0 ) {
		$( el ).removeClass( 'active' );
		return $( 'ul.collections' ).remove();
	}

	//get id
	var id = $( '.collect_button_external' ).attr( 'data-articleid' );

	//save it
	this.collectionArticleId = id;

	//do we need to get our collections?
	if( this.collections.length <= 0 && !noloop ) {
		//disable button
		$( el ).addClass( 'disabled' );

		//load collections & return here
		this.get(
			'/article/' + id + '/collect',
			{},
			function( data, el ) {
				//remove disabled
				$( el ).removeClass( 'disabled' );

				//save collections
				api.collections = data.collections;

				//and reload this function
				api.collectExternal( el, true );
			},
			function( data, el ) {
				console.log( $( el ).attr( 'href' ) );
			},
			el
		);
		return;
	}

	//set active to el
	$( el ).addClass( 'active' );

	//open collect ul
	$( el ).parent().prepend( '<ul class="collections"><span class="tip"></span></ul>' );

	//now we have collections, lets add the html
	var ul = $( '.collections', $( el ).parent() );

	//add each collection
	for( var i = 0; i < this.collections.length; i++ ) {
		ul.append( '<li><a href="' + mod_root +'/article/' + id + '/collect" data-collectionid="' + this.collections[i].id + '" data-articleid="' + id + '" class="submit_collect">' + this.collections[i].name + ' <span class="edit inline">' + this.collections[i].articles + ' articles</span></a>' );
	}

	//add new collection id
	ul.append( '<li><form action="' + mod_root + '/article/' + id + '/collect" class="submit_collect" data-articleid="' + id + '" data-collectionid="0"><input type="text" value="new collection..." onclick="if( this.value == \'new collection...\' ) { this.value = \'\'; }" onblur="if( this.value == \'\' ) { this.value = \'new collection...\'; }" /></form></li>' );

	//bind the links
	$( 'a.submit_collect' ).bind( 'click', function( ev ) {
		ev.preventDefault();
		api.collectArticle( ev.target );
	});
	//and the form
	$( 'form.submit_collect' ).bind( 'submit', function( ev ) {
		ev.preventDefault();
		api.collectArticle( ev.target );
	});

	//bind the whole thing
	$( 'ul#external ul.collections' ).bind( 'click', function( ev ) {
		ev.stopPropagation();
	});
}


//share article
api.shareExternal = function( el ) {
	console.log( el );
}