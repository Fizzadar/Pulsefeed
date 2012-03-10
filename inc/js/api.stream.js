/*
	file: inc/js/api.stream.js
	desc: stream api functions
*/

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
		'/process/article-read',
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
				$( el ).attr( 'action', mod_root + '/process/unsubscribe' );
				$( 'input[type=submit]', el ).addClass( 'unsubscribe' );
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
				$( el ).attr( 'action', mod_root + '/process/subscribe' );
				$( 'input[type=submit]', el ).removeClass( 'unsubscribe' );
			},
			function( data, el ) {
				$( 'input[type=submit]', el ).removeAttr( 'disabled' );
				$( 'input[type=submit]', el ).removeClass( 'disabled' );
			},
			el
		);
	}
}