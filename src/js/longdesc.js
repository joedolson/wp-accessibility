(function ($) {
	'use strict';

	if ( 'link' === wpald.type ) {
		// Handle longdescriptions with links.
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
			wpa_load_image_link( img );
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

		function wpa_load_image_link( img ) {
			var id = img.attr( 'class' ).replace( 'wp-image-', '' );
			var api = wpald.url + '/' + id;

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
					console.log( 'cannot load media for longdesc on ' + id )
				});
		}
	} else {
		// Handle longdescriptions with buttons.
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
			img.parent('.wpa-ld').append('<button aria-expanded="false" type="button" class="wpa-toggle">' + wpald.text + '</button>');
			img.parent('.wpa-ld').append('<div class="longdesc"></div>');
			var container = img.parent('.wpa-ld').children('.longdesc');
			container.hide();
			container.load( longdesc + ' #desc_' + image_id );
			img.parent('.wpa-ld').children('button').on( 'click', function(e) {
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
			wpa_load_image_button( img );
		});

		function wpa_draw_longdesc( img, image_id, longdesc, rawdesc ) {
			var classes = img.attr('class');
			img.attr('class', '').attr('longdesc', longdesc );
			img.attr('id','longdesc-return-' + image_id );
			img.wrap('<div class="wpa-ld" />')
			img.parent('.wpa-ld').addClass(classes);
			img.parent('.wpa-ld').append('<button aria-expanded="false" class="wpa-toggle">' + wpald.text + '</button>');
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

		function wpa_load_image_button( img ) {
			var classes = img.attr( 'class' );
			if ( '' === classes ) {
				classes = img.parent( '.wpa-alt' ).attr( 'class' ).replace( 'wpa-alt ', '' );
			}
			var id = classes.replace( 'wp-image-', '' );
			var api = wpald.url + '/' + id;

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
					console.log( 'cannot load media for longdesc on ' + id )
				});
		}
	}
}(jQuery));