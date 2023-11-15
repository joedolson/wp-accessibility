(function ($) {
	'use strict';
	var selector = ( wpalt.selector ) ? wpalt.selector : '.hentry img[alt!=""], .comment-content img[alt!=""]';
	var collection = $( selector );
	collection.each( function () {
		var img      = $( this );
		var inLink   = ( 1 == img.closest( 'a' ).length ) ? true : false;
		var inButton = ( 1 == img.closest( 'button' ).length ) ? true : false;
		var width    = img.innerWidth();
		var height   = img.innerHeight();
		if ( width || height ) {
			width    = ( ! width ) ? 56 : width; // Enough width for button to be visible.
			height   = ( ! height ) ? 56 : height; // Enough height for button & first line to be visible.
			var area = height * width;
			if ( area < ( 150 * 300 ) ) {
				// Small images won't get displayed alt text containers.
				return;
			}
		}
		var alt = img.attr('alt');
		// The default selector will also pick up images with missing alt attribute, so eliminate those els.
		if ( ! alt ) {
			return;
		}
		var classes = img.attr('class');
		img.attr('class', '');
		if ( inLink || inButton ) {
			var wrapper = ( inLink ) ? img.closest( 'a' ) : img.closest( 'button' );
			wrapper.wrap( '<div class="wpa-alt" />' );
		} else {
			img.wrap('<div class="wpa-alt" />')
		}
		img.closest('.wpa-alt').addClass( classes );
		img.closest('.wpa-alt').append('<button aria-expanded="false" type="button" class="wpa-toggle">alt</button>');
		img.closest('.wpa-alt').append('<div class="wpa-alt-text"></div>');
		var container = img.closest('.wpa-alt').children('.wpa-alt-text');
		container.hide();
		container.html( alt );
		img.closest('.wpa-alt').children('button').on( 'click', function(e) {
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