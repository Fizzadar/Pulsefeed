/*
file: inc/js/pulsefeed.js
desc: pulsefeeds core js file
*/

//ie is shit!
if( !console ) {
console = {
log: function( text ) {}
}
}

//pf object (general data store)
var pulsefeed = {};
pulsefeed.stream = false;

//start pf
pulsefeed.start = function() {
console.log( '[Pulsefeed] Charging up...' );

//start stuff
message.start();
api.start( true );
design.start();
queue.start();

console.log( '[Pulsefeed] Charged.' );
}

//start pf using jquery
$( document ).ready( function() {
pulsefeed.start();
});/*
file: inc/js/message
desc: handle messages
*/
var message = {};

//start our messages
message.start = function() {
//hide them on click
$( 'div.message' ).click( function( el ) {
$( this ).slideUp( 200 );
});

//slide up after 2s
function messageslide() {
$( 'div.message' ).slideUp( 200 );
}
setTimeout( messageslide, 2000 );
};

//add message
message.add = function( message, type ) {
//add the message (normally warning)
$( '#messages' ).append( '<div class="message ' + type + '"><div class="wrap">' + message + '<span class="right">hide message</span></div></div>' );

//hide after 2 seconds
this.start();
}/*
file: inc/js/api.js
desc: core api functions
*/
var api = {};

//api startup
api.start = function( full ) {
//bind like buttons
$( '.like_form' ).unbind().bind( 'submit', function( ev ) {
ev.preventDefault();
api.like( ev.target );
});
//bind hide buttons
$( '.hide_form' ).unbind().bind( 'submit', function( ev ) {
ev.preventDefault();
api.read( ev.target );
});
//bind subscribe buttons
$( '.source_subscribe' ).unbind().bind( 'submit', function( ev ) {
ev.preventDefault();
api.subscribe( ev.target );
});
//bind follow buttons
$( '.user_follow' ).unbind().bind( 'submit', function( ev ) {
ev.preventDefault();
api.follow( ev.target );
});
//bind collect buttons
$( '.collect_button' ).unbind().bind( 'click', function( ev ) {
ev.preventDefault();
api.collect( ev.target );
});
//bind hide buttons
$( '.uncollect_form' ).unbind().bind( 'submit', function( ev ) {
ev.preventDefault();
api.uncollect( ev.target );
});


//full load?
if( full ) {
//pf stream?
if( pulsefeed.stream ) {
$( '.stream_load_more' ).bind( 'click', function( ev ) {
ev.preventDefault();
api.loadStream( ev.target, false );
});
}
//load more sources
if( pulsefeed.sbrowser ) {
$( '.source_load_more' ).bind( 'click', function( ev ) {
ev.preventDefault();
api.loadSource( ev.target );
});
}
//bind search form
$( 'form#search' ).bind( 'submit', function( ev ) {
ev.preventDefault();
api.search( ev.target, 0 );
});
//hide on click out of search
$( 'form#search input' ).bind( 'blur', function( ev ) {
api.searchActive = 0;
setTimeout( 'api.hideSearch()', 150 ); //done in case searchActive swiched back immediately #hacky!
});
//external like
$( '.like_form_external' ).unbind().bind( 'submit', function( ev ) {
ev.preventDefault();
api.likeExternal( ev.target );
});
//external collect button
$( '.collect_button_external' ).unbind().bind( 'click', function( ev ) {
ev.preventDefault();
api.collectExternal( ev.target );
});
}
}

//request
api.request = function( type, url, data, success, failure, element ) {
//do request
$.ajax({
type: type,
url: mod_root + url + '?iapi',
data: data,
context: element,
dataType: 'json',
success: function( data ) {
if( !data || !data.mod_token || !data.result ) {
failure( data, this );
console.log( '[Pulsefeed] API Request Failed: invalid data returned!' );
} else {
mod_token = data.mod_token;
if( data.result == 'success' ) {
success( data, this );
console.log( '[Pulsefeed] API Request Success: ' + data.message );
} else {
failure( data, this );
console.log( '[Pulsefeed] API Request Failed: ' + data.message );
}
}
},
error: function( data, text, error ) {
failure( data, this );
console.log( '[Pulsefeed] API Request Failed Miserably: ' + text );
}
});

//log
console.log( '[Pulsefeed] API Request Fired: ' + url );
}

//make get request
api.get = function( url, data, success, failure, element ) {
//request
this.request( 'GET', url, data, success, failure, element );
}

