/*
	file: inc/js/template.js
	desc: template functions
*/
var template = {};

//stream item template
template.item = function( item ) {
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

	//refs
	for( var i = 0; i < item.refs.length; i++ ) {
		r += '<a href="' + mod_root + '/';
		switch( item.refs[i].source_type ) {
			case 'source':
				r += 'source/' + item.refs[i].source_id;
				break;
			case 'like':
				r += 'user/' + item.refs[i].source_id;
				break;
			case 'facebook':
			case 'twitter':
				r += 'account/' + item.refs[i].source_type;
				break;
			default:
				r += '#';
		}
		r += '" class="tip">';
		r += '<span><strong>' + item.refs[i].source_title + '</strong><small>';
		switch( item.refs[i].source_type ) {
			case 'public':
				r += 'Public source';
				break;
			case 'source':
				r += 'You are subscribed';
				break;
			case 'twitter':
				r += 'You follow them';
				break;
			case 'facebook':
				r += 'You are subscribed';
				break;
			case 'like':
				r += 'You follow them';
				break;
			default:
				r += 'Unknown';
		}
		r += '</small><span></span></span>';
		r += '<img src="';
		switch( item.refs[i].source_type ) {
			case 'public':
			case 'source':
				r += 'http://www.google.com/s2/favicons?domain=' + item.refs[i].source_data.domain;
				break;
			case 'twitter':
			case 'facebook':
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
			r += '<img src="http://www.google.com/s2/favicons?domain=' + item.refs[i].origin_data.domain + '" alt="" /></a>';
		}
	}//end refs

	//logged in?
	if( mod_userid > 0 ) {
		//hide button
		if( item.unread && item.unread == 1 ) {
			r += '<form action="' + mod_root + '/process/article-hide" method="post" class="hide_form">';
			r += '<input type="hidden" name="article_id" value="' + item.id + '" />';
			r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
			r += '<input type="submit" value="Hide" />';
			r += '</form> - ';
		}

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
	r += '<a href="' + mod_root + '/source/' + source.id + '">';
	r += '<img src="http://screenshots.fanaticaldev.com/?u=' + source.site_url + '&w=110&h=75" alt="" />';
	r += '</a><h2><a href="' + mod_root + '/source/' + source.id + '">' + source.site_title + '</a></h2>';
	r += '<span class="url"><a target="_blank" href="' + source.site_url + '">' + source.site_url_trim + '</a></span>';
	r += '<span class="meta">Subscribers: <strong>' + source.subscribers + '</strong></span>';
	
	//logged in?
	if( mod_userid > 0 ) {
		r += '<form action="' + mod_root + '/process/' + ( source.subscribed ? 'unsubscribe' : 'subscribe' ) + '" method="post" class="source_subscribe">';
		r += '<input type="hidden" name="source_id" value="' + source.id + '" />';
		r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
		r += '<input type="submit" value="' + ( source.subscribed ? 'UnSubscribe" class="unsubscribe"' : 'Subscribe"' ) + ' />';
		r += '</form>';
	}

	r += '</div><!--end source-->';

	//return
	return r;
}