(function ($) {
	'use strict';
	$('.hentry img[alt!=""], .comment-content img[alt!=""]').each(function () {
		var width = $(this).attr( 'width' );
		var height = $(this).attr( 'height' );
		if ( width || height ) {
			width = ( ! width ) ? 56 : width; // Enough width for button to be visible.
			height = ( ! height ) ? 56 : height; // Enough height for button & first line to be visible.
			var area = height * width;
			if ( area < ( 80 * 300 ) ) {
				// Small images won't get displayed alt text containers.
				return;
			}
		}
		var alt = $(this).attr('alt');
		var img = $(this);
		var classes = img.attr('class');

		img.attr('class', '');
		img.wrap('<div class="wpa-alt" />')
		img.parent('.wpa-alt').addClass(classes);
		img.parent('.wpa-alt').append('<div class="wpa-alt-text" aria-live="assertive"></div>');
		img.parent('.wpa-alt').append('<button aria-expanded="false">alt</button>');
		var container = img.parent('.wpa-alt').children('.wpa-alt-text');
		container.hide();
		container.html( alt );
		img.parent('.wpa-alt').children('button').on( 'click', function(e) {
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
}(jQuery));