/*
	file: inc/js/template.js
	desc: template functions
*/
var template = {};

//stream item template
template.item = function( item, no_image, header ) {
	var long = true;

	//if cookie
	if( cookie.get( 'hide_images' ) )
		no_image = true;

	//been image?
	var been_image = false;

	//is tweet/fb?
	var is_tweet = false;
	var is_post = false;
	for( var i = 0; i < item.refs.length; i++ )
		if( item.refs[i].source_type == 'twitter' && item.refs[i].source_data.text )
			is_tweet = i;
		else if( item.refs[i].source_type == 'facebook' && item.refs[i].source_data.text )
			is_post = i;

	//build string
	var r = '<div class="item" id="article_' + item.id + '" style="opacity:0;">';
	r += header ? '<' + header + '>' : '<h3>';
	r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">' + item.title + '</a>';
	r += header ? '</' + header + '>' : '</h3>';

	if( !no_image && item.image_wide_big && cookie.get( 'two_col' ) ) {
		been_image = true;
		r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">';
		r += '<img class="thumb" src="' + mod_root + '/' + item.image_wide_big + '" alt="' + item.title + '" />';
		r += '</a>';
	} else if( !no_image && item.image_wide ) {
		been_image = true;
		r += '<a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">';
		r += '<img class="thumb" src="' + mod_root + '/' + item.image_wide + '" alt="' + item.title + '" />';
		r += '</a>';
	}

	//content
	r += '<p' + ( been_image || is_tweet || is_post ? '' : ' class="wide"' ) + '>';
	r += item.short_description ? html_entity_decode( item.short_description ) : html_entity_decode( item.description );
	r += '<span class="extended hidden">' + ( item.extended_description ? html_entity_decode( item.extended_description ) : '' ) + '</span>';
	switch( item.type ) {
		case 'video':
			break;
		default:
			r += '... <a href="' + mod_root + '/article/' + item.id + '" class="article_link" rel="nofollow">read article&nbsp;&rarr;</a>';
	}
	r += '</p>';

	//tweet/facebook
	if( !been_image && is_tweet ) {
		r += '<div class="tweet">';
		r += '<img src="http://tweeter.fdev.in/' + item.refs[is_tweet].source_id + '" alt="" />';
		r += '<a href="' + mod_root + '/account/twitter/' + item.refs[is_tweet].source_id + '">@' + item.refs[is_tweet].source_title + '</a>';
		r += '<p>' + item.refs[is_tweet].source_data.text + '</p>';
		r += '</div>';
	} else if( !been_image && is_post ) {
		r += '<div class="tweet">';
		r += '<img src="http://tweeter.fdev.in/' + item.refs[is_post].source_id + '" alt="" />';
		r += '<a href="' + mod_root + '/account/twitter/' + item.refs[is_post].source_id + '">' + item.refs[is_post].source_title + '</a>';
		r += '<p>' + item.refs[is_post].source_data.text + '</p>';
		r += '</div>';
	}



	//meta
	r += '<ul class="meta">';

	//refs
	for( var i = 0; i < item.refs.length; i++ ) {
		var link = mod_root + '/';
		switch( item.refs[i].source_type ) {
			case 'website':
			case 'public':
				link += 'website' + '/' + item.refs[i].source_id;
				break;
			case 'like':
				link += 'user' + '/' + item.refs[i].source_id;
				break;
			case 'facebook':
			case 'twitter':
				link += 'account/' + item.refs[i].source_type + '/' + item.refs[i].source_id;
				break;
			case 'topic':
				link += 'topic/' + item.refs[i].source_id;
				break;
			case 'share':
				link += 'user/' + item.refs[i].source_id;
				break;
			default:
				link = '#';
		}
		r += '<li class="tip hover big"><span>';


		//work out info texts
		switch( item.refs[i].source_type ) {
				case 'twitter':
				case 'facebook':
					//avatar
					r += '<img src="' + ( item.refs[i].source_type == 'twitter' ? 'http://tweeter.fdev.in/' + item.refs[i].source_id : 'http://graph.facebook.com/' + item.refs[i].source_id + '/picture' ) + '" class="avatar" />';

					//text
					r += '<em class="big">"' + ( item.refs[i].source_data.text ? html_entity_decode( item.refs[i].source_data.text ) : 'No post / tweet located' ) + '</em>';

					//link to post
					if( item.refs[i].source_data.postid ) {
						r += '<a target="_blank" href=';
						r += item.refs[i].source_type == 'twitter' ? 'http://twitter.com/' + item.refs[i].source_title + '/status/' + item.refs[i].source_data.postid : 'http://facebook.com/' + item.refs[i].source_id + '/posts/' + item.refs[i].source_data.postid;
						r += '" class="button right ' + item.refs[i].source_type + '">View ' + ( item.refs[i].source_type =='twitter' ? 'Tweet' : 'Post' ) + '</a>';
						r += '<a target="_blank" href="';
						r += item.refs[i].source_type == 'twitter' ? 'http://twitter.com/' + item.refs[i].source_title : 'http://facebook.com/' + item.refs[i].source_id;
						r += '" class="button right green">Profile</a>';
					}
					break;

				default:
					r += '<ul>';
					r += '<li><small class="edit">author</small> ' + ( item.author ? 'Unknown' : item.autho ) + '</li>';
					r += '<li><small class="edit">date</small> ' + item.time + '</li>';
					r += '<li><small class="edit">subscribers</small> ' + ( item.refs[i].source_data.subscribers ? item.refs[i].source_data.subscribers : 0 ) + '</li>';
					r += '</ul>';

					if( item.refs[i].source_type == 'website' ) {
						r += '<form action="' + mod_root + '/process/website-' + ( item.refs[i].subscribed ? 'un' : '' ) + 'subscribe" method="post" id="subunsub" class="website_subscribe">';
						r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
						r += '<input type="hidden" name="website_id" value="' + item.refs[i].source_id + '" />';
						r += '<input type="submit" value="' + ( item.refs[i].subscribed ? 'Unsubscribe' : '+ Subscribe' ) + '" class="button ' + ( item.refs[i].subscribed ? 'red' : 'green' ) + '" />';
						r += '</form>';
					} else if( item.refs[i].source_type == 'topic' ) {
						r += '<form action="' + mod_root + '/process/topic-' + ( item.refs[i].subscribed ? 'un' : '' ) + 'subscribe" method="post" id="subunsub" class="topic_subscribe">';
						r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
						r += '<input type="hidden" name="website_id" value="' + item.refs[i].source_id + '" />';
						r += '<input type="submit" value="' + ( item.refs[i].subscribed ? 'Unsubscribe' : '+ Subscribe' ) + '" class="button ' + ( item.refs[i].subscribed ? 'red' : 'green' ) + '" />';
						r += '</form>';
					} else if( item.refs[i].source_type == 'share' ) {
						r += '<form action="' + mod_root + '/process/' + ( item.refs[i].subscribed ? 'un' : '' ) + 'follow" method="post" id="subunsub" class="user_follow">';
						r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
						r += '<input type="hidden" name="website_id" value="' + item.refs[i].source_id + '" />';
						r += '<input type="submit" value="' + ( item.refs[i].subscribed ? 'Unsubscribe' : '+ Follow' ) + '" class="button ' + ( item.refs[i].subscribed ? 'red' : 'green' ) + '" />';
						r += '</form>';
					}
		}

		switch( item.refs[i].source_type ) {
			case 'twitter':
			case 'facebook':
			case 'website':
			case 'topic':
				r += '<img src="' + mod_root + '/inc/img/icons/share/' + item.refs[i].source_type + '.png" />';
		}

		r += '<strong><a href="' + link + '">';
		switch( item.refs[i].source_type ) {
			case 'twitter':
				r += '@';
				break;
			case 'topic':
				r += 'Topic: ';
				break;
		}
		r += item.refs[i].source_title + '</a></strong><small>';

		//tip text
		switch( item.refs[i].source_type ) {
			case 'public':
				r += 'Public source';
				break;

			case 'website':
			case 'topic':
				if( pulsefeed.streamUser == mod_userid ) {
					r += item.refs[i].subscribed ? 'You are subscribed' : 'Not subscribed';
				} else {
					r += pulsefeed.streamUsername + ' is subscribed';
				}
				break;

			case 'share':
				if( pulsefeed.streamUser == mod_userid ) {
					r += item.refs[i].subscribed ? 'You are subscribed' : 'Not subscribed';
				} else {
					r += pulsefeed.streamUsername + ' is subscribed';
				}
				break;

			case 'facebook':
				r += 'You are subscribed';
				break;

			case 'twitter':
				r += ( pulsefeed.streamUser == mod_userid ? 'You follow' : pulsefeed.streamUsername + 'follows' ) + ' them';
				break;

			default:
				r += 'Unknown';
		}
		r += '</small><span></span></span>';

		r += '<a class="link" href="' + link + '">';	
		switch( item.refs[i].source_type ) {
			case 'public':
			case 'website':
				r += '<img class="icon" src="http://favicon.fdev.in/' + item.refs[i].source_data.domain + '" alt="" />';
				break;
			case 'twitter':
				r += '<img class="icon" src="http://tweeter.fdev.in/' + item.refs[i].source_id + '" alt="" />';
				break;
			case 'facebook':
				r += '<img class="icon" src="http://graph.facebook.com/' + item.refs[i].source_id + '/picture" alt="" />';
				break;
			case 'topic':
				r += '#';
				break;
		}
		r += item.refs[i].source_title + '</a></li>';
	}//end refs
	r += '</ul>';


	//right meta
	r+= '<div class="meta">';

	//logged in?
	if( mod_userid > 0 ) {
		//hide button
		if( item.unread && item.unread == 1 && mod_userid == pulsefeed.streamUser ) {
			r += '<form action="' + mod_root + '/process/article-hide" method="post" class="hide_form">';
			r += '<input type="hidden" name="article_id" value="' + item.id + '" />';
			r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
			r += '<input type="submit" value="Hide" class="meta" />';
			r += '</form> - ';
		}

		//collect
		r += '<span class="collect"><a class="collect_button tip mini always" href="' + mod_root + '/article/' + item.id + '/collect" data-articleid="' + item.id + '">Collect</a></span> - ';

	}
	
	//share
	r += '<form action="' + mod_root + '/process/article-share" method="post" class="share_form">';
	r += '<input type="hidden" name="article_id" value="' + item.id + '" />';
	r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
	r += '<input type="hidden" name="twitter_links" value="' + item.twitter_links + '" />';
	r += '<input type="hidden" name="facebook_shares" value="' + item.facebook_shares + '" />';
	r += '<input type="submit" value="Share" class="meta" />';
	r += '<span class="share"></span>';
	r += '</form>';

	r += '<span class="time"> - ' + item.time_ago + '</span>';

	r += '</div>';
	r += '</div><!--end item-->';

	//return it
	return r;
}

