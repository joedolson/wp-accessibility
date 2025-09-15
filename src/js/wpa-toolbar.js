/*
 * Originally by Chris Rodriguez. Original: https://github.com/downzer0/a11y-toolbar
 */

// Cookie handler.
function createCookie(name, value, days) {
	let expires;
	if (days) {
		let date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		expires = "; expires=" + date.toGMTString();
	} else {
		expires = '';
	}

	document.cookie = name + "=" + value + expires + "; path=/; SameSite=Strict;";
}

function readCookie(name) {
	let nameEQ = name + "=";
	let ca = document.cookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1, c.length);
		}
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length, c.length);
		}
	}

	return null;
}

function eraseCookie(name) {
	createCookie(name, "");
}

(function () {
	const a11y_toolbar = document.createElement( 'div' );
	let insert_a11y_toolbar = '';

	insert_a11y_toolbar += '<ul class="a11y-toolbar-list">';
	if ( wpatb.enable_contrast == 'true' ) {
		insert_a11y_toolbar += '<li class="a11y-toolbar-list-item"><button type="button" class="a11y-toggle a11y-toggle-contrast toggle-contrast" data-id="is_normal_contrast" aria-pressed="false"><span class=\"offscreen\">' + wpatb.contrast + '</span><span class="aticon aticon-adjust" aria-hidden="true"></span></button></li>';
	}
	if ( wpatb.enable_grayscale == 'true' ) {
		insert_a11y_toolbar += '<li class="a11y-toolbar-list-item"><button type="button" class="a11y-toggle a11y-toggle-grayscale toggle-grayscale" data-id="is_normal_color" aria-pressed="false"><span class="offscreen">' + wpatb.grayscale + '</span><span class="aticon aticon-tint" aria-hidden="true"></span></button></li>';
	}
	if ( wpatb.enable_fontsize == 'true' ) {
		insert_a11y_toolbar += '<li class="a11y-toolbar-list-item"><button type="button" class="a11y-toggle a11y-toggle-fontsize toggle-fontsize" data-id="is_normal_fontsize" aria-pressed="false"><span class="offscreen">' + wpatb.fontsize + '</span><span class="aticon aticon-font" aria-hidden="true"></span></button></li>';
	}
	insert_a11y_toolbar += '</ul>';
	a11y_toolbar.classList.add( wpatb.responsive, 'a11y-toolbar', wpatb.is_rtl, wpatb.is_right, wpatb.custom_location );
	a11y_toolbar.innerHTML = insert_a11y_toolbar;

	let insertionPoint = document.querySelector( wpatb.location );
	if ( null !== insertionPoint ) {
		insertionPoint.insertAdjacentElement( 'afterbegin', a11y_toolbar );
	} else {
		insertionPoint = document.querySelector( 'body' );
		a11y_toolbar.classList.remove( 'custom-location' );
		a11y_toolbar.classList.add( 'standard-location' );
		insertionPoint.insertAdjacentElement( 'afterbegin', a11y_toolbar );
		console.log( 'WP Accessibility Toolbar insertion point not valid. Moved to body.' );
	}

let a11yToggle = document.querySelectorAll( '.a11y-toggle' );
	const body = document.querySelector( 'body' );
	const head = document.querySelector( 'head' );
	const html = document.querySelector( 'html' );
	const toolbar = document.querySelector( '.a11y-toolbar' );

	a11yToggle.forEach( (el) => {
		el.addEventListener( 'focus', function() {
			el.classList.remove( 'tooltip-dismissed' );
		});
		el.addEventListener( 'keyup', function(e) {
			if ( e.keyCode == 27 ) {
				el.classList.addClass( 'tooltip-dismissed' );
			}
		});
	});

	// Saturation handler.
	if ( readCookie( 'a11y-desaturated' ) ) {
		desaturate();
	}
	// high contrast handler.
	if ( readCookie( 'a11y-high-contrast' ) ) {
		addHighContrast();
	}

	// font size switcher.
	if (readCookie('a11y-larger-fontsize')) {
		largeFontSize();
	}

	function desaturate() {
		html.classList.add( 'desaturated' );
		let buttons = document.querySelectorAll( '[data-id="is_normal_color"]' );
		buttons.forEach( (button) => {
			button.setAttribute( 'data-id', 'is_grayscale' );
			button.setAttribute( 'aria-pressed', true );
			button.classList.add( 'active' );
		});
	}

	function resaturate() {
		html.classList.remove( 'desaturated' );
		let buttons = document.querySelectorAll( '[data-id="is_grayscale"]' );
		buttons.forEach( (button) => {
			button.setAttribute( 'data-id', 'is_normal_color' );
			button.setAttribute( 'aria-pressed', false );
			button.classList.remove( 'active' );
		});
	}

	function addHighContrast() {
		body.classList.add( 'contrast' );
		let buttons = document.querySelectorAll( '[data-id="is_normal_contrast"]' );
		buttons.forEach( (button) => {
			button.setAttribute( 'data-id', 'is_high_contrast' );
			button.setAttribute( 'aria-pressed', true );
			button.classList.add( 'active' );
		});

		let styles = document.createElement( 'link' );
		styles.setAttribute( 'href', wpa11y.path );
		styles.setAttribute( 'id', 'highContrastStylesheet' );
		styles.setAttribute( 'rel', 'stylesheet' );
		head.insertAdjacentElement( 'beforeend', styles );
	}

	function resetContrast() {
		body.classList.remove( 'contrast' );
		let buttons = document.querySelectorAll( '[data-id="is_high_contrast"]' );
		buttons.forEach( (button) => {
			button.setAttribute( 'data-id', 'is_normal_contrast' );
			button.setAttribute( 'aria-pressed', false );
			button.classList.remove( 'active' );
		});
		let styles = document.getElementById( 'highContrastStylesheet' );
		styles.remove();
	}

	function largeFontSize() {
		html.classList.add( 'fontsize' );
		let buttons = document.querySelectorAll( '[data-id="is_normal_fontsize"]' );
		buttons.forEach( (button) => {
			button.setAttribute( 'data-id', 'is_large_fontsize' );
			button.setAttribute( 'aria-pressed', true );
			button.classList.add( 'active' );
		});
	}

	function resetFontSize() {
		html.classList.remove( 'fontsize' );
		let buttons = document.querySelectorAll( '[data-id="is_large_fontsize"]' );
		buttons.forEach( (button) => {
			button.setAttribute( 'data-id', 'is_normal_fontsize' );
			button.setAttribute( 'aria-pressed', false );
			button.classList.remove( 'active' );
		});
	}

	const grayscaleButton = document.querySelectorAll( '.toggle-grayscale' );
	const contrastButton = document.querySelectorAll( '.toggle-contrast' );
	const fontsizeButton = document.querySelectorAll( '.toggle-fontsize' );

	if ( null !== grayscaleButton ) {
		grayscaleButton.forEach( (el) => {
			el.addEventListener( 'click', function() {
				if ( this.getAttribute( 'data-id' ) === 'is_normal_color' ) {
					desaturate();
				} else {
					resaturate();
				}
			});
		})
	}
	if ( null !== contrastButton ) {
		contrastButton.forEach( (el) => {
			el.addEventListener( 'click', function() {
				if ( this.getAttribute( 'data-id' ) === 'is_normal_contrast' ) {
					addHighContrast();
				} else {
					resetContrast();
				}
			});
		});
	}
	if ( null !== fontsizeButton ) {
		fontsizeButton.forEach( (el) => {
			el.addEventListener( 'click', function() {
				if ( this.getAttribute( 'data-id' ) === 'is_normal_fontsize' ) {
					largeFontSize();
				} else {
					resetFontSize();
				}
			});
		});
	}

	const focusable = document.querySelectorAll('input,a[href],select,textarea,button:not(.a11y-toggle),[tabindex]:not([tabindex="-1"])');
	focusable.forEach((el) => {
		el.addEventListener( 'focus', function() {
			let bounds  = el.getBoundingClientRect();
			let toolbar = document.querySelector( '.a11y-toolbar.standard-location' );
			if ( ! toolbar ) {
				toolbar = { 'bottom' : 0, 'left' : 0, 'top' : 0, 'right' : 0 };
			} else {
				toolbar = toolbar.getBoundingClientRect();
			}

			let overlap = ! (
				bounds.top > toolbar.bottom ||
				bounds.right < toolbar.left ||
				bounds.bottom < toolbar.top ||
				bounds.left > toolbar.right
			);
			if ( overlap ) {
				let diff = bounds.bottom - toolbar.top;
				window.scrollBy( 0, diff );
			}
		});
	});
})();