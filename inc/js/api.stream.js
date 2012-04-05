/*
	file: inc/js/api.stream.js
	desc: stream api functions
*/

//store our collections
api.collections = new Array();
api.collectionId = 0;
api.collectionArticleId = 0;

//stream like/unlike
api.like = function( el ) {
	//get data from dom
	var article_id = $( 'input[name=article_id]', el ).attr( 'value' );
	var action = $( el ).attr( 'action' );

	//disable the button while we work
	$( 'input[type=submit]', el ).attr( 'disabled', 'disabled' );
	$( 'input[type=submit]', el ).addClass( 'disabled' );

	//do we like or unlike?
	if( action == mod_root + '/process/article-like' ) {
		this.post(
			'/process/article-like',
			{ article_id: article_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', 'Unlike' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'span.likes span', el ).html( parseInt( $( 'span.likes span', el ).html() ) + 1 );
				$( el ).attr( 'action', mod_root + '/process/article-unlike' );
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
				$( 'input[type=submit]', el ).attr( 'value', 'Like' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'span.likes span', el ).html( parseInt( $( 'span.likes span', el ).html() ) - 1 );
				$( el ).attr( 'action', mod_root + '/process/article-like' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	}
}

//stream read article
api.read = function( el ) {
	//get data from dom
	var article_id = $( 'input[name=article_id]', el ).attr( 'value' );

	//disable the button while we work
	$( 'input[type=submit]', el ).attr( 'disabled', 'disabled' );
	$( 'input[type=submit]', el ).addClass( 'disabled' );

	//make our request
	this.post(
		'/process/article-hide',
		{ article_id: article_id },
		function( data, el ) {
			var article_id = $( 'input[name=article_id]', el ).attr( 'value' );
			$( '#article_' + article_id ).animate( { height: 'toggle' }, 150, function() {
				$( this ).remove();
			});
		},
		function( data, el ) {
			$( 'input[type=submit]', el ).removeAttr( 'disabled' );
			$( 'input[type=submit]', el ).removeClass( 'disabled' );
		},
		el
	);
}

//read whole topics
api.readTopic = function( el ) {
	//hide
	$( el ).parent().parent().slideUp();
	
	var forms = $( el ).parent().parent().find( 'form.hide_form' );

	//remove each item
	for( var i = 0; i < forms.length; i++ ) {
		queue.add( function( args ) {
			$( args.el ).submit();
		}, 2000, { el: forms[i] } );
	}
}

//stream follow/unfollow user
api.follow = function( el ) {
	//get data from dom
	var user_id = $( 'input[name=user_id]', el ).attr( 'value' );
	var action = $( el ).attr( 'action' );

	//disable the button while we work
	$( 'input[type=submit]', el ).attr( 'disabled', 'disabled' );
	$( 'input[type=submit]', el ).addClass( 'disabled' );

	//do we follow or unfollow?
	if( action == mod_root + '/process/follow' ) {
		this.post(
			'/process/follow',
			{ user_id: user_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', 'Unfollow' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'green' );
				$( el ).attr( 'action', mod_root + '/process/unfollow' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	} else {
		this.post(
			'/process/unfollow',
			{ user_id: user_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', '+ Follow' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).addClass( 'green' );
				$( el ).attr( 'action', mod_root + '/process/follow' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	}
}

//stream subscribe/unsubscribe source
api.subscribe = function( el ) {
	//get data from dom
	var source_id = $( 'input[name=source_id]', el ).attr( 'value' );
	var action = $( el ).attr( 'action' );

	//disable the button while we work
	$( 'input[type=submit]', el ).attr( 'disabled', 'disabled' );
	$( 'input[type=submit]', el ).addClass( 'disabled' );

	//do we like or unlike?
	if( action == mod_root + '/process/subscribe' ) {
		this.post(
			'/process/subscribe',
			{ source_id: source_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', 'Unsubscribe' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'green' );
				$( el ).attr( 'action', mod_root + '/process/unsubscribe' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	} else {
		this.post(
			'/process/unsubscribe',
			{ source_id: source_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', '+ Subscribe' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).addClass( 'green' );
				$( el ).attr( 'action', mod_root + '/process/subscribe' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	}
}

//collect articles
api.collect = function( el ) {
	//return here if re-clicking the active one
	if( $( '.collections', $( el ).parent() ).length > 0 )
		return $( '.item .meta ul.collections' ).remove();

	//remove any open uls
	$( '.item .meta ul.collections' ).remove();

	//get id
	var id = $( el ).attr( 'articleID' );

	//do we need to get our collections?
	if( this.collections.length <= 0 ) {
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
				api.collect( el );
			},
			function( data, el ) {
				window.location = mod_root + '/article/' + id + '/collect';
			},
			el
		);
		return;
	}

	//open collect ul
	$( el ).parent().append( '<ul class="collections"><span class="tip"></span></ul>' );

	//now we have collections, lets add the html
	var ul = $( '.collections', $( el ).parent() );

	//add each collection
	for( var i = 0; i < this.collections.length; i++ ) {
		ul.append( '<li><a href="#" collectionID="' + this.collections[i].id + '" articleID="' + id + '" class="submit_collect">' + this.collections[i].name + '</a> <span class="edit inline">' + this.collections[i].articles + ' articles</span>' );
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

//actually do the collect
api.collectArticle = function( el ) {
	//get article_id
	var art_id = $( el ).attr( 'articleID' );
	//get collection id
	var col_id = $( el ).attr( 'collectionID' );
	//name
	var name = '';

	//collection id = 0
	if( col_id == 0 ) {
		var name = $( 'input[type=text]', el ).attr( 'value' );
		
		//no name? just return
		if( name.length <= 0 ){
			return;
		}
	}

	//save the name and article id
	this.collectionId = col_id;
	this.collectionArticleId = art_id;

	//make our request
	this.post(
		'/process/article-collect',
		{
			collection_name: name,
			collection_id: col_id,
			article_id: art_id
		},
		function( data, el ) {
			//remove the divs
			$( '.item .meta ul.collections' ).remove();

			//if collection id was 0, force reload of list
			if( api.collectionId == 0 ) {
				api.collections = new Array();
			}
		},
		function( data, el ) {
			//something went wrong!
			window.location = mod_root + '/article/' + api.collectionArticleId + '/collect';
		},
		el
	);
}