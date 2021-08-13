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

	if ( wpa.target ) {
		$('a:not(.wpa-allow-target)').removeAttr( 'target' );
	}

	if ( wpa.tabindex ) {
		$('input,a,select,textarea,button').removeAttr('tabindex');
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
