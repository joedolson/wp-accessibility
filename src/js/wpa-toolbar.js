(function( $ ) { 'use strict';
	var insert_a11y_toolbar = '<!-- a11y toolbar -->';
	insert_a11y_toolbar += '<div class="' + wpa.responsive + 'a11y-toolbar' + wpa.is_rtl + wpa.is_right + '">';
	insert_a11y_toolbar += '<ul class="a11y-toolbar-list">';
	if ( wpa.enable_contrast == 'true' ) {
		insert_a11y_toolbar += '<li class="a11y-toolbar-list-item"><button type="button" class="a11y-toggle-contrast toggle-contrast" id="is_normal_contrast" aria-pressed="false"><span class=\"offscreen\">' + wpa.contrast + '</span><span class="aticon aticon-adjust" aria-hidden="true"></span></button></li>';
	}
	if ( wpa.enable_grayscale == 'true' ) {
		insert_a11y_toolbar += '<li class="a11y-toolbar-list-item"><button type="button" class="a11y-toggle-grayscale toggle-grayscale" id="is_normal_color" aria-pressed="false"><span class="offscreen">' + wpa.grayscale + '</span><span class="aticon aticon-tint" aria-hidden="true"></span></button></li>';
	}
	if ( wpa.enable_fontsize == 'true' ) {
		insert_a11y_toolbar += '<li class="a11y-toolbar-list-item"><button type="button" class="a11y-toggle-fontsize toggle-fontsize" id="is_normal_fontsize" aria-pressed="false"><span class="offscreen">' + wpa.fontsize + '</span><span class="aticon aticon-font" aria-hidden="true"></span></button></li>';
	}
	insert_a11y_toolbar += '</ul>';
	insert_a11y_toolbar += '</div>';
	insert_a11y_toolbar += '<!-- // a11y toolbar -->';
	$( document ).find( wpa.location ).prepend( insert_a11y_toolbar );
}(jQuery));