//make post request
api.post = function( url, data, success, failure, element ) {
//add mod token
data.mod_token = mod_token;
//request
this.request( 'POST', url, data, success, failure, element );
}/*
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
$( 'input[type=submit]', el ).addClass( 'red' );
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
$( 'input[type=submit]', el ).removeClass( 'red' );
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

//collect articles (show list of collections)
api.collect = function( el, noloop ) {
//return here if re-clicking the active one
if( $( '.collections', $( el ).parent() ).length > 0 ) {
$( '.item .meta .collect_button' ).removeClass( 'active' );
return $( '.item .meta ul.collections' ).remove();
}

//remove any open uls & active buttons
$( '.item .meta ul.collections' ).remove();
$( '.item .meta .collect_button' ).removeClass( 'active' );

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
var ul = $( '.collections', $( el ).parent() );

//add each collection
for( var i = 0; i < this.collections.length; i++ ) {
ul.append( '<li><a href="' + mod_root +'/article/' + id + '/collect" collectionID="' + this.collections[i].id + '" articleID="' + id + '" class="submit_collect">' + this.collections[i].name + '</a> <span class="edit inline">' + this.collections[i].articles + ' articles</span>' );
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
//remove the divs on stream
$( '.item .meta ul.collections' ).remove();
$( '.item .meta a.collect_button' ).removeClass( 'active' );
//remove the div on external
$( 'ul#external ul.collections' ).remove();
$( 'ul#external a.collect_button_external' ).removeClass( 'active' );

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
}/*
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
$( '#search_results' ).append( '<li class="search_user"><a href="' + mod_root + '/user/' + data.users[i].id + '"><span class="title">' + ( data.users[i].avatar.length > 0 ? '<img src="' + data.users[i].avatar + '"/>' : '' ) + data.users[i].title + '</span><span class="type">user</span></a></li>' );
}
}

if( data.articles.length > 0 ) {
$( '#search_results' ).append( '<li class="title">Articles</li>' );

for( var i = 0; i < data.articles.length; i++ ) {
$( '#search_results' ).append( '<li class="search_article"><a href="' + mod_root + '/article/' + data.articles[i].id + '"><span class="title">' + ( data.articles[i].source ? '<img src="http://favicon.fdev.in/' + data.articles[i].source.domain + '"/>' : '' ) + data.articles[i].title + '</span><span class="type">article' + ( data.articles[i].source ? ' from ' + data.articles[i].source.title : '' ) + '</span></a></li>' );
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
}/*
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
var id = pulsefeed.article_id;
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
}/*
file: inc/js/api.page.js
desc: extra page data (offsets)
*/

//load more sources
api.loadSource = function( el ) {
$( el ).html( 'loading <img src="' + mod_root + '/inc/img/icons/loader.gif" alt="" />' );

//make our request
this.get(
'/sources/' + pulsefeed.sourceType,
{ offset: pulsefeed.sourceOffset },
//success, lets distribute those articles
function( data, el ) {
console.log( data );
if( data.result == 'success' ) {
if( data.sources == null || data.sources.length == 0 ) {
$( el ).html( 'no more sources :(' );
$( el ).removeClass( 'source_load_more' );
$( el ).unbind( 'click' );
$( el ).bind( 'click', function( ev ) {
ev.preventDefault();
});
$( el ).addClass( 'disabled' );
return;
} else {
//loop sources, add to div
for( var i = 0; i < data.sources.length; i++ ) {
$( '#sources' ).append( template.source( data.sources[i] ) );
//fade in
queue.add( function( args ) {
$( '#source_' + args.id ).animate( { opacity: 1 }, 250 );
}, 150, { id: data.sources[i].id } );
}
}

//up offset
pulsefeed.sourceOffset++;
//reload links
api.start( false );
//loading text
$( el ).html( 'load more sources &darr;' );
} else {
window.location = mod_root + '/sources/' + pulsefeed.sourceType + '?offset=' + pulsefeed.sourceOffset;
}
},
//failure!
function( data, el ) {
window.location = mod_root + '/sources/' + pulsefeed.sourceType + '?offset=' + pulsefeed.sourceOffset;
},
el
);
}

//work out stream api link
api.linkStream = function() {
switch( pulsefeed.streamType ) {
case 'public':
return  '/public';
case 'source':
return '/source/' + pulsefeed.streamSource;
case 'account':
return '/account/' + pulsefeed.streamAccount;
case 'collection':
return '/collection/' + pulsefeed.streamCollection;
default:
return '/user/' + pulsefeed.streamUser + '/' + pulsefeed.streamType;
}
}

