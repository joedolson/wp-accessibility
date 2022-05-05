(function( $ ) { 'use strict';

	if ( wpa.skiplinks.enabled ) {
		$('body').prepend( wpa.skiplinks.output );
	}

	var html = document.querySelector( 'html' );
	var lang = html.getAttribute( 'lang' );
	if ( ! lang ) {
		$('html').attr( 'lang', wpa.lang );
	}
	var dir  = html.getAttribute( 'dir' );
	if ( ! dir ) {
		$('html').attr( 'dir', wpa.dir );
	}

	var viewport = document.querySelector( 'meta[name="viewport"]' );
	if ( viewport ) {
		var conditions = viewport.getAttribute( 'content' );
		if ( conditions.search(/user-scalable=no/g) ) {
			conditions = conditions.replace( 'user-scalable=no', 'user-scalable=yes' );
			viewport.setAttribute( 'content', conditions );
		}
	}

	if ( wpa.target ) {
		$('a:not(.wpa-allow-target)').removeAttr( 'target' );
	}

	if ( wpa.tabindex ) {
		// Remove tabindex from elements that should be natively focusable.
		var focusable = $('input,a,select,textarea,button').not('a:not([href])');
		focusable.removeAttr('tabindex');

		// Add tabindex to elements that appear active but are not natively focusable.
		var fakeButtons = $('div[role="button"]').not('div[tabindex]' );
		var buttonLinks = $('a[role="button"]').not('a[tabindex],a[href]');
		fakeButtons.attr( 'tabindex', '0' ).addClass('wpa-focusable');
		buttonLinks.attr( 'tabindex', '0' ).addClass('wpa-focusable');
	}

	if ( wpa.underline.enabled ) {
		$( wpa.underline.target ).css( 'text-decoration','underline' );
		$(  wpa.underline.target ).on( 'focusin mouseenter', function() {
			$(this).css( 'text-decoration','none' );
		});
		$(  wpa.underline.target ).on( 'focusout mouseleave', function() {
			$(this).css( 'text-decoration','underline' );
		});
	}

}(jQuery));
