(function ($) {
    'use strict';
    $('nav').each(function () {
        $(this).attr('role', 'navigation');
    });
    $('main').each(function () {
        $(this).attr('role', 'main');
    });
    $('header:first').each(function () {
        $(this).attr('role', 'banner');
    });
    $('footer:last').each(function () {
        $(this).attr('role', 'contentinfo');
    });
    $('input[name=s]').parents('form').attr('role', 'search');
    if (wpaComplementary != false) {
        $('#' + wpaComplementary).each(function () {
            $(this).attr('role', 'complementary');
        });
    }
}(jQuery));
		