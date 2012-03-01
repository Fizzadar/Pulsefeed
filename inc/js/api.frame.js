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
			'/process/article-recommend',
			{ article_id: article_id },
			function( data, el ) {
				$( 'button span' ).html( 'Unlike' );
				$( 'button img ' ).attr( 'src', mod_root + '/inc/img/icons/recommended.png' );
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
			'/process/article-unrecommend',
			{ article_id: article_id },
			function( data, el ) {
				$( 'button span' ).html( 'Like' );
				$( 'button img ' ).attr( 'src', mod_root + '/inc/img/icons/recommend.png' );
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