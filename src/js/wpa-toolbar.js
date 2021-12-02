(function () {
	var a11y_toolbar = document.createElement( 'div' );
	var insert_a11y_toolbar = '';

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
	a11y_toolbar.classList.add( wpa.response, 'a11y-toolbar', wpa.is_rtl, wpa.is_right );
	a11y_toolbar.innerHTML = insert_a11y_toolbar;

	var insertionPoint = document.querySelector( wpa.location );
	if ( null !== insertionPoint ) {
		insertionPoint.insertAdjacentElement( 'afterbegin', a11y_toolbar );
	} else {
		console.log( 'WP Accessibility Toolbar insertion point not valid.' );
	}
})();