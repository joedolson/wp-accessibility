(() => {
	'use strict';
	let wrap = (elem, wrapper) => {
		elem.parentElement.insertBefore(wrapper, elem);
		wrapper.appendChild(elem);
	};

	let longDescImgs = document.querySelectorAll( 'img[longdesc]' );
	if ( 'link' === wpald.type ) {
		// Links with an actual longdesc attribute.
		if ( longDescImgs.length > 0 ) {
			longDescImgs.forEach( (el) => {
				let wrapper = document.createElement( 'div' );
				wrapper.classList.add( 'wpa-ld' );
				let longdesc = el.getAttribute('longdesc');
				let alt = el.getAttribute('alt');
				let classes = [...img.classList];
				wrap(el,wrapper);
				classes.forEach(className => {
					wrapper.classList.add(className);
				});
				el.setAttribute('class', '');
				let newLink = document.createElement( 'a' );
				newLink.setAttribute( 'href', longdesc );
				newLink.classList.add( 'longdesc-link' );
				newLink.innerHTML = 'Description<span class="screen-reader-text"> of' + alt + '</span>';
				el.insertAdjacentElement( 'afterend', newLink );
			});
		}
		// Links with the block style.
		let hasStyleLongdesc = document.querySelectorAll( 'figure.is-style-longdesc' );
		if ( hasStyleLongdesc.length > 0 ) {
			hasStyleLongdesc.forEach( (el) => {
				let img = el.querySelector( 'img' );
				wpa_load_image_control( img );
			});
		}

	} else {
		// Handle longdescriptions with buttons.
		if ( longDescImgs.length > 0 ) {
			longDescImgs.forEach( (el) => {
				wrap( el, wrapper );
				let longdesc = el.getAttribute('longdesc');
				let class_array = el.getAttribute('class').match(/\S+/g);
				let image_id = '';
				class_array.forEach( (clas) => {
					if ( clas.match( /wp-image-/gi ) ) {
						image_id = clas;
					}
					wrapper.classList.add( clas );
				});

				// Secondary check for image ID, if not in classes.
				if ( '' === image_id ) {
					let imgId = el.getAttribute( 'id' );
					image_id = imgId.replace( 'longdesc-return-', '' );
				}
				el.setAttribute('class', '');
				wrapper.insertAdjacentHTML( 'beforeend', '<button aria-expanded="false" type="button" class="wpa-toggle">' + wpald.text + '</button>');
				wrapper.insertAdjacentHTML('<div class="longdesc"></div>');
				let container = wrapper.querySelector('.longdesc');
				container.style.display = 'none';

				container.load( longdesc + ' #desc_' + image_id );
				wrapper.querySelector('button').addEventListener( 'click', function() {
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
		}

		// Links with the block style.
		let hasStyleLongdesc = document.querySelectorAll( 'figure.is-style-longdesc' );
		if ( hasStyleLongdesc.length > 0 ) {
			hasStyleLongdesc.forEach( (el) => {
				let img = el.querySelector( 'img' );
				wpa_load_image_control( img );
			});
		}
	}

	function wpa_load_image_control( img ) {
		let id = img.getAttribute( 'class' ).replace( 'wp-image-', '' );
		let api = wpald.url + '/' + id;

		fetch( api )
			.then( response => response.json())
			.then(data => {
				let rawdesc = data.description.rendered;
				rawdesc = rawdesc.replace(/(<([^>]+)>)/gi, '').trim();
				if ( '' !== rawdesc ) {
					let url = new URL( wpald.home );
					url.searchParams.set( 'longdesc', id );
					url.searchParams.set( 'p', id );
					url.toString();
					wpa_draw_longdesc( img, id, url, rawdesc );
				}
			})
			.catch( error => {
				console.log( error );
			});
	}

	function wpa_draw_longdesc( img, image_id, longdesc, rawdesc ) {
		let wrapper = document.createElement( 'div' );
		wrapper.classList.add( 'wpa-ld' );
		let alt = img.getAttribute('alt');
		if ( '' === alt ) {
			alt = '<code>' + img.getAttribute('src').replace( wpald.home, '' ) + '</code>';
		}
		let post_classes = document.body.className.split(/\s+/);
		let post_id = '';

		post_classes.forEach( (value) => {
			if ( value.match( /postid-/gi ) ) {
				post_id = value.replace( 'postid-', '', value );
			}
			if ( value.match( /page-id-/gi ) ) {
				post_id = value.replace( 'page-id-', '', value );
			}
		});
		let url = new URL(longdesc);
		url.searchParams.set( 'referrer', post_id );
		url.toString();
		let classes = [...img.classList];
		classes.forEach( (c) => {
			wrapper.classList.add(c);
		});
		wrap( img, wrapper );

		img.setAttribute('alt', '');
		img.setAttribute('class', '');
		if ( 'link' === wpald.type ) {
			wrapper.insertAdjacentHTML( 'beforeend', '<a href="' + url + '" class="longdesc-link">Description<span class="screen-reader-text"> of ' + alt + '</span></a>');
		} else {
			wrapper.insertAdjacentHTML( 'beforeend', '<button aria-expanded="false" class="wpa-toggle">' + wpald.text + '</button>');
			wrapper.insertAdjacentHTML( 'beforeend', '<div class="longdesc"></div>');
			let container = wrapper.querySelector('.longdesc');
			container.style.display = 'none';

			fetch( longdesc )
				.then( response => {
					if ( response.ok ) {
						return response.text();
					} else {
						container.insertAdjacentHTML( 'beforeend', rawdesc );
					}
				}).then (html => {
					const parser = new DOMParser();
					const doc = parser.parseFromString( html, "text/html" );
					container.insertAdjacentElement( 'beforeend', doc.querySelector( '#desc_' + image_id ) );
				});

			wrapper.querySelector('button').addEventListener( 'click', function(e) {
				let visible = container.checkVisibility();
				if ( visible ) {
					this.setAttribute( 'aria-expanded', 'false' );
					container.style.display = 'none';
				} else {
					this.setAttribute( 'aria-expanded', 'true' );
					container.style.display = 'block';
				}
			});
		}
	}
})();