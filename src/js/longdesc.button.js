(function ($) {
	'use strict';
	$('img[longdesc]').each(function () {
		var longdesc = $(this).attr('longdesc');
		var img = $(this);
		var classes = img.attr('class');
		var class_array = img.attr('class').match(/\S+/g);
		var image_id = '';
		$.each( class_array, function ( index, value ) {
			if ( value.match( /wp-image-/gi ) ) {
				image_id = value;
			}
		});
		// Secondary check for image ID, if not in classes.
		if ( '' === image_id ) {
			var imgId = img.attr( 'id' );
			image_id = imgId.replace( 'longdesc-return-', '' );
		}
		img.attr('class', '');
		img.wrap('<div class="wpa-ld" />')
		img.parent('.wpa-ld').addClass(classes);
		img.parent('.wpa-ld').append('<button aria-expanded="false" class="wpa-toggle">' + wparest.text + '</button>');
		img.parent('.wpa-ld').append('<div class="longdesc"></div>');
		var container = img.parent('.wpa-ld').children('.longdesc');
		container.hide();
		container.load( longdesc + ' #desc_' + image_id );
		img.parent('.wpa-ld').children('button').on( 'click', function(e) {
			e.preventDefault();
			var visible = container.is( ':visible' );
			if ( visible ) {
				$( this ).attr( 'aria-expanded', 'false' );
				container.hide();
			} else {
				$( this ).attr( 'aria-expanded', 'true' );
				container.show(150);
			}
		});
	});

	$( 'figure.is-style-longdesc' ).each(function() {
		var img = $(this).find( 'img' );
		wpa_load_image( img );
	});

	function wpa_draw_longdesc( img, image_id, longdesc, rawdesc ) {
		var classes = img.attr('class');
		img.attr('class', '').attr('longdesc', longdesc );
		img.attr('id','longdesc-return-' + image_id );
		img.wrap('<div class="wpa-ld" />')
		img.parent('.wpa-ld').addClass(classes);
		img.parent('.wpa-ld').append('<button aria-expanded="false" class="wpa-toggle">' + wparest.text + '</button>');
		img.parent('.wpa-ld').append('<div class="longdesc"></div>');
		var container = img.parent('.wpa-ld').children('.longdesc');
		container.hide();
		container.load( longdesc + ' #desc_' + image_id, {limit:25}, 
			function( responseText, textStatus, xhr ) {
				if ( 'error' === textStatus ) {
					container.html( rawdesc );
				}
			}
		);
		img.parent('.wpa-ld').children('button').on( 'click', function(e) {
			e.preventDefault();
			var visible = container.is( ':visible' );
			if ( visible ) {
				$( this ).attr( 'aria-expanded', 'false' );
				container.hide();
			} else {
				$( this ).attr( 'aria-expanded', 'true' );
				container.show(150);
			}
		});
	}

	function wpa_load_image( img ) {
		var classes = img.attr( 'class' );
		if ( '' === classes ) {
			classes = img.parent( '.wpa-alt' ).attr( 'class' ).replace( 'wpa-alt ', '' );
		}
		var id = classes.replace( 'wp-image-', '' );
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
					wpa_draw_longdesc( img, id, url, rawdesc );
				}
			})
			.fail( function() {
				alert( 'cannot load media' )
			});
	}
}(jQuery));