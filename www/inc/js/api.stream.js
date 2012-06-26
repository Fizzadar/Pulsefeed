/*
	file: inc/js/api.stream.js
	desc: stream api functions
*/

//store our collections
api.collections = new Array();
api.collectionId = 0;
api.collectionArticleId = 0;

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
				$( 'input[type=submit]', el ).addClass( 'red' );
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
				$( 'input[type=submit]', el ).removeClass( 'red' );
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
api.subscribeWebsite = function( el ) {
	//get data from dom
	var website_id = $( 'input[name=website_id]', el ).attr( 'value' );
	var action = $( el ).attr( 'action' );

	//disable the button while we work
	$( 'input[type=submit]', el ).attr( 'disabled', 'disabled' );
	$( 'input[type=submit]', el ).addClass( 'disabled' );

	//do we like or unlike?
	if( action == mod_root + '/process/website-subscribe' ) {
		this.post(
			'/process/website-subscribe',
			{ website_id: website_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', 'Unsubscribe' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'green' );
				$( 'input[type=submit]', el ).addClass( 'red' );
				$( el ).attr( 'action', mod_root + '/process/website-unsubscribe' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	} else {
		this.post(
			'/process/website-unsubscribe',
			{ website_id: website_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', '+ Subscribe' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'red' );
				$( 'input[type=submit]', el ).addClass( 'green' );
				$( el ).attr( 'action', mod_root + '/process/website-subscribe' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	}
}

//stream subscribe/unsubscribe topic
api.subscribeTopic = function( el ) {
	//get data from dom
	var topic_id = $( 'input[name=topic_id]', el ).attr( 'value' );
	var action = $( el ).attr( 'action' );

	//disable the button while we work
	$( 'input[type=submit]', el ).attr( 'disabled', 'disabled' );
	$( 'input[type=submit]', el ).addClass( 'disabled' );

	//do we like or unlike?
	if( action == mod_root + '/process/topic-subscribe' ) {
		this.post(
			'/process/topic-subscribe',
			{ topic_id: topic_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', 'Unsubscribe' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'green' );
				$( 'input[type=submit]', el ).addClass( 'red' );
				$( el ).attr( 'action', mod_root + '/process/topic-unsubscribe' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	} else {
		this.post(
			'/process/topic-unsubscribe',
			{ topic_id: topic_id },
			function( data, el ) {
				$( 'input[type=submit]', el ).attr( 'value', '+ Subscribe' );
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'red' );
				$( 'input[type=submit]', el ).addClass( 'green' );
				$( el ).attr( 'action', mod_root + '/process/topic-subscribe' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	}
}

//collect articles (show list of collections)
api.collect = function( el, noloop ) {
	//return here if re-clicking the active one
	if( $( 'ul.collections', $( el ).parent() ).length > 0 ) {
		$( '.item .meta .collect_button' ).removeClass( 'active' );
		$( '.item .meta form.share_form input[type=submit]' ).removeClass( 'active' );
		$( '.item .meta ul.collections' ).remove();
		$( '.item .meta ul.shares' ).remove();
		return;
	}

	//remove any open uls & active buttons
	$( '.item .meta ul.collections' ).remove();
	$( '.item .meta ul.shares' ).remove();
	$( '.item .meta .collect_button' ).removeClass( 'active' );
	$( '.item .meta form.share_form input[type=submit]' ).removeClass( 'active' );

	//get id
	var id = $( el ).attr( 'data-articleid' );
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
				api.collect( el, true );
			},
			function( data, el ) {
				window.location = mod_root + '/article/' + this.collectionArticleId + '/collect';
			},
			el
		);
		return;
	}

	//set active to el
	$( el ).addClass( 'active' );

	//open collect ul
	$( el ).parent().append( '<ul class="collections"><span class="tip"></span></ul>' );

	//now we have collections, lets add the html
	var ul = $( 'ul.collections', $( el ).parent() );

	//add each collection
	for( var i = 0; i < this.collections.length; i++ ) {
		ul.append( '<li><a href="' + mod_root +'/article/' + id + '/collect" data-collectionid="' + this.collections[i].id + '" data-articleid="' + id + '" class="submit_collect">' + this.collections[i].name + '</a> <span class="edit inline">' + this.collections[i].articles + ' articles</span>' );
	}

	//add new collection id
	ul.append( '<li><form action="' + mod_root + '/article/' + id + '/collect" class="submit_collect" data-articleid="' + id + '" data-collectionid="0"><input class="meta" type="text" value="new collection..." onclick="if( this.value == \'new collection...\' ) { this.value = \'\'; }" onblur="if( this.value == \'\' ) { this.value = \'new collection...\'; }" /></form></li>' );

	//now we have a height, and since this is js-only, lets move the box up
	ul.css( { marginTop: -1 * ( ul.height() + 30 ) } );
	$( '.tip', ul ).css( { marginTop: ul.height() + 5 } );

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
	$( '.item .meta ul.collections' ).bind( 'click', function( ev ) {
		ev.stopPropagation();
	});
}

//actually do the collect
api.collectArticle = function( el ) {
	//get article_id
	var art_id = $( el ).attr( 'data-articleid' );
	//get collection id
	var col_id = $( el ).attr( 'data-collectionid' );
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
			//set span text
			var span = $( 'span', $( el ).parent() );
			var ul = $( el ).parent().parent();

			span.html( 'added' );

			if( $( el ).attr( 'data-collectionid' ) > 0 ) {
				$( el ).addClass( 'disabled' );
			} else {
				$( el ).parent().before( '<li class="new_collection" style="display:none;">' + $( 'input[type=text]', el ).attr( 'value' ) + ' <span class="edit inline">1 article</span></li>' );
				$( 'input[type=text]', el ).attr( 'value', '' );
				$( 'li.new_collection', ul ).slideDown( 300 );
				ul.animate( { marginTop: -1 * ( ul.height() + 52 ) }, 300 );
				$( '.tip', ul ).animate( { marginTop: ul.height() + 28 }, 300 );
			}

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



//remove/uncollect an article
api.uncollect = function( el ) {
	//get data from dom
	var article_id = $( 'input[name=article_id]', el ).attr( 'value' );
	var collection_id = $( 'input[name=collection_id]', el ).attr( 'value' );

	//disable the button while we work
	$( 'input[type=submit]', el ).attr( 'disabled', 'disabled' );
	$( 'input[type=submit]', el ).addClass( 'disabled' );

	//make our request
	this.post(
		'/process/article-uncollect',
		{
			article_id: article_id,
			collection_id: collection_id
		},
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



//share an article
api.share = function( el ) {
	//return here if re-clicking the active one
	if( $( '.shares', $( el ).parent() ).length > 0 ) {
		$( '.item .meta form.share_form input[type=submit]' ).removeClass( 'active' );
		$( '.item .meta form.share_form input[type=submit]' ).removeClass( 'disabled' );
		$( '.item .meta .collect_button' ).removeClass( 'active' );
		$( '.item .meta ul.shares' ).remove();
		$( '.item .meta ul.collections' ).remove();
		return;
	}

	//remove any open uls & active buttons
	$( '.item .meta ul.shares' ).remove();
	$( '.item .meta ul.collections' ).remove();
	$( '.item .meta form.share_form input[type=submit]' ).removeClass( 'active' );
	$( '.item .meta .collect_button' ).removeClass( 'active' );

	//get id
	var id = $( 'input[name=article_id]', el ).attr( 'value' );
	var fb_shares = $( 'input[name=facebook_shares]', el ).attr( 'value' );
	var tw_links = $( 'input[name=twitter_links]', el ).attr( 'value' );

	//set active to el
	$( 'input[type=submit]', $( el ) ).addClass( 'active' );
	$( 'input[type=submit]', $( el ) ).removeClass( 'disabled' );

	//open collect ul
	$( 'span.share', el ).append( '<ul class="shares"></ul>' );

	//now we have collections, lets add the html
	var ul = $( '.shares', $( el ).parent() );

	//get bits
	var title = $( 'input[name=article_title]', el ).attr( 'value' );
	var url = $( 'input[name=article_url]', el ).attr( 'value' );

	//append some shizzle
	//twitter
	ul.append( '<li><span>' + tw_links + '</span> <a href="javascript:var w=window.open( \'https://twitter.com/share?via=pulsefeed&url=' + url + '&text=' + title + '\', \'test\', \'menubar=0,resizable=0,width=700,height=300\' );w.moveTo($(window).width()/2-350,$(window).height()/2-150);" target="_blank" class="button twitter">+ Twitter</a></li>' );

	//facebook
	ul.append( '<li><span>' + fb_shares + '</span> <a href="javascript:var w=window.open( \'http://www.facebook.com/sharer.php?u=' + url + '&t=' + title + '\', \'test\', \'menubar=0,resizable=0,width=700,height=300\' );w.moveTo($(window).width()/2-350,$(window).height()/2-150);" target="_blank" class="button facebook">+ Facebook</a></li>' );

	//tip
	ul.append( '<li class="form"><input type="checkbox" name="share_to_followers" id="share_to_followers" /><label for="share_to_followers">share to followers</label></li><span class="tip"></span>' );

	//now we attempt to share article (doesnt matter if we fail)
	this.post(
		'/process/article-share',
		{
			article_id: id
		},
		function( data, el ) {
			//success!
			$( el ).attr( 'checked', 'checked' );
		},
		function( data, el ) {
			//failure!
		},
		$( 'ul.shares #share_to_followers' )
	);

	//bind unshare
	$( 'ul.shares #share_to_followers' ).bind( 'click', function( ev ) {
		api.unshare( ev.target );
		ev.preventDefault();
	});

	//stop hiding on clicks
	$( 'ul.shares' ).bind( 'click', function( ev ) {
		ev.stopPropagation();
	});
}



//unshare article
api.unshare = function( el ) {
	//disable
	$( el ).addClass( 'disabled' );

	//get post id
	var id = $( 'input[name=article_id]', $( el ).parent().parent().parent().parent() ).attr( 'value' );

	this.post(
		'/process/article-' + ( !$( el ).attr( 'checked' ) ? 'un' : '' ) + 'share',
		{
			article_id: id
		},
		function( data, el ) {
			//default action
			!$( el ).attr( 'checked' ) ? $( el ).attr( 'checked', 'checked' ) : $( el ).removeAttr( 'checked' );
			$( el ).removeClass( 'disabled' );
		},
		function( data, el ) {
			//default action
		},
		el
	);
}



//option: toggle images
api.imageToggle = function( el ) {
	if( cookie.get( 'hide_images' ) ) {
		//delete cookie
		cookie.delete( 'hide_images' );
		//set button
		$( el ).html( 'Images: on<span>hide images in the stream<span></span></span>' );
		$( el ).addClass( 'green' ).removeClass( 'red' );
		//fade in images
		$( '.col1 .item img.thumb' ).fadeIn();
		$( '.col2 .item img.thumb' ).fadeIn();
		if( $( '#stream' ).hasClass( 'evencol' ) ) {
			$( '.col3 .item img.thumb' ).fadeIn();
		}
	} else {
		//set cookie
		cookie.set( 'hide_images', true );
		//set button
		$( el ).html( 'Images: off<span>show images in the stream<span></span></span>' );
		$( el ).addClass( 'red' ).removeClass( 'green' );
		//fade out images
		$( '.item img.thumb' ).fadeOut();
	}
}



//option: toggle columns
api.columnToggle = function( el ) {
	if( cookie.get( 'two_col' ) ) {
		//delete cookie
		cookie.delete( 'two_col' );
		//set button
		$( el ).html( 'Columns: 3<span>switch between 2 &amp; 3 columns<span></span></span>' );
		//set two col
		$( '#stream' ).removeClass( 'twocol' );
	} else {
		//set cookie
		cookie.set( 'two_col', true );
		//set button
		$( el ).html( 'Columns: 2<span>switch between 2 &amp; 3 columns<span></span></span>' );
		//set two col
		$( '#stream' ).addClass( 'twocol' );
	}
}



//option: toggle message
api.messageToggle = function( el ) {
	if( cookie.get( 'hide_message' ) ) {
		//delete cookie
		cookie.delete( 'hide_message' );
		//set button
		$( el ).html( 'Hide Message<span>hide the login message<span></span></span>' );
		$( el ).addClass( 'red' ).removeClass( 'green' );
		//show message
		$( '.welcome' ).slideDown();
	} else {
		//set cookie
		cookie.set( 'hide_message', true );
		//set button
		$( el ).html( 'Show Message<span>show the login message<span></span></span>' );
		$( el ).addClass( 'green' ).removeClass( 'red' );
		//hide message
		$( '.welcome ' ).slideUp();
	}
}