//hide current stream
api.hideStream = function() {
$( '.col' ).animate( { height: 'toggle' }, 300, function() {
$( '.col' ).html( '' );
});
}

//show stream
api.showStream = function() {
$( '.col' ).css( { height: 'auto' } );
}

//load more stream
api.loadStream = function( el, reload ) {
$( el ).html( 'loading <img src="' + mod_root + '/inc/img/icons/loader.gif" alt="" />' );

if( reload )
this.hideStream();

//build the link
var link = this.linkStream();

//make our request
this.get(
link,
{ offset: pulsefeed.streamOffset },
//success, lets distribute those articles
function( data, el ) {
if( data.result == 'success' ) {
if( data.stream == null ) {
$( el ).html( 'no more articles :(' );
$( el ).removeClass( 'stream_load_more' );
$( el ).unbind( 'click' );
$( el ).bind( 'click', function( ev ) {
ev.preventDefault();
});
$( el ).addClass( 'disabled' );
return;
}

api.showStream();
//build & load
var stream = api.buildStream( data.stream );
api.renderStream( stream, data.recommends );
//increase our page offset
pulsefeed.streamOffset++;
//reload links
api.start( false );
//loading text
$( el ).html( 'load more articles &darr;' );
} else {
window.location = mod_root + api.linkStream() + '?offset=' + pulsefeed.streamOffset;
}
},
//failure!
function( data, el ) {
window.location = mod_root + api.linkStream() + '?offset=' + pulsefeed.streamOffset;
},
el
);
}

//render stream
api.renderStream = function( stream, recommends ) {
var length = 0;

//work out longest length
if( stream.col1.length > length )
length = stream.col1.length;
if( stream.col2.length > length )
length = stream.col2.length;
if( stream.col3.length > length )
length = stream.col3.length;

//now iterate
for( var i = 0; i < length; i++ ) {
//col 1
if( stream.col1[i] != undefined ) {
$( '.col1' ).append( template.item( stream.col1[i] ) );
//fade in
queue.add( function( args ) {
$( '#article_' + args.id ).animate( { opacity: 1 }, 250 );
}, 100, { id: stream.col1[i].id } );
}
//col 2
if( stream.col2[i] != undefined ) {
$( '.col2' ).append( template.item( stream.col2[i] ) );
//fade in
queue.add( function( args ) {
$( '#article_' + args.id ).animate( { opacity: 1 }, 250 );
}, 100, { id: stream.col2[i].id } );
}
//col 3
if( stream.col3[i] != undefined ) {
switch( pulsefeed.streamType ) {
//2 col main, 1 col upcoming
case 'hybrid':
case 'popular':
case 'public':
$( '.col3' ).append( template.item( stream.col3[i], true, 'h4' ) );
break;
default:
$( '.col3' ).append( template.item( stream.col3[i] ) );
}

//fade in
queue.add( function( args ) {
$( '#article_' + args.id ).animate( { opacity: 1 }, 250 );
}, 100, { id: stream.col3[i].id } );
}
}
}

//build stream
api.buildStream = function( items ) {
var cols = new Array();
cols[1] = new Array();
cols[2] = new Array();
cols[3] = new Array();

switch( pulsefeed.streamType ) {
//2 col main, 1 col upcoming
case 'hybrid':
case 'popular':
case 'public':
if( items.length > 2 ) {
//get 1/3 length
var length = items.length;
var third = Math.round( length / 2.4 );

//get col3, items from length - third to length
for( var i = length - third; i < length; i++ ) {
items[i].short_description = items[i].shorter_description;
cols[3][cols[3].length] = items[i];
}

//now generate other 2 cols
var iscol2 = false;
for( var i = 0; i < length - third; i++ ) {
//choose the col
if( iscol2 ) {
cols[2][cols[2].length] = items[i];
} else {
cols[1][cols[1].length] = items[i];
}

//switch
iscol2 = !iscol2;
}
break;
}

//3 col even
default:
var col = 1;

//add each item
for( var i = 0; i < items.length; i++ ) {
cols[col][cols[col].length] = items[i];
col++;
if( col > 3 )
col = 1;
}
}

//return
var r = {};
r.col1 = cols[1];
r.col2 = cols[2];
r.col3 = cols[3];
return r;
}/*
file: inc/js/template.js
desc: template functions
*/
var template = {};

