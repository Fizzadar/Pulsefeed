/*
	file: inc/js/cookie
	desc: handle cookies
	note: an almost identical copy to the php class
	uses: https://github.com/carhartl/jquery-cookie
*/
var cookie = {
	//vars
	cookie_id: 'pulsefeed_',
	cookie_domain: '.' + window.location.host,

	//set cookie
	set: function( key, value ) {
		return $.cookie( this.cookie_id + key, value, { expires: 365, path: '/', domain: this.cookie_domain } );
	},

	//get cookie
	get: function( key ) {
		return $.cookie( this.cookie_id + key );
	},

	//delete cookie
	delete: function( key ) {
		$.cookie( this.cookie_id + key, false, { expires: -1, path: '/', domain: this.cookie_domain } );
	}
};