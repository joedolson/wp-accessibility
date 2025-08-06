(() => {
	'use strict';
	let selector = ( wpalt.selector ) ? wpalt.selector : '.hentry img[alt]:not([alt=""]), .comment-content img[alt]:not([alt=""]), #content img[alt]:not([alt=""]),.entry-content img[alt]:not([alt=""])';
	let collection = document.querySelectorAll(  selector );
	let wrap = (elem, wrapper) => {
		elem.parentElement.insertBefore(wrapper, elem);
		wrapper.appendChild(elem);
	};
	collection.forEach((el) => {
		let img       = el;
		let closestLink   = img.closest( 'a' );
		let closestButton = img.closest( 'button' );
		let inLink    = ( closestLink ) ? true : false;
		let inButton  = ( closestButton ) ? true : false;
		let width     = img.innerWidth;
		let height    = img.innerHeight;
		if ( width || height ) {
			width    = ( ! width ) ? 56 : width; // Enough width for button to be visible.
			height   = ( ! height ) ? 56 : height; // Enough height for button & first line to be visible.
			let area = height * width;
			if ( area < ( 150 * 300 ) ) {
				// Small images won't get displayed alt text containers.
				return;
			}
		}
		let alt = img.getAttribute('alt');

		let classes = [...img.classList];
		img.setAttribute('class', '');
		let elContainer = document.createElement( 'div' );
		elContainer.classList.add( 'wpa-alt' );
		if ( inLink || inButton ) {
			let wrapper = ( inLink ) ? closestLink : closestButton;
			wrap( wrapper, elContainer );
		} else {
			wrap( img, elContainer );
		}
		classes.forEach(className => {
			elContainer.classList.add(className);
		});

		elContainer.insertAdjacentHTML('afterbegin', '<button aria-expanded="false" type="button" class="wpa-toggle">alt</button>');
		elContainer.insertAdjacentHTML('beforeend', '<div class="wpa-alt-text"></div>');
		let container = elContainer.querySelector('.wpa-alt-text');
		container.style.display = 'none';
		container.innerText = alt;
		let button = elContainer.querySelector('button');
		button.addEventListener( 'click', function(e) {
			let visible = container.checkVisibility();
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