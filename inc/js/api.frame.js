/*
	file: inc/js/api.frame.js
	desc: article page (frame) api functions
*/

//article like
api.likeExternal = function( el ) {
	//get data from dom
	var article_id = $( 'input[name=article_id]', el ).attr( 'value' );
	var text = $( 'button span', el ).html();

	//disable the button while we work
	$( 'button', el ).attr( 'disabled', 'disabled' );
	$( 'button', el ).addClass( 'disabled' );

	//do we like or unlike?
	if( text == 'Like' ) {
		this.post(
			'/process/article-like',
			{ article_id: article_id },
			function( data, el ) {
				$( 'button span' ).html( 'Unlike' );
				$( 'button img ' ).attr( 'src', mod_root + '/inc/img/icons/liked.png' );
				$( 'button', el ).removeAttr( 'disabled' );
				$( 'button', el ).removeClass( 'disabled' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	} else {
		this.post(
			'/process/article-unlike',
			{ article_id: article_id },
			function( data, el ) {
				$( 'button span' ).html( 'Like' );
				$( 'button img ' ).attr( 'src', mod_root + '/inc/img/icons/like.png' );
				$( 'button', el ).removeAttr( 'disabled' );
				$( 'button', el ).removeClass( 'disabled' );
			},
			function( data, el ) {
				$( 'button', el ).removeAttr( 'disabled' );
				$( 'button', el ).removeClass( 'disabled' );
			},
			el
		);
	}
}

//article collect
api.collectExternal = function( el, noloop ) {
	//return here if re-clicking the active one
	if( $( '.collections', $( el ).parent() ).length > 0 ) {
		$( el ).removeClass( 'active' );
		return $( 'ul.collections' ).remove();
	}

	//get id
	var id = $( el ).attr( 'articleID' );
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
		ul.append( '<li><a href="' + mod_root +'/article/' + id + '/collect" collectionID="' + this.collections[i].id + '" articleID="' + id + '" class="submit_collect">' + this.collections[i].name + ' <span class="edit inline">' + this.collections[i].articles + ' articles</span></a>' );
	}

	//add new collection id
	ul.append( '<li><form action="' + mod_root + '/article/' + id + '/collect" class="submit_collect" articleID="' + id + '" collectionID="0"><input type="text" value="new collection..." onclick="if( this.value == \'new collection...\' ) { this.value = \'\'; }" onblur="if( this.value == \'\' ) { this.value = \'new collection...\'; }" /></form></li>' );

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
}