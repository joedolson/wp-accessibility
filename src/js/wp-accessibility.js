(function( $ ) { 'use strict';

	var html = document.querySelector( 'html' );
	var lang = html.getAttribute( 'lang' );
	if ( ! lang ) {
		$('html').attr( 'lang', wpa.lang );
	}
	var dir  = html.getAttribute( 'dir' );
	if ( ! dir ) {
		$('html').attr( 'dir', wpa.dir );
	}

	var viewport = document.querySelector( 'meta[name="viewport"]' );
	if ( viewport ) {
		var conditions = viewport.getAttribute( 'content' );
		if ( conditions.search(/user-scalable=no/g) ) {
			conditions = conditions.replace( 'user-scalable=no', 'user-scalable=yes' );
			viewport.setAttribute( 'content', conditions );
		}
		if ( conditions.search(/maximum-scale=1/g) ) {
			conditions = conditions.replace( 'maximum-scale=1', 'maximum-scale=5' );
			conditions = conditions.replace( 'maximum-scale=0', 'maximum-scale=5' );
			viewport.setAttribute( 'content', conditions );
		}
	}

	if ( wpa.skiplinks.enabled ) {
		$('body').prepend( wpa.skiplinks.output );
	}

	if ( wpa.current ) {
		$(function() {
			$( '.current-menu-item a, .current_page_item a' ).attr( 'aria-current', 'page' );
		});
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
				var form_id = field.attr( 'id' );
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
					if ( hasAriaId && 0 === ariaTarget.length ) {
						console.log( 'aria-labelledby target ID does not exist: ', ariaId );
					}
					if ( form_id ) {
						var label = $( 'label[for=' + form_id + ']' );
						if ( !label.length && !implicit.length ) {
							field.before( "<label for='" + form_id + "' class='wpa-screen-reader-text'>" + wpa.wpalabels[value] + "</label>" );
						}
					} else {
						if ( !implicit.length ) {
							field.attr( 'id', 'wpa_label_' + value ).before( "<label for='wpa_label_" + value + "' class='wpa-screen-reader-text'>" + wpa.wpalabels[value] + "</label>" );
						}
					}
				}
			}
		});
	}


	if ( wpa.titles ) {
		const els = document.querySelectorAll( 'img, a, input, textarea, select, button' );
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
						break;
				}
			}
		});

	}

	if ( wpa.target ) {
		$('a:not(.wpa-allow-target)').removeAttr( 'target' );
	}

	if ( wpa.tabindex ) {
		// Remove tabindex from elements that should be natively focusable.
		var focusable = $('input,a,select,textarea,button').not('a:not([href])');
		focusable.removeAttr('tabindex');

		// Add tabindex to elements that appear active but are not natively focusable.
		var fakeButtons = $('div[role="button"]').not('div[tabindex]' );
		var buttonLinks = $('a[role="button"]').not('a[tabindex],a[href]');
		fakeButtons.attr( 'tabindex', '0' ).addClass('wpa-focusable');
		buttonLinks.attr( 'tabindex', '0' ).addClass('wpa-focusable');
	}

	if ( wpa.underline.enabled ) {
		$( wpa.underline.target ).not( 'nav ' + wpa.underline.target ).css( 'text-decoration','underline' );
		$(  wpa.underline.target ).on( 'focusin mouseenter', function() {
			$(this).css( 'text-decoration','none' );
		});
		$(  wpa.underline.target ).on( 'focusout mouseleave', function() {
			$(this).css( 'text-decoration','underline' );
		});
	}
}(jQuery));

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
			text += wpaElementText(children[i]);
		}
	}

	return text;
};