//stream item template
template.item = function( item, no_image, header ) {
var long = true;

//source or origin ref
var source = false;
var orig = false;
for( var i = 0; i < item.refs.length; i++ ) {
if( item.refs[i]['source_type'] == 'source' ) {
source = true;
}
}

//build string
var r = '<div class="item" id="article_' + item.id + '" style="opacity:0;">';
r += header ? '<' + header + '>' : '<h3>';
r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">' + item.title + '</a>';
r += header ? '</' + header + '>' : '</h3>';

//image?
if( item.image_half != '' && !no_image ) {
long = false;
r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">';
r += '<img class="thumb" src="' + mod_root + '/' + item.image_half + '" alt="' + item.title + '" />';
r += '</a>';
} else if( item.image_third != '' && !no_image ) {
long = false;
r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">';
r += '<img class="thumb" src="' + mod_root + '/' + item.image_third + '" alt="' + item.title + '" />';
r += '</a>';
}

//content
r += '<p>';
//long?
if( long ) {
r += item.short_description;
} else {
r += item.shorter_description;
}
r += ' <a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">';
switch( item.type ) {
case 'video':
r += '';
break;
default:
r += 'read article &rarr;';
}
r += '</a></p>';

//meta
r += '<div class="meta">';

//refs
for( var i = 0; i < item.refs.length; i++ ) {
r += '<a href="' + mod_root + '/';
switch( item.refs[i].source_type ) {
case 'source':
case 'public':
r += 'source/' + item.refs[i].source_id;
break;
case 'like':
r += 'user/' + item.refs[i].source_id;
break;
case 'facebook':
case 'twitter':
r += 'account/' + item.refs[i].source_type + '/' + item.refs[i].source_id;
break;
default:
r += '#';
}
r += '" class="tip hover">';
r += '<span>';
switch( item.refs[i].source_type ) {
case 'twitter':
case 'facebook':
r += '<img src="' + mod_root + '/inc/img/icons/share/' + item.refs[i].source_type + '.png" />';
}
r += '<strong>';
if( item.refs[i].source_type == 'twitter' ) {
r += '@';
}
r += item.refs[i].source_title + '</strong><small>';
switch( item.refs[i].source_type ) {
case 'public':
r += 'Public source';
break;
case 'source':
if( mod_userid == 0 ) {
r += 'Public Source';
} else if( pulsefeed.streamType == 'source' ) {
if( pulsefeed.streamSubscribed ) {
r += 'You are subscribed';
} else {
r += 'Not subscribed';
}
} else if( pulsefeed.streamType == 'collection' ) {
r += 'Original Source';
} else {
if( pulsefeed.streamUser == mod_userid ) {
r += 'You are subscribed';
} else {
r += pulsefeed.streamUsername + ' is subscribed';
}
}
break;
case 'facebook':
r += 'You are subscribed';
break;
case 'twitter':
case 'like':
if( pulsefeed.streamUser == mod_userid ) {
r += 'You follow them';
} else {
r += pulsefeed.streamUsername + ' follows them';
}
break;
default:
r += 'Unknown';
}
r += '</small><span></span></span>';
r += '<img src="';
switch( item.refs[i].source_type ) {
case 'public':
case 'source':
r += 'http://favicon.fdev.in/' + item.refs[i].source_data.domain;
break;
case 'twitter':
r += 'http://tweeter.fdev.in/' + item.refs[i].source_id;
break;
case 'facebook':
r += 'http://graph.facebook.com/' + item.refs[i].source_id + '/picture';
break;
case 'like':
r += mod_root + '/inc/img/icons/share/' + item.refs[i].source_type + '.png';
break;
default:
r += mod_root + '/inc/img/icons/sidebar/original.png';
}
r += '" alt="" /></a>';
if( !orig && !source && item.refs[i].origin_id && item.refs[i].origin_id > 0 && item.refs[i].origin_title != '' && item.refs[i].origin_data ) {
orig = true;
r += '<a href="' + mod_root + '/source/' + item.refs[i].origin_id + '" class="tip">';
r += '<span><strong>' + item.refs[i].origin_title + '</strong><small>Original source</small><span></span></span>';
r += '<img src="http://favicon.fdev.in/' + item.refs[i].origin_data.domain + '" alt="" /></a>';
}
}//end refs

//logged in?
if( mod_userid > 0 ) {
//hide button
if( item.unread && item.unread == 1 && mod_userid == pulsefeed.streamUser ) {
r += '<form action="' + mod_root + '/process/article-hide" method="post" class="hide_form">';
r += '<input type="hidden" name="article_id" value="' + item.id + '" />';
r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
r += '<input type="submit" value="Hide" />';
r += '</form> - ';
}

//collect
r += '<span class="collect"><a class="collect_button tip mini always" href="' + mod_root + '/article/' + item.id + '/collect" articleID="' + item.id + '">Collect</a></span> - ';

//like button
r += '<form action="' + mod_root + '/process/article-' + ( item.liked ? 'unrecommend' : 'recommend' ) + '" method="post" class="like_form">';
r += '<input type="hidden" name="article_id" value="' + item.id + '" />';
r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
r += '<input type="submit" value="' + ( item.liked ? 'Unlike' : 'Like' ) + '" /> <span class="likes">(<span>' + item.likes + '</span>)</span>';
r += '</form>';
}

r += '<span class="time"> - ' + item.time_ago + '</span>';

r += '</div>';
r += '</div><!--end item-->';

//return it
return r;
}

