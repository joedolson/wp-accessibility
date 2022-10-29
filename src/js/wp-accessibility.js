(function( $ ) { 'use strict';
	var html   = document.querySelector( 'html' );
	if ( wpa.lang ) {
		var lang   = html.getAttribute( 'lang' );
		if ( ! lang ) {
			$('html').attr( 'lang', wpa.lang );
			if ( wpa.errors ) {
				console.log( 'HTML language set by WP Accessibility' );
			}
		}
	}

	if ( wpa.dir ) {
		var dir  = html.getAttribute( 'dir' );
		if ( ! dir ) {
			$('html').attr( 'dir', wpa.dir );
			if ( wpa.errors ) {
				console.log( 'HTML language direction set by WP Accessibility' );
			}
		}
	}

	var viewport = document.querySelector( 'meta[name="viewport"]' );
	if ( viewport ) {
		var conditionsBefore = viewport.getAttribute( 'content' );
		var conditionsAfter  = viewport.getAttribute( 'content' );
		if ( conditionsBefore.search(/user-scalable=no/g) ) {
			conditionsAfter = conditionsBefore.replace( 'user-scalable=no', 'user-scalable=yes' );
			viewport.setAttribute( 'content', conditionsAfter );
			if ( wpa.errors && conditionsAfter != conditionsBefore ) {
				console.log( 'Viewport made scalable by WP Accessibility' );
			}
		}
		if ( conditionsBefore.search(/maximum-scale=1/g) ) {
			conditionsAfter = conditionsBefore.replace( 'maximum-scale=1', 'maximum-scale=5' );
			conditionsAfter = conditionsAfter.replace( 'maximum-scale=0', 'maximum-scale=5' );
			viewport.setAttribute( 'content', conditionsAfter );
			if ( wpa.errors && conditionsAfter != conditionsBefore  ) {
				console.log( 'Viewport maximum scale set by WP Accessibility' );
			}
		}
	}

	if ( wpa.skiplinks.enabled ) {
		$('body').prepend( wpa.skiplinks.output );
		if ( wpa.errors ) {
			console.log( 'Skip links added by WP Accessibility' );
		}
	}

	if ( wpa.current ) {
		$(function() {
			$( '.current-menu-item a, .current_page_item a' ).attr( 'aria-current', 'page' );
		});
		if ( wpa.errors ) {
			console.log( 'ARIA current added by WP Accessibility' );
		}
	}

	if ( wpa.labels ) {
		var wpa_names = [ 's', 'author', 'email', 'url', 'comment' ];
		$.each( wpa_names, function( index, value ) {
			if ( value == 'comment' ) {
				var field = $( 'textarea[name=' + value + ']' );
			} else {
				var field = $( 'input[name=' + value + ']' ).not( '#adminbar-search' );
			}
			if ( 0 !== field.length ) {
				var field_id = field.attr( 'id' );
				var implicit = $( field ).parent( 'label' );
				var aria = $( field ).attr( 'aria-label' );
				var ariaId = $( field ).attr( 'aria-labelledby' );
				var ariaTarget = {};
				if ( ariaId ) {
					ariaTarget = $( '#' + ariaId );
				}
				var hasAria   = ( '' == aria || 'undefined' == typeof( aria ) ) ? false : true;
				var hasAriaId = ( '' == ariaId || 'undefined' == typeof( ariaId ) ) ? false : true;
				// Add label if aria label empty, aria labelledby empty, or aria reference ID does not exist.
				if ( ( ! hasAria && ! hasAriaId ) || ( ! hasAria && ( hasAriaId && 0 === ariaTarget.length ) ) ) {
					if ( field_id ) {
						var label = $( 'label[for=' + field_id + ']' );
						if ( !label.length && !implicit.length ) {
							field.before( "<label for='" + field_id + "' class='wpa-screen-reader-text'>" + wpa.wpalabels[value] + "</label>" );
							if ( wpa.errors ) {
								console.log( 'Explicit label on ' + wpa.wpalabels[value] + 'added by WP Accessibility' );
							}
						}
					} else {
						if ( !implicit.length ) {
							field.attr( 'id', 'wpa_label_' + value ).before( "<label for='wpa_label_" + value + "' class='wpa-screen-reader-text'>" + wpa.wpalabels[value] + "</label>" );
							if ( wpa.errors ) {
								console.log( 'Implicit label on ' + wpa.wpalabels[value] + 'added by WP Accessibility' );
							}
						}
					}
				}
			}
		});
	}


	if ( wpa.titles ) {
		var images   = 0;
		var controls = 0;
		var fields   = 0;
		const els    = document.querySelectorAll( 'img, a, input, textarea, select, button' );
		els.forEach((el) => {
			var title = el.getAttribute( 'title' );
			if ( title && '' !== title ) {
				switch ( el.tagName ) {
					case 'IMG':
						// If image has alt, remove title. If not, set title as alt.
						var alt = el.getAttribute( 'alt' );
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
						var linkText = wpaElementText(el);
						if ( ! linkText || '' === linkText ) {
							var ariaLabel = el.getAttribute( 'aria-label' );
							if ( ! ariaLabel || '' === ariaLabel ) {
								el.setAttribute( 'aria-label', title );
								el.removeAttribute( 'title' );
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
						var ariaLabel        = el.getAttribute( 'aria-label' );
						var ariaLabelled     = el.getAttribute( 'aria-labelledby' );
						var ariaLabeller     = ( ariaLabelled ) ? document.getElementById( ariaLabelled ) : false;
						var labelId          = el.getAttribute( 'id' );
						var label            = ( labelId ) ? document.querySelector( 'label[for="' + labelId + '"]' ) : false;
						var parentLabel      = el.closest( 'label' );
						var hasAriaLabel     = ( ariaLabel && '' !== ariaLabel ) ? true : false;
						var hasRealLabel     = ( label && '' !== wpaElementText( label ) ) ? true : false;
						var hasImplicitLabel = ( parentLabel && '' !== wpaElementText( parentLabel ) ) ? true : false;
						var hasAriaLabelled  = ( ariaLabeller && '' !== wpaElementText( arialabeller ) ) ? true : false;
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
		if ( wpa.errors ) {
			if ( images > 0 ) {
				console.log( images + ' title attributes removed from images by WP Accessibility' );
			}
			if ( controls > 0 ) {
				console.log( controls + ' title attributes removed from links and buttons by WP Accessibility' );
			}
			if ( fields > 0 ) {
				console.log( fields + ' title attributes removed from input fields by WP Accessibility' );
			}
		}
	}

	if ( wpa.target ) {
		var targeted      = $('a:not(.wpa-allow-target)');
		var targetRemoved = 0;
		targeted.each( function() {
			var target = $( this ).attr( 'target' );
			if ( target ) {
				$( this ).removeAttr( 'target' );
				targetRemoved++;
			}
		});
		if ( targetRemoved > 0 && wpa.errors ) {
			console.log( targetRemoved + ' target attributes removed from links by WP Accessibility' );
		}
	}

	if ( wpa.tabindex ) {
		// Remove tabindex from elements that should be natively focusable.
		var focusable  = $('input,a,select,textarea,button').not('a:not([href])');
		var tabRemoved = 0;
		focusable.each( function() {
			var tabindex = $( this ).attr( 'tabindex' );
			if ( tabindex ) {
				$( this ).removeAttr('tabindex');
				tabRemoved++;
			}
		});

		if ( tabRemoved > 0 && wpa.errors ) {
			console.log( tabRemoved + ' tabindex attributes removed from links, buttons and inputs by WP Accessibility' );
		}

		// Add tabindex to elements that appear active but are not natively focusable.
		var fakeButtons = $('div[role="button"]').not('div[tabindex]' );
		var buttonLinks = $('a[role="button"]').not('a[tabindex],a[href]');
		fakeButtons.attr( 'tabindex', '0' ).addClass('wpa-focusable');
		if ( fakeButtons.length > 0 && wpa.errors ) {
			console.log( fakeButtons.length + ' tabindex attributes added to divs with the button role by WP Accessibility' );
		}
		buttonLinks.attr( 'tabindex', '0' ).addClass('wpa-focusable');
		if ( buttonLinks.length > 0 && wpa.errors ) {
			console.log( buttonLinks.length + ' tabindex attributes added to anchor elements with the button role and no href value by WP Accessibility' );
		}
	}

	if ( wpa.underline.enabled ) {
		// Underline any link not inside a `nav` region. Using JS for this avoids problems with cascade precedence.
		var originalOutline = $( wpa.underline.target ).css( 'outline-width' );
		var originalOffset  = $( wpa.underline.target ).css( 'outline-offset' );
		var textColor       = $( wpa.underline.target ).css( 'color' );
		var originalColor   = $( wpa.underline.target ).css( 'outline-color' );
		$( wpa.underline.target ).not( 'nav ' + wpa.underline.target ).css( 'text-decoration', 'underline' );

		$( wpa.underline.target ).on( 'mouseenter', function() {
			$( this ).css( 'text-decoration', 'none' );
		});
		$(  wpa.underline.target ).on( 'mouseleave', function() {
			// Reset visible appearance on exit.
			$( this ).css( 'text-decoration', 'underline' );
		});

		$( wpa.underline.target ).on( 'focusin', function() {
			var newOutline = '2px';
			if ( originalOutline == '2px' ) {
				newOutline = '4px';
			}
			// Ensure there's a visible change of appearance on hover or focus.
			$(this).css( 'outline-width', newOutline );
			$(this).css( 'outline-color', textColor );
			$(this).css( 'outline-offset', '2px' );
		});
		$(  wpa.underline.target ).on( 'focusout', function() {
			// Reset visible appearance on exit.
			$(this).css( 'outline-width', originalOutline );
			$(this).css( 'outline-color', originalColor );
			$(this).css( 'outline-offset', originalOffset );
		});
	}
}(jQuery));

/**
 * Check whether an element contains text, including inspecting contained content for image alt attributes or aria-label attributes.
 *
 *
 * @arg el DOM element to check.
 *
 * Based on work by Roger Johansson https://www.456bereastreet.com/archive/201105/get_element_text_including_alt_text_for_images_with_javascript/
 */
function wpaElementText(el) {
	var text = '';
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
		var children = el.childNodes;
		for (var i = 0, l = children.length; i < l; i++) {
			// If an element has an aria-label, that will override any other contained text.
			var ariaLabel = el.getAttribute( 'aria-label' );
			text += ( ariaLabel ) ? ariaLabel : wpaElementText( children[i] ) + ' ';
		}
	}

	return text;
};