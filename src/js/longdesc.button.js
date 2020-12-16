(function ($) {
	'use strict';
	$('img[longdesc]').each(function () {
		var longdesc = $(this).attr('longdesc');
		var text = '<span>Long Description</span>';
		var classes = $(this).attr('class');
		var class_array = ( Array.isArray(classes) ) ? classes.split(' ') : [];
		var image_id = '';
		$.each( class_array, function ( index, value ) {
			if ( value.match( /wp-image-/gi ) ) {
				image_id = value;
			}
		});
		$(this).attr('class', '');
		$(this).wrap('<div class="wpa-ld" />')
		$(this).parent('.wpa-ld').addClass(classes);
		$(this).parent('.wpa-ld').append('<div class="longdesc" aria-live="assertive"></div>');
		$(this).parent('.wpa-ld').append('<button>' + text + '</button>');
		var container = $(this).parent('.wpa-ld').children('.longdesc');
		container.hide();
		container.load( longdesc + ' #desc_' + image_id );
		$(this).parent('.wpa-ld').children('button').on( 'click', function(e) {
			e.preventDefault();
			var visible = container.is( ':visible' );
			if ( visible ) {
				container.hide();
			} else {
				container.show(150);
			}
		});
	});
}(jQuery));