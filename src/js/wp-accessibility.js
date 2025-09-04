(() => {
	'use strict';
	const html = document.querySelector( 'html' );
	const body = document.querySelector( 'body' );
	let errors = [];
	if ( wpa.lang ) {
		let lang   = html.getAttribute( 'lang' );
		if ( ! lang ) {
			html.setAttribute( 'lang', wpa.lang );
			if ( wpa.errors || wpa.tracking ) {
				errors.push( 'html-lang' );
				console.log( 'HTML language set by WP Accessibility' );
			}
		}
	}

	if ( wpa.dir ) {
		let dir  = html.getAttribute( 'dir' );
		if ( ! dir && wpa.dir !== 'ltr' ) {
			html.setAttributeattr( 'dir', wpa.dir );
			if ( wpa.errors || wpa.tracking ) {
				errors.push( 'html-lang-direction' );
				console.log( 'HTML language direction set by WP Accessibility' );
			}
		}
	}

	if ( wpa.continue ) {
		let readMore = document.querySelectorAll( '.wp-block-post-excerpt__more-link' );
		if ( readMore.length !== 0 ) {
			readMore.forEach( (el) => {
				if ( ! el.hasAttribute( 'aria-describedby' ) ) {
					let post = el.closest( '.wp-block-post' );
					let readMoreId = post.getAttribute( 'class' );
					readMoreId = readMoreId.replaceAll( ' ', '-' );
					let heading = post.querySelector( '.wp-block-post-title' );
					if ( heading ) {
						if ( ! heading.hasAttribute( 'id' ) ) {
							heading.setAttribute( 'id', readMoreId );
						} else {
							readMoreId = heading.getAttribute( 'id' );
						}
						el.setAttribute( 'aria-describedby', readMoreId );
						if ( wpa.errors || wpa.tracking ) {
							console.log( 'Continue Reading link description set by WP Accessibility' );
						}
					}
				}
			});
		}
	}

	const viewport = document.querySelector( 'meta[name="viewport"]' );
	if ( viewport ) {
		let conditionsBefore = viewport.getAttribute( 'content' );
		let conditionsAfter  = viewport.getAttribute( 'content' );
		if ( conditionsBefore.search(/user-scalable=no/g) ) {
			conditionsAfter = conditionsBefore.replace( 'user-scalable=no', 'user-scalable=yes' );
			viewport.setAttribute( 'content', conditionsAfter );
			if ( ( wpa.errors || wpa.tracking ) && conditionsAfter != conditionsBefore ) {
				errors.push( 'viewport-scalable' );
				console.log( 'Viewport made scalable by WP Accessibility' );
			}
		}
		if ( conditionsBefore.search(/maximum-scale=1/g) ) {
			conditionsAfter = conditionsBefore.replace( 'maximum-scale=1', 'maximum-scale=5' );
			conditionsAfter = conditionsAfter.replace( 'maximum-scale=0', 'maximum-scale=5' );
			viewport.setAttribute( 'content', conditionsAfter );
			if ( ( wpa.errors || wpa.tracking ) && conditionsAfter != conditionsBefore  ) {
				errors.push( 'viewport-maxscale' );
				console.log( 'Viewport maximum scale set by WP Accessibility' );
			}
		}
	}

	if ( wpa.skiplinks.enabled ) {
		body.insertAdjacentHTML( 'afterbegin', wpa.skiplinks.output );
		if ( wpa.errors || wpa.tracking  ) {
			errors.push( 'skiplinks' );
			console.log( 'Skip links added by WP Accessibility' );
		}
	}

	if ( wpa.current ) {
		let current = document.querySelectorAll( '.current-menu-item a, .current_page_item a' );
		if ( current.length !== 0 ) {
			current.forEach((el) => {
				el.setAttribute( 'aria-current', 'page' );
			});
			if ( wpa.errors || wpa.tracking  ) {
				errors.push( 'aria-current' );
				console.log( 'ARIA current added by WP Accessibility' );
			}
		}
	}

	if ( wpa.labels ) {
		let wpa_names = [ 's', 'author', 'email', 'url', 'comment' ];
		wpa_names.forEach((value) => {
			let fields, field_id, implicit, aria, ariaId, ariaTarget, hasAria, hasAriaId, label, labelText;
			if ( value == 'comment' ) {
				fields = document.querySelectorAll( 'textarea[name=' + value + ']' );
			} else {
				fields = document.querySelectorAll( 'input[name=' + value + ']:not(#adminbar-search)' );
			}
			if ( fields.length !== 0 ) {
				fields.forEach( (field) => {
					if ( 0 !== field.length ) {
						field_id = field.getAttribute( 'id' );
						implicit = field.closest( 'label' );
						aria = field.getAttribute( 'aria-label' );
						ariaId = field.getAttribute( 'aria-labelledby' );
						ariaTarget = {};
						if ( ariaId ) {
							ariaTarget = document.getElementById( ariaId );
						}
						hasAria   = ( null == aria || '' == aria || 'undefined' == typeof( aria ) ) ? false : true;
						hasAriaId = ( null == ariaId || '' == ariaId || 'undefined' == typeof( ariaId ) ) ? false : true;
						// Add label if aria label empty, aria labelledby empty, or aria reference ID does not exist.
						if ( ( ! hasAria && ! hasAriaId ) || ( ! hasAria && ( hasAriaId && ! ariaTarget ) ) ) {
							if ( field_id ) {
								label = document.querySelector( 'label[for=' + field_id + ']' );
								if ( label ) {
									labelText = label.innerText;
									if ( label && ! labelText ) {
										label.innerText = wpa.wpalabels[value];
										if ( wpa.errors || wpa.tracking ) {
											errors.push( ['empty-label', wpa.wpalabels[value]] );
											console.log( 'Empty label on ' + wpa.wpalabels[value] + ' added by WP Accessibility' );
										}
									}
								}
								if ( !label && !implicit ) {
									field.insertAdjacentHTML( 'beforebegin', "<label for='" + field_id + "' class='wpa-screen-reader-text'>" + wpa.wpalabels[value] + "</label>" );
									if ( wpa.errors || wpa.tracking ) {
										errors.push( ['explicit-label', wpa.wpalabels[value]] );
										console.log( 'Explicit label on ' + wpa.wpalabels[value] + ' added by WP Accessibility' );
									}
								}
							} else {
								if ( !implicit ) {
									field.setAttribute( 'id', 'wpa_label_' + value );
									field.insertAdjacentHTML( 'beforebegin', "<label for='wpa_label_" + value + "' class='wpa-screen-reader-text'>" + wpa.wpalabels[value] + "</label>" );
									if ( wpa.errors || wpa.tracking ) {
										errors.push( ['implicit-label', wpa.wpalabels[value]] );
										console.log( 'Implicit label on ' + wpa.wpalabels[value] + ' added by WP Accessibility' );
									}
								}
							}
						}
					}
				});
			}
		});
	}

	if ( wpa.titles ) {
		let images   = 0,
			controls = 0,
			fields   = 0,
			noremove = false;
		const els    = document.querySelectorAll( 'img, a, input, textarea, select, button' );
		if ( els.length !== 0 ) {
			els.forEach((el) => {
				let title = el.getAttribute( 'title' );
				if ( el.classList.contains( 'nturl' ) ) {
					// Exempt title attributes from Translate WordPress - Google Language Translator, which uses them as a CSS hook.
					noremove = true;
				}
				if ( title && '' !== title ) {
					switch ( el.tagName ) {
						case 'IMG':
							// If image has alt, remove title. If not, set title as alt.
							let alt = el.getAttribute( 'alt' );
							title = el.getAttribute( 'title' );
							if ( ! alt || '' === alt ) {
								el.setAttribute( 'alt', title );
								el.removeAttribute( 'title' );
							} else {
								if ( title === alt ) {
									el.removeAttribute( 'title' );
								}
							}
							images++;
							break;
						case 'A':
						case 'BUTTON':
							// If link or button has contained text or an img with alt, remove title. Otherwise, set title as aria-label unless element already has aria-label.
							let linkText = wpaElementText(el);
							if ( ! linkText || '' === linkText ) {
								let ariaLabel = el.getAttribute( 'aria-label' );
								if ( ! ariaLabel || '' === ariaLabel ) {
									el.setAttribute( 'aria-label', title );
									if ( ! noremove ) {
										el.removeAttribute( 'title' );
									}
								}
							} else {
								el.removeAttribute( 'title' );
							}
							controls++;
							break;
						case 'INPUT':
						case 'SELECT':
						case 'TEXTAREA':
							// If input field has an aria-label, aria-labelledby, associated label, or wrapping label, remove title. Else, add title as aria-label.
							let ariaLabel        = el.getAttribute( 'aria-label' );
							let ariaLabelled     = el.getAttribute( 'aria-labelledby' );
							let ariaLabeller     = ( ariaLabelled ) ? document.getElementById( ariaLabelled ) : false;
							let labelId          = el.getAttribute( 'id' );
							let label            = ( labelId ) ? document.querySelector( 'label[for="' + labelId + '"]' ) : false;
							let parentLabel      = el.closest( 'label' );
							let hasAriaLabel     = ( ariaLabel && '' !== ariaLabel ) ? true : false;
							let hasRealLabel     = ( label && '' !== wpaElementText( label ) ) ? true : false;
							let hasImplicitLabel = ( parentLabel && '' !== wpaElementText( parentLabel ) ) ? true : false;
							let hasAriaLabelled  = ( ariaLabeller && '' !== wpaElementText( ariaLabeller ) ) ? true : false;
							if ( hasAriaLabel || hasRealLabel || hasImplicitLabel || hasAriaLabelled ) {
								// This has a label.
								el.removeAttribute( 'title' );
							} else {
								el.setAttribute( 'aria-label', title );
								el.removeAttribute( 'title' );
							}
							fields++;
							break;
					}
				}
			});
			if ( wpa.errors || wpa.tracking ) {
				if ( images > 0 ) {
					errors.push( ['images-titles', images] );
					console.log( images + ' title attributes removed from images by WP Accessibility' );
				}
				if ( controls > 0 ) {
					errors.push( ['control-titles', controls] );
					console.log( controls + ' title attributes removed from links and buttons by WP Accessibility' );
				}
				if ( fields > 0 ) {
					errors.push( ['input-titles', fields] );
					console.log( fields + ' title attributes removed from input fields by WP Accessibility' );
				}
			}
		}
	}

	if ( wpa.target ) {
		let targeted      = document.querySelectorAll('a:not(.wpa-allow-target)');
		let targetRemoved = 0;
		if ( targeted.length !== 0 ) {
			targeted.forEach( (el) => {
				let target   = el.getAttribute( 'target' );
				let href     = el.getAttribute( 'href' );

				if ( target ) {
					// For on-page fragments.
					if ( href.startsWith( '#') ) {
						el.removeAttribute( 'target' );
					} else {
						try {
							let url      = new URL( href );
							let hostname = url.hostname;
							if ( ! hostname.includes( 'facebook' ) ) {
								el.removeAttr( 'target' );
								targetRemoved++;
							}
						} catch (exception) {

							// No action; the `href` attribute didn't resolve as a URL.
						}
					}
				}
			});
			if ( targetRemoved > 0 && ( wpa.errors || wpa.tracking ) ) {
				errors.push( ['link-targets', targetRemoved] );
				console.log( targetRemoved + ' target attributes removed from links by WP Accessibility' );
			}
		}
	}

	if ( wpa.tabindex ) {
		// Remove tabindex from elements that should be natively focusable.
		let focusable  = document.querySelectorAll('input,a[href],select,textarea,button');
		let tabRemoved = 0;
		if ( focusable.length !== 0 ) {
			focusable.forEach( (el) => {
				let tabindex = el.getAttribute('tabindex');
				if ( tabindex ) {
					el.removeAttribute('tabindex');
					tabRemoved++;
				}
			});

			if ( tabRemoved > 0 && ( wpa.errors || wpa.tracking ) ) {
				errors.push( ['control-tabindex', tabRemoved] );
				console.log( tabRemoved + ' tabindex attributes removed from links, buttons and inputs by WP Accessibility' );
			}
		}

		// Add tabindex to elements that appear active but are not natively focusable.
		let fakeButtons = document.querySelectorAll( '[role="button"]:not([tabindex]):not(a):not(button)' );
		let buttonLinks = document.querySelectorAll( 'a[role="button"]:not([tabindex]):not([href])');
		let fakeLinks = document.querySelectorAll( '[role="link"]:not([tabindex]):not(a)');
		let linkLinks = document.querySelectorAll( 'a:not([href]):not([tabindex])' );
		if ( fakeButtons.length !== 0 ) {
			fakeButtons.forEach( (el) => {
				el.setAttribute( 'tabindex', '0' );
				el.classList.add('wpa-focusable');
			});
			if ( fakeButtons.length > 0 && ( wpa.errors || wpa.tracking ) ) {
				errors.push( ['button-add-tabindex', fakeButtons.length] );
				console.log( fakeButtons.length + ' tabindex attributes added to divs with the button role by WP Accessibility' );
			}
		}
		if ( buttonLinks !== 0 ) {
			buttonLinks.forEach( (el) => {
				el.setAttribute( 'tabindex', '0' );
				el.classList.add('wpa-focusable');
			});
			if ( buttonLinks.length > 0 && ( wpa.errors || wpa.tracking ) ) {
				errors.push( ['link-add-tabindex', buttonLinks.length] );
				console.log( buttonLinks.length + ' tabindex attributes added to anchor elements with the button role and no href value by WP Accessibility' );
			}
		}
		if ( fakeLinks !== 0 ) {
			fakeLinks.forEach( (el) => {
				el.setAttribute( 'tabindex', '0' );
				el.classList.add('wpa-focusable');
			});
			if ( fakeLinks.length > 0 && ( wpa.errors || wpa.tracking ) ) {
				errors.push( ['fakelink-add-tabindex', fakeLinks.length] );
				console.log( fakeLinks.length + ' tabindex attributes added to elements with the link role not using the a element by WP Accessibility' );
			}
		}
		if ( linkLinks !== 0 ) {
			linkLinks.forEach( (el) => {
				el.setAttribute( 'tabindex', '0' );
				el.classList.add('wpa-focusable');
			});
			if ( linkLinks.length > 0 && ( wpa.errors || wpa.tracking ) ) {
				errors.push( ['links-add-tabindex', linkLinks.length] );
				console.log( linkLinks.length + ' tabindex attributes added to a with no href attribute by WP Accessibility' );
			}
		}
	}

	// If stats are disabled, skip this.
	if ( 'disabled' !== wpa.url ) {
		/*
		* fingerprintJS 0.1 - Fast browser fingerprint library
		* https://github.com/Valve/fingerprintJS
		* Copyright (c) 2013 Valentin Vasilyev (iamvalentin@gmail.com)
		* Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) license.
		* Modified by Joe Dolson 2023.
		*/
		class Fingerprint {
			constructor(hasher) {
				var nativeForEach = Array.prototype.forEach;
				var nativeMap = Array.prototype.map;
				this.each = function (obj, iterator, context) {
					if (obj == null) return;
					if (nativeForEach && obj.forEach === nativeForEach) {
						obj.forEach(iterator, context);
					} else if (obj.length === +obj.length) {
						for (var i = 0, l = obj.length; i < l; i++) {
							if (JSON.stringify(iterator.call(context, obj[i], i, obj)) === '{}') return;
						}
					} else {
						for (var key in obj) {
							if (obj.hasOwnProperty(key)) {
								if (JSON.stringify(iterator.call(context, obj[key], key, obj)) === '{}') return;
							}
						}
					}
				};
				this.map = function (obj, iterator, context) {
					var results = [];
					if (obj == null) return results;
					if (nativeMap && obj.map === nativeMap) return obj.map(iterator, context);
					this.each(obj, function (value, index, list) {
						results[results.length] = iterator.call(context, value, index, list);
					});
					return results;
				};

				if (hasher) {
					this.hasher = hasher;
				}
			}
			get() {
				let keys = [];
				keys.push(navigator.userAgent);
				keys.push([screen.height, screen.width, screen.colorDepth].join('x'));
				keys.push(new Date().getTimezoneOffset());
				keys.push(!!window.sessionStorage);
				keys.push(!!window.localStorage);
				var pluginsString = this.map(navigator.plugins, function (p) {
					var mimeTypes = this.map(p, function (mt) {
						return [mt.type, mt.suffixes].join('~');
					}).join(',');
					return [p.name, p.description, mimeTypes].join('::');
				}, this).join(';');
				keys.push(pluginsString);
				if (this.hasher) {
					return this.hasher(keys.join('###'), 31);
				} else {
					return this.murmurhash3_32_gc(keys.join('###'), 31);
				}
			}
			/**
			 * JS Implementation of MurmurHash3 (r136) (as of May 20, 2011)
			 *
			 * @author <a href="mailto:gary.court@gmail.com">Gary Court</a>
			 * @see http://github.com/garycourt/murmurhash-js
			 * @author <a href="mailto:aappleby@gmail.com">Austin Appleby</a>
			 * @see http://sites.google.com/site/murmurhash/
			 *
			 * @param {string} key ASCII only
			 * @param {number} seed Positive integer only
			 * @return {number} 32-bit positive integer hash
			 */
			murmurhash3_32_gc(key, seed) {
				let remainder, bytes, h1, h1b, c1, c2, k1, i;

				remainder = key.length & 3; // key.length % 4
				bytes = key.length - remainder;
				h1 = seed;
				c1 = 0xcc9e2d51;
				c2 = 0x1b873593;
				i = 0;

				while (i < bytes) {
					k1 =
						((key.charCodeAt(i) & 0xff)) |
						((key.charCodeAt(++i) & 0xff) << 8) |
						((key.charCodeAt(++i) & 0xff) << 16) |
						((key.charCodeAt(++i) & 0xff) << 24);
					++i;

					k1 = ((((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16))) & 0xffffffff;
					k1 = (k1 << 15) | (k1 >>> 17);
					k1 = ((((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16))) & 0xffffffff;

					h1 ^= k1;
					h1 = (h1 << 13) | (h1 >>> 19);
					h1b = ((((h1 & 0xffff) * 5) + ((((h1 >>> 16) * 5) & 0xffff) << 16))) & 0xffffffff;
					h1 = (((h1b & 0xffff) + 0x6b64) + ((((h1b >>> 16) + 0xe654) & 0xffff) << 16));
				}

				k1 = 0;

				switch (remainder) {
					case 3: k1 ^= (key.charCodeAt(i + 2) & 0xff) << 16;
					case 2: k1 ^= (key.charCodeAt(i + 1) & 0xff) << 8;
					case 1: k1 ^= (key.charCodeAt(i) & 0xff);

						k1 = (((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16)) & 0xffffffff;
						k1 = (k1 << 15) | (k1 >>> 17);
						k1 = (((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16)) & 0xffffffff;
						h1 ^= k1;
				}

				h1 ^= key.length;

				h1 ^= h1 >>> 16;
				h1 = (((h1 & 0xffff) * 0x85ebca6b) + ((((h1 >>> 16) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
				h1 ^= h1 >>> 13;
				h1 = ((((h1 & 0xffff) * 0xc2b2ae35) + ((((h1 >>> 16) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
				h1 ^= h1 >>> 16;

				return h1 >>> 0;
			}
		}

		let fingerprint = new Fingerprint().get();

		function logStats(event,fingerprint,type='event') {
			event = JSON.stringify( event );
			const data = new FormData();
			data.append( 'action', wpa.action );
			data.append( 'security', wpa.security );
			data.append( 'stats', event );
			data.append( 'post_id', wpa.post_id );
			data.append( 'title', fingerprint );
			data.append( 'type', type );
			fetch( wpa.ajaxurl, {
				method: 'POST',
				body: data
			});
		}
		let contrastButton = document.querySelector( '.toggle-contrast' );
		if ( contrastButton ) {
			contrastButton.addEventListener('click', function () {
				let event;
				// This fires after the contrast change happens, and the ID is already changed.
				if ( this.getAttribute( 'id' ) == "is_normal_contrast") {
					// high contrast turned on.
					event = {'contrast' : 'disabled'};
				} else {
					// high contrast turned off.
					event = {'contrast' : 'enabled'};
				}
				logStats(event,fingerprint);
			});
		}

		let fontsizeButton = document.querySelector( '.toggle-fontsize' );
		if ( fontsizeButton ) {
			fontsizeButton.addEventListener('click', function () {
				let event;
				// This fires after the fontsize change happens, and the ID is already changed.
				if ( this.getAttribute( 'id' ) == "is_normal_fontsize") {
					// fontsizes turned on.
					event = {'fontsize' : 'disabled'};
				} else {
					// fontsizes turned off.
					event = {'fontsize' : 'enabled'};
				}
				logStats(event,fingerprint);
			});
		}

		wpaWaitForElement('.wpa-ld button').then((el) => {
			el.addEventListener( 'click', function(e) {
				// For descriptions, we aren't concerned about state changes; just usage.
				let visible = ( 'true' === el.getAttribute( 'aria-expanded' ) ) ? true : false;
				if ( visible ) {
					let img      = el.closest( 'div' );
					let image_id = img.getAttribute( 'class' ).replace( 'wpa-ld wp-image-', '' );
					let event    = { 'longdesc' : image_id };
					logStats(event,fingerprint);
				}
			});
		});

		let altButtons = document.querySelectorAll( '.wpa-alt button' );
		if ( altButtons.length > 0 ) {
			altButtons.forEach( (el) => {
				el.addEventListener( 'click', function(e) {
					// For alt text, we aren't concerned about state changes; just usage.
					let visible = ( 'true' === el.getAttribute( 'aria-expanded' ) ) ? true : false;
					if ( visible ) {
						let img      = el.closest( 'div' );
						let image_id = img.getAttribute( 'class' ).replace( 'wpa-alt wp-image-', '' );
						let event    = { 'alttext' : image_id };
						logStats(event,fingerprint);
					}
				});
			});
		}
		if ( wpa.tracking && errors.length >= 1 ) {
			logStats(errors,wpa.url,'view');
		}

	}
	if ( wpa.underline.enabled ) {
		// Underline any link not inside a `nav` region. Using JS for this avoids problems with cascade precedence.
		let targetEls = document.querySelectorAll( wpa.underline.target + ':not(nav ' + wpa.underline.target + '):not(#wpadminbar a), .wpa-focusable[role=link]' );
		if ( targetEls.length > 0 ) {
			targetEls.forEach( (el) => {
				let originalOutline = el.style.outlineWidth;
				let originalOffset  = el.style.outlineOffset;
				let textColor       = el.style.color;
				let originalColor   = el.style.outlineColor;
				el.style.textDecoration = 'underline';
				el.addEventListener( 'mouseenter', function() {
					this.style.textDecoration = 'none';
				});
				el.addEventListener( 'mouseleave', function() {
					this.style.textDecoration = 'underline';
				});
				el.addEventListener( 'focusin', function() {
					let newOutline = '2px';
					if ( originalOutline == '2px' ) {
						newOutline = '4px';
					}
					// Ensure there's a visible change of appearance on hover or focus.
					this.style.outlineWidth = newOutline;
					this.style.outlineColor = textColor;
					this.style.outlineOffset = '2px';
				});
				el.addEventListener( 'focusout', function() {
					// Reset visible appearance on exit.
					this.style.outlineWidth = originalOutline;
					this.style.outlineColor = originalColor;
					this.style.outlineOffset = originalOffset;
				});
			});
		}
	}

	if ( wpa.videos ) {
		// Add a pause/play button to autoplaying videos without controls.
		let motionQuery = matchMedia( '(prefers-reduced-motion)' );
		let initialState = 'false';
		let autoplayVideos = document.querySelectorAll( 'video[autoplay]:not([controls])' );
		if ( autoplayVideos.length > 0 ) {
			autoplayVideos.forEach( (el) => {
				if ( motionQuery.matches ) {
					el.pause();
					initialState = 'true';
				}
				let parentEl    = el.parentElement;
				let pauseButton = document.createElement( 'button' );
				let buttonIcon  = document.createElement( 'span' );
				let buttonText  = document.createElement( 'span' );
				pauseButton.setAttribute( 'type', 'button' );
				pauseButton.classList.add( 'wpa-video' );
				pauseButton.setAttribute( 'aria-pressed', initialState );
				buttonIcon.classList.add( 'dashicons-controls-pause', 'dashicons' );
				buttonIcon.setAttribute( 'aria-hidden', 'true' );
				buttonText.classList.add( 'screen-reader-text' );
				buttonText.innerText = wpa.pause;
				pauseButton.append( buttonIcon, buttonText );
				parentEl.append( pauseButton );
				pauseButton.addEventListener( 'click', function() {
					let pressed = this.getAttribute( 'aria-pressed' );
					console.log( pressed );
					if ( 'true' === pressed ) {
						el.play();
						this.setAttribute( 'aria-pressed', 'false' );
					} else {
						el.pause();
						this.setAttribute( 'aria-pressed', 'true' );
					}
				});
			});
		}
	}

	if ( wpa.alt ) {
		let selector = ( wpa.altSelector ) ? wpa.altSelector : '.hentry img[alt]:not([alt=""]), .comment-content img[alt]:not([alt=""]), #content img[alt]:not([alt=""]),.entry-content img[alt]:not([alt=""])';
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
			let width     = img.width;
			let height    = img.height;
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
	}

	if ( wpa.ldType ) {
		let wrap = (elem, wrapper) => {
			elem.parentElement.insertBefore(wrapper, elem);
			wrapper.appendChild(elem);
		};

		let longDescImgs = document.querySelectorAll( 'img[longdesc]' );
		if ( 'link' === wpa.ldType ) {
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
					wrapper.insertAdjacentHTML( 'beforeend', '<button aria-expanded="false" type="button" class="wpa-toggle">' + wpa.ldText + '</button>');
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
			let classes = img.getAttribute( 'class' );
			if ( null === classes || '' === classes ) {
				parent  = img.closest( '.wpa-alt' );
				classes = parent.getAttribute( 'class' ).replace( 'wpa-alt ', '' );
			}
			let id = classes.replace( 'wp-image-', '' );
			let api = wpa.restUrl + '/' + id;
			fetch( api )
				.then( response => response.json())
				.then(data => {
					let rawdesc = data.description.rendered;
					rawdesc = rawdesc.replace(/(<([^>]+)>)/gi, '').trim();
					if ( '' !== rawdesc ) {
						let url = new URL( wpa.ldHome );
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
				alt = '<code>' + img.getAttribute('src').replace( wpa.ldHome, '' ) + '</code>';
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
			if ( 'link' === wpa.ldType ) {
				wrapper.insertAdjacentHTML( 'beforeend', '<a href="' + url + '" class="longdesc-link">Description<span class="screen-reader-text"> of ' + alt + '</span></a>');
			} else {
				wrapper.insertAdjacentHTML( 'beforeend', '<button aria-expanded="false" class="wpa-toggle">' + wpa.ldText + '</button>');
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
	}
})();

/**
 * Check whether an element contains text, including inspecting contained content for image alt attributes or aria-label attributes.
 *
 * @arg el DOM element to check.
 *
 * Based on work by Roger Johansson https://www.456bereastreet.com/archive/201105/get_element_text_including_alt_text_for_images_with_javascript/
 */
function wpaElementText(el) {
	let text = '';
	// Text node (3) or CDATA node (4) - return its text
	if ( (el.nodeType === 3) || (el.nodeType === 4) ) {
		text = el.nodeValue;
	// If node is an element (1) and an img, input[type=image], or area element, return its alt text
	} else if ( (el.nodeType === 1) && (
			(el.tagName.toLowerCase() == 'img') ||
			(el.tagName.toLowerCase() == 'area') ||
			((el.tagName.toLowerCase() == 'input') && el.getAttribute('type') && (el.getAttribute('type').toLowerCase() == 'image'))
			) ) {
		text = el.getAttribute('alt') || '';
	// Traverse children unless this is a script or style element
	} else if ( (el.nodeType === 1) && !el.tagName.match(/^(script|style)$/i) ) {
		let children = el.childNodes;
		for (let i = 0, l = children.length; i < l; i++) {
			// If an element has an aria-label, that will override any other contained text.
			let ariaLabel = el.getAttribute( 'aria-label' );
			text += ( ariaLabel ) ? ariaLabel : wpaElementText( children[i] ) + ' ';
		}
	}

	return text;
};

/**
 * Wait to see whether an element becomes available.
 *
 * @param {string} selector
 * @returns
 */
function wpaWaitForElement(selector) {
    return new Promise(resolve => {
        if (document.querySelector(selector)) {
            return resolve(document.querySelector(selector));
        }

        const observer = new MutationObserver(mutations => {
            if (document.querySelector(selector)) {
                observer.disconnect();
                resolve(document.querySelector(selector));
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
}