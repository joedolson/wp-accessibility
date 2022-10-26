(function ($) {
	'use strict';
	var selector = ( wpalt.selector ) ? wpalt.selector : '.hentry img[alt!=""], .comment-content img[alt!=""]';
	$( selector ).each(function () {
		var inLink   = ( $(this).closest( 'a' ) ) ? true : false;
		var inButton = ( $(this).closest( 'button' ) ) ? true : false;
		var width    = $(this).innerWidth();
		var height   = $(this).innerHeight();
		if ( width || height ) {
			width    = ( ! width ) ? 56 : width; // Enough width for button to be visible.
			height   = ( ! height ) ? 56 : height; // Enough height for button & first line to be visible.
			var area = height * width;
			if ( area < ( 150 * 300 ) ) {
				// Small images won't get displayed alt text containers.
				return;
			}
		}
		var alt = $(this).attr('alt');
		var img = $(this);
		var classes = img.attr('class');

		img.attr('class', '');
		if ( inLink || inButton ) {
			var wrapper = ( inLink ) ? $( this ).closest( 'a' ) : $( this ).closest( 'button' );
			wrapper.wrap( '<div class="wpa-alt" />' );
		} else {
			img.wrap('<div class="wpa-alt" />')
		}
		img.closest('.wpa-alt').addClass(classes);
		img.closest('.wpa-alt').append('<div class="wpa-alt-text" aria-live="assertive"></div>');
		img.closest('.wpa-alt').append('<button aria-expanded="false" class="wpa-toggle">alt</button>');
		var container = img.closest('.wpa-alt').children('.wpa-alt-text');
		container.hide();
		container.html( alt );
		img.closest('.wpa-alt').children('button').on( 'click', function(e) {
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