//source browse template
template.source = function( source ) {
//build string
var r = '<div class="source" id="source_' + source.id + '" style="opacity:0;">';

//header
r += '<h2><img src="http://favicon.fdev.in/' + source.site_domain + '" alt="" /> ';
r += '<a href="' + mod_root + '/source/' + source.id + '">' + source.site_title + '</a>';
r += '<span class="url"><a target="_blank" href="' + source.site_url + '">' + source.site_url_trim + '</a></span></h2>';

//meta
r += '<div class="meta">';
//thumbnail
r += '<a href="' + mod_root + '/source/' + source.id + '">';
r += '<img src="http://screenshots.fanaticaldev.com/?u=' + source.site_url + '" alt="" />';
r += '</a>';
r += '<span class="meta">Subscribers: <strong>' + source.subscribers + '</strong> - Last Updated: ' + source.time_ago + '</span>';
r += '</div>';

//articles
r += '<ul class="articles">';
for( var i = 0; i < source.articles.length; i++ ) {
r += '<li><a href="' + mod_root + '/article/' + source.articles[i].id + '">' + source.articles[i].title + ' &rarr;</a></li>';
}
if( source.articles.length <= 0 ) {
r += '<li>This source has no articles!</li>';
}
r += '</ul><!--end articles-->';

//logged in?
if( mod_userid > 0 ) {
r += '<form action="' + mod_root + '/process/' + ( source.subscribed ? 'unsubscribe' : 'subscribe' ) + '" method="post" class="source_subscribe">';
r += '<input type="hidden" name="source_id" value="' + source.id + '" />';
r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
r += '<input type="submit" value="' + ( source.subscribed ? 'Unsubscribe" class="button red"' : '+ Subscribe" class="green button"' ) + ' />';
r += '</form>';
}

r += '</div><!--end source-->';

//return
return r;
}/*
file: inc/js/design.js
desc: design commands
*/
var design = {};

//start
design.start = function() {
//top links
$( '.top_link' ).bind( 'click', function( ev ) {
ev.preventDefault();
design.scrollTo( 0 );
});

//article links
$( '.article_link' ).bind( 'click', function( ev ) {
localStorage.pulsefeedprevScroll = $( 'body' ).scrollTop();
});

//scroll if we must
if( pulsefeed.stream && localStorage.pulsefeedprevScroll > 0 ) {
this.scrollTo( localStorage.pulsefeedprevScroll - 200 );
localStorage.pulsefeedprevScroll = 0;
}

//make all buttons go to disabled on click
$( 'input[type=submit]' ).bind( 'click', function( ev ) {
$( ev.target ).addClass( 'disabled' );
});
}

//scroll to function
design.scrollTo = function( px ) {
//firefox likes this
$( 'html' ).animate( { scrollTop: px }, 300 );

//everything else likes this
$( 'body' ).animate( { scrollTop: px }, 300 );
}/*
file: inc/js/queue.js
desc: template functions
*/
var queue = {};
queue.items = new Array();
queue.timer = 0;
queue.pos = 0;

//start the queue
queue.start = function() {
setInterval( this.process, 50 );
}

//add to queue
queue.add = function( func, time, args ) {
this.items[this.items.length] = {
time: time,
func: func,
args: args
};
}

//process the queue
queue.process = function() {
//empty list?
if( queue.items[queue.pos] == undefined )
return;

//timer up?
if( queue.timer <= 0 ) {
//do next item
queue.items[queue.pos].func( queue.items[queue.pos].args );
//up timer
queue.timer = queue.items[queue.pos].time;
//up position
queue.pos++;
}

//decrease timer
queue.timer -= 50;
}