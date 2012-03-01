/*
	file: inc/js/template.js
	desc: template functions
*/
var template = {};

//stream item template
template.item = function( item ) {
	var long = true;

	//build string
	var r = '<div class="item" id="article_' + item.id + '" style="opacity:0;">';
	r += '<h3><a href="' + mod_root + '/article/' + item.id + '" class="article_link">' + item.title + '</a></h3>';

	//image?
	if( item.image_half != '' ) {
		long = false;
		r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link">';
		r += '<img class="thumb" src="' + mod_root + '/' + item.image_half + '" alt="' + item.title + '" />';
		r += '</a>';
	} else if( item.image_third != '' ) {
		long = false;
		r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link">';
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
	r += ' <a href="' + mod_root + '/article/' + item.id + '" class="article_link">read article &rarr;</a></p>';

	//meta
	r += '<div class="meta">';
	r += '<a href="' + mod_root + '/source/' + item.id + '" class="tip"><span>' + item.source_title + '<span></span></span><img src="http://www.google.com/s2/favicons?domain=' + item.source_domain + '" /></a>';

	if( mod_userid > 0 ) {
		//hide button
		if( parseInt( item.expired ) == 0 && item.subscribed && parseInt( item.subscribed ) > 0 ) {
			r += '<form action="' + mod_root + '/process/article-read" method="post" class="hide_form">';
			r += '<input type="hidden" name="article_id" value="' + item.id + '" />';
			r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
			r += '<input type="submit" value="Hide" />';
			r += '</form> - ';
		}

		//like button
		r += '<form action="' + mod_root + '/process/article-' + ( item.recommended ? 'unrecommend' : 'recommend' ) + '" method="post" class="like_form">';
		r += '<input type="hidden" name="article_id" value="' + item.id + '" />';
		r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
		r += '<input type="submit" value="' + ( item.recommended ? 'Unlike' : 'Like' ) + '" /> <span class="likes">(<span>' + item.recommendations + '</span>)</span>';
		r += '</form>';
	}
	
	r += '<span class="time"> - ' + item.time_ago + '</span>';

	r += '</div>';
	r += '</div><!--end item-->';

	//return it
	return r;
}

//recommend template
template.recommend = function( recommend ) {

}

//source browse template
template.source = function( source ) {
	
}