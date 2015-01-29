(function ($) {
    'use strict';
    $('img[longdesc]').each(function () {
        var longdesc = $(this).attr('longdesc');
        var alt = $(this).attr('alt');
        var classes = $(this).attr('class');
        $(this).wrap('<div class="wpa-ld" />');
        $(this).parent('.wpa-ld').addClass(classes);
        $(this).attr('alt', '').attr('class', '');
        $(this).parent('.wpa-ld').append('<a href="' + longdesc + '" class="longdesc-link">Description<span> of' + alt + '</span></a>');
    });
}(jQuery));		