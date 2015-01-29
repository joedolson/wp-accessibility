(function ($) {
    'use strict';
    $('img[longdesc]').each(function () {
        var longdesc = $(this).attr('longdesc');
        var text = '<span>Long Description</span>';
        var classes = $(this).attr('class');
        $(this).attr('class', '');
        $(this).wrap('<div class="wpa-ld" />')
        $(this).parent('.wpa-ld').addClass(classes);
        $(this).parent('.wpa-ld').append('<div class="longdesc" aria-live="assertive"></div>'); // better supported
        $(this).parent('.wpa-ld').append('<button>' + text + '</button>');
        $(this).parent('.wpa-ld').children('.longdesc').hide();
        $(this).parent('.wpa-ld').children('.longdesc').load(longdesc + ' #desc');
        $(this).parent('.wpa-ld').children('button').toggle(function () {
            $(this).parent('.wpa-ld').children('.longdesc').show(150);
        }, function () {
            $(this).parent('.wpa-ld').children('.longdesc').hide();
        });
    });
}(jQuery));
		