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
		body.prepend( wpa.skiplinks.output );
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
						hasAria   = ( '' == aria || 'undefined' == typeof( aria ) ) ? false : true;
						hasAriaId = ( '' == ariaId || 'undefined' == typeof( ariaId ) ) ? false : true;
						// Add label if aria label empty, aria labelledby empty, or aria reference ID does not exist.
						if ( ( ! hasAria && ! hasAriaId ) || ( ! hasAria && ( hasAriaId && 0 === ariaTarget.length ) ) ) {
							if ( field_id ) {
								label = document.querySelector( 'label[for=' + field_id + ']' );
								labelText = label.innerText;
								if ( label.length && ! labelText ) {
									label.innerText = wpa.wpalabels[value];
									if ( wpa.errors || wpa.tracking ) {
										errors.push( ['empty-label', wpa.wpalabels[value]] );
										console.log( 'Empty label on ' + wpa.wpalabels[value] + ' added by WP Accessibility' );
									}
								}
								if ( !label.length && !implicit.length ) {
									field.insertAdjacentHTML( 'beforebegin', "<label for='" + field_id + "' class='wpa-screen-reader-text'>" + wpa.wpalabels[value] + "</label>" );
									if ( wpa.errors || wpa.tracking ) {
										errors.push( ['explicit-label', wpa.wpalabels[value]] );
										console.log( 'Explicit label on ' + wpa.wpalabels[value] + ' added by WP Accessibility' );
									}
								}
							} else {
								if ( !implicit.length ) {
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
							if ( ! alt || '' === alt ) {
								el.setAttribute( 'alt', title );
								el.removeAttribute( 'title' );
							} else {
								el.removeAttribute( 'title' );
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
							let hasAriaLabelled  = ( ariaLabeller && '' !== wpaElementText( arialabeller ) ) ? true : false;
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
		let fakeButtons = document.querySelectorAll( '[role="button"]:not([tabindex]):not(a)' ); // $('div[role="button"]').not('div[tabindex]' );
		let buttonLinks = document.querySelectorAll( 'a[role="button"]:not([tabindex]):not([href])'); // $('a[role="button"]').not('a[tabindex],a[href]');
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
	}

	// If stats are disabled, skip this.
	if ( 'disabled' !== wpa.url ) {
		let fingerprint = new Fingerprint().get();

		function logStats(event,fingerprint,type='event') {
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

		let fontsizeButton = document.querySelector( '.toggle-contrast' );
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

		waitForElement('.wpa-ld button').then((el) => {
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
		let targetEls = document.querySelectorAll( wpa.underline.target + ':not(nav ' + wpa.underline.target + ')' );
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
				el.addEventListener( 'mouseleve', function() {
					this.style.textDecoration = 'none';
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
function waitForElement(selector) {
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