//source browse template
template.source = function( source, type ) {
	//start string
	var r = '<div class="source" style="opacity:0;" id="source_' + source.id + '">';

	//title
	r += '<h2><a href="' + mod_root +  '/' + type + '/' + source.id + '">';
	r += type == 'website' ? '<img class="favicon" src="http://favicon.fdev.in/' + source.site_domain + '" alt="" /> ' : '';
	r += source.title;
	r += '</a>'; 
	r += type == 'website' ? '<a href="' + source.site_url + '" class="edit" target="_blank">' + source.site_url + '</a>' : '';
	r += type == 'collection' ? '<a href="' + mod_root + '/user/' + source.user_id + '" class="edit">' + ( source.owned ? 'your collection' : source.username ) + '</span></a>' : '';
	r += '</h2>';

	//images
	r += '<div class="images"><div class="img one">';
	if( source.articles[0] ) {
		r += '<a href="' + mod_root + '/article/' + source.articles[0].id + '">';
		r += '<span class="title">' + source.articles[0].title + '</span><img src="' + mod_root + '/' + source.articles[0].image_thumb + '" alt="" />';
		r += '</a>';
	}
	r += '</div><div class="img two">';
	if( source.articles[1] ) {
		r += '<a href="' + mod_root + '/article/' + source.articles[1].id + '">';
		r += '<span class="title">' + source.articles[1].title + '</span><img src="' + mod_root + '/' + source.articles[1].image_thumb + '" alt="" />';
		r += '</a>';
	}
	r += '</div><div class="img three">';
	if( source.articles[2] ) {
		r += '<a href="' + mod_root + '/article/' + source.articles[2].id + '">';
		r += '<span class="title">' + source.articles[2].title + '</span><img src="' + mod_root + '/' + source.articles[2].image_thumb + '" alt="" />';
		r += '</a>';
	}
	r += '</div></div>';

	//meta
	r += '<ul class="meta">';
	switch( type ) {
		case 'topic':
		case 'website':
			r += '<li><small class="edit">subscribers</small>' + source.subscribers + '</li>';
			r += '<li><small class="edit">articles</small>' + source.article_count + '</li>';
			break;

		case 'collection':
			r += '<li><small class="edit">views</small>' + source.views + '</li>';
			r += '<li><small class="edit">articles</small>' + source.article_count + '</li>';
			break;
	}
	r += '</ul>';

	switch( type ) {
		case 'topic':
		case 'website':
			r += '<form method="post" action="' + mod_root + '/process/' + type + '-' + ( source.subscribed ? 'unsubscribe' : 'subscribe' ) + '" class="' + type + '_subscribe">';
			r += '<input type="submit" class="button ' + ( source.subscribed ? 'red' : 'green' ) + '" value="' + ( source.subscribed ? 'Unsubscribe' : '+ Subscribe' ) + '" />';
			r += '<input type="hidden" name="' + type + '_id" value="' + source.id + '" />';
			r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
			r += '</form>';
			break;

		case 'collection':
			r += '<form method="post" action="' + mod_root + '/process/collection-delete">';
			r += '<a href="' + mod_root + '/collection/' + source.id + '" class="button blue">View</a>';
			r += source.owned ? '<input type="submit" class="button red" value="Delete" />' : '';
			r += '<input type="hidden" name="' + type + '_id" value="' + source.id + '" />';
			r += '<input type="hidden" name="mod_token" value="' + mod_token + '" />';
			r += '</form>';
			break;
	}

	//end
	r += '</div><!--end source-->';

	//return
	return r;
}