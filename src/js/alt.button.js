(() => {
	'use strict';
	var selector = ( wpalt.selector ) ? wpalt.selector : '.hentry img[alt]:not([alt=""]), .comment-content img[alt]:not([alt=""]), #content img[alt]:not([alt=""]),.entry-content img[alt]:not([alt=""])';
	var collection = document.querySelectorAll(  selector );
	var wrap = (elem, wrapper) => {
		elem.parentElement.insertBefore(wrapper, elem);
		wrapper.appendChild(elem);
	};
	collection.forEach((el) => {
		var img       = el;
		var closestLink   = img.closest( 'a' );
		var closestButton = img.closest( 'button' );
		var inLink    = ( closestLink ) ? true : false;
		var inButton  = ( closestButton ) ? true : false;
		var width     = img.innerWidth;
		var height    = img.innerHeight;
		if ( width || height ) {
			width    = ( ! width ) ? 56 : width; // Enough width for button to be visible.
			height   = ( ! height ) ? 56 : height; // Enough height for button & first line to be visible.
			var area = height * width;
			if ( area < ( 150 * 300 ) ) {
				// Small images won't get displayed alt text containers.
				return;
			}
		}
		var alt = img.getAttribute('alt');

		var classes = [...img.classList];
		img.setAttribute('class', '');
		var elContainer = document.createElement( 'div' );
		elContainer.classList.add( 'wpa-alt' );
		if ( inLink || inButton ) {
			var wrapper = ( inLink ) ? closestLink : closestButton;
			wrap( wrapper, elContainer );
		} else {
			wrap( img, elContainer );
		}
		classes.forEach(className => {
			elContainer.classList.add(className);
		});

		elContainer.insertAdjacentHTML('afterbegin', '<button aria-expanded="false" type="button" class="wpa-toggle">alt</button>');
		elContainer.insertAdjacentHTML('beforeend', '<div class="wpa-alt-text"></div>');
		var container = elContainer.querySelector('.wpa-alt-text');
		container.style.display = 'none';
		container.innerText = alt;
		var button = elContainer.querySelector('button');
		button.addEventListener( 'click', function(e) {
			var visible = container.checkVisibility();
			if ( visible ) {
				this.setAttribute( 'aria-expanded', 'false' );
				container.style.display = 'none';
			} else {
				this.setAttribute( 'aria-expanded', 'true' );
				container.style.display = 'block';
			}
		});
	});
})();