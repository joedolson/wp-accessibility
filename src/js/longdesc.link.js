(function ($) {
	'use strict';
	$('img[longdesc]').each(function () {
		var longdesc = $(this).attr('longdesc');
		var alt = $(this).attr('alt');
		var classes = $(this).attr('class');
		$(this).wrap('<div class="wpa-ld" />');
		$(this).parent('.wpa-ld').addClass(classes);
		$(this).attr('alt', '').attr('class', '');
		$(this).parent('.wpa-ld').append('<a href="' + longdesc + '" class="longdesc-link">Description<span> of' + alt + '</span></a>');
	});

	$( 'figure.is-style-longdesc' ).each(function() {
		var img = $(this).find( 'img' );
		wpa_load_image( img );
	});

	function wpa_draw_longdesc( img, image_id, longdesc ) {
		var alt = img.attr('alt');
		var post_classes = document.body.className.split(/\s+/);
		var post_id = '';
		console.log( post_classes );
		$.each( post_classes, function ( index, value ) {
			if ( value.match( /postid-/gi ) ) {
				post_id = value.replace( 'postid-', '', value );
			}
		});
		var url = new URL(longdesc);
		url.searchParams.set('referrer',post_id );
		url.toString();
		var classes = img.attr('class');
		img.wrap('<div class="wpa-ld" />');
		img.parent('.wpa-ld').addClass(classes);
		img.attr('alt', '').attr('class', '');
		img.parent('.wpa-ld').append('<a href="' + url + '" class="longdesc-link">Description<span> of' + alt + '</span></a>');
	}

	function wpa_load_image( img ) {
		var id = img.attr( 'class' ).replace( 'wp-image-', '' );
		var api = wparest.url + '/' + id;

		$.get( api )
			.done( function( response ) {
				var attachment = {
					attachment: response
				}
				var rawdesc = response.description.rendered;
				rawdesc = rawdesc.replace(/(<([^>]+)>)/gi, '').trim();
				if ( '' !== rawdesc ) {
					var url = new URL( response.link );
					url.searchParams.set( 'longdesc', id );
					url.toString();
					wpa_draw_longdesc( img, id, url );
				}
			})
			.fail( function() {
				alert( 'cannot load media' )
			});
	}
}(jQuery));