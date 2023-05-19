/*
 * Chris Rodriguez
 * chris@inathought.com
 */

// Cookie handler, non-$ style
function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	} else {
		var expires = '';
	}

	document.cookie = name + "=" + value + expires + "; path=/; SameSite=Strict;";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}

	return null;
}

function eraseCookie(name) {
	createCookie(name, "");
}

( function( $ ) {
	$( '.a11y-toggle' ).on( 'focus', function(e) {
		$( this ).removeClass( 'tooltip-dismissed' );
	});

	$( '.a11y-toggle' ).on( 'keyup', function(e) {
		if ( e.keyCode == 27 ) {
			$( this ).addClass( 'tooltip-dismissed' );
		}
	});

	// Saturation handler.
	if (readCookie('a11y-desaturated')) {
		$('body').addClass('desaturated');
		$('#is_normal_color').attr('id', 'is_grayscale').attr('aria-pressed', true).addClass('active');
	}
	// high contrast handler.
	if (readCookie('a11y-high-contrast')) {
		$('body').addClass('contrast');
		$('head').append($("<link href='" + wpa11y.path + "' id='highContrastStylesheet' rel='stylesheet' type='text/css' />"));
		$('#is_normal_contrast').attr('id', 'is_high_contrast').attr('aria-pressed', true).addClass('active');
		$('.a11y-toolbar ul li a i').addClass('icon-white');
	}
	// font size switcher.
	if (readCookie('a11y-larger-fontsize')) {
		$('html').addClass('fontsize');
		$('#is_normal_fontsize').attr('id', 'is_large_fontsize').attr('aria-pressed', true).addClass('active');
	}

	$('.toggle-grayscale').on('click', function (e) {
		if ($(this).attr('id') == "is_normal_color") {
			$('body').addClass('desaturated');
			$(this).attr('id', 'is_grayscale').attr('aria-pressed', true).addClass('active');
			createCookie('a11y-desaturated', '1');
		} else {
			$('body').removeClass('desaturated');
			$(this).attr('id', 'is_normal_color').attr('aria-pressed', false).removeClass('active');
			eraseCookie('a11y-desaturated');
		}

		return false;
	});

	$('.toggle-contrast').on('click', function (e) {
		if ($(this).attr('id') == "is_normal_contrast") {
			$('head').append($("<link href='" + wpa11y.path + "' id='highContrastStylesheet' rel='stylesheet' type='text/css' />"));
			$('body').addClass('contrast');
			$(this).attr('id', 'is_high_contrast').attr('aria-pressed', true).addClass('active');
			createCookie('a11y-high-contrast', '1');
		} else {
			$('#highContrastStylesheet').remove();
			$('body').removeClass('contrast');
			$(this).attr('id', 'is_normal_contrast').attr('aria-pressed', false).removeClass('active');
			eraseCookie('a11y-high-contrast');
		}

		return false;
	});

	$('.toggle-fontsize').on('click', function (e) {
		if ($(this).attr('id') == "is_normal_fontsize") {
			$('html').addClass('fontsize');
			$(this).attr('id', 'is_large_fontsize').attr('aria-pressed', true).addClass('active');
			createCookie('a11y-larger-fontsize', '1');
		} else {
			$('html').removeClass('fontsize');
			$(this).attr('id', 'is_normal_fontsize').attr('aria-pressed', false).removeClass('active');
			eraseCookie('a11y-larger-fontsize');
		}

		return false;
	});

} )( jQuery );
