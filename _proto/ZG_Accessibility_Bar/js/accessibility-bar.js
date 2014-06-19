/**
 * Accessibility.js
 * Enables the Zeitguys accessibility bar, including skip-links, menu tabbing 
 * and font resizing.
 * Note that for font resizing to function, classes matching the 
 * FONT_RESIZE_FULL_CLASS and optionally FONT_RESIZE_SCALED_CLASS must be 
 * leveraged in parent HTML elements.
 * They must ALSO be defined within the <head> tag of the HTML document, 
 * preferably pre-initializing using the value stored in the cookie,
 * defined by the COOKIE_PREFIX + WP.COOKIE_FONT_BASE_SIZE or 
 * WP.COOKIE_FONT_SCALED_SIZE
 *
 * @TODO Before we do any scaling, we need to scan the current (unscaled) sizes,
 *		 and record them. This allows us to reset our font size correctly rather
 *		 than setting things to 1em. That breaks any element that has their ems 
 *		 explicitly set, which is a Bad Thing.
 * 
 * @TODO Changes were made in TOBIUS that need to be brought over.
 * 
 * @TODO High Contrast Mode Limitations 
 *		 a) All box elements are given a high contrast background color. If 
 *		    elements have a background image and its child does not and it covers
 *		    the parent, the parent's background image will be covered by the 
 *		    child's high contrast background color. Perhaps check all 
 *		    elements without a background color applied and make sure they have a 
 *		    transparent background color. 
 *		 b) Grayscaling images will only work if images are hosted in the same 
 *			domain. using images from other domains with render an error message:
			"Canvas tainted by cross-origin data"
 *		 
 */

var max_height = 70,
    hidden_height = 24;

( function( $ ){
	/**
	 * Shared vars between PHP and JS set by localize_script()
	 *
	 * @type object {
	 *		COOKIE_HIDE_ACCESSIBILITY_BAR,
			COOKIE_FONT_BASE_SIZE,
			COOKIE_FONT_SCALED_SIZE,
			COOKIE_EXPIRY_LENGTH,
			COOKIE_HIGH_CONTRAST_MODE,
			HIGH_CONTRAST_BG_COLOR,
			BAR_INITIALLY_CLOSED,
			FONT_MAX_SIZE,
			FONT_MIN_SIZE,
			FONT_SCALE_FACTOR,
			FONT_SCALE_INCREMENT,
			COLLAPSED_CLASS
	 */
	var WP = ZG_ACCESSIBILITY_BAR;

	// Set this to true if you want the accessibility bar to start off closed. Otherwise it will start off open. Open is the recommended setting.
	var INITIALLY_CLOSED = false;

	// Change this to reflect the ID or class of the pull-down menus you wish to become tab-accessible (you can use commas if you want to specify more than 1)
	var ACCESSIBLE_MENU_SELECTOR = '#menu-main-menu';
	// Change this to reflect the ID or class of the accessibility bar (or leave blank)
	var ACCESSIBILITY_BAR_SELECTOR = '#accessibilityBar';

	// This defines the ID or class assigned to the expand/collapse widget
	var SHOW_SELECTOR = '#accessibility-bar-expand';
	var HIDE_SELECTOR = '#accessibility-bar-collapse';

	// This css class identifies an anchor tag as a "skip link" for accessibility and generally takes the user to
	// another named anchor on the same page (though it doesn't have to)
	var SKIP_LINK_CLASS = 'skip-link';


	// This should be a selector for the Font Size widget (leave blank if there are none)
	var FONT_SIZE_WIDGET_SELECTOR = '#controlsMenu';
	// These correspond to the CSS classes or IDs of the various controls on the Font Size widget
	var INCREASE_FONT_SELECTOR = '#textLarge';
	var DECREASE_FONT_SELECTOR = '#textSmall';
	var RESET_FONT_SELECTOR = '#textReload';
	
	//This corresponds to the CSS class or ID of the High Contrast Mode Toggle.
	var HIGH_CONTRAST_TOGGLE = '.contrastToggle';
	
	var HIGH_CONTRAST_ON    = 'high_contrast_on';
	var HIGH_CONTRAST_OFF   = 'high_contrast_off';
	var HIGH_CONTRAST_CLASS = 'high-contrast-mode';
	
	// These are the css classes that are controlled by the Font Size Widget
	var FONT_RESIZE_FULL_CLASS = '.accessibility-resize-full'; // font size is scaled at 100%
	var FONT_RESIZE_SCALED_CLASS = '.accessibility-resize-scaled'; // font size is scaled at WP.FONT_SCALE_FACTOR

	// This css class can be used to highlight a selected button
	var SELECTED_BUTTON_CLASS = 'button-active';

	// CONSTANTS - don't change beyond this point
	var currentFontSizeEms = 1;
	var currentScaledSizeEms = 1;

	$( document ).ready( function() {
		prepareMenu( ACCESSIBLE_MENU_SELECTOR );
		initializeAccessibilityBar( ACCESSIBILITY_BAR_SELECTOR );
		initializeFontSizeControls( FONT_SIZE_WIDGET_SELECTOR );
		initializeAccessKeys();
		initializeHighConstrastMode();
	} );

	/**
	 * Converts an existing menu (UL + LI) into an accessible one
	 * with keyboard controls
	 * @param {String|Object} menuElement a dom element, a jQuery CSS selector, or a jQuery object to be processed
	 * @return bool success
	 */
	function prepareMenu(menuElement){
		if ($(menuElement).length){
			var item = $('li > a', $(menuElement));
			item.focus(function(event){
				$(this).parents('ul > li').addClass('focus-item');
			});
			item.blur(function(event){
				$(this).parents('ul > li').removeClass('focus-item');
			});
            return true;
		} else {
            return false;
        }
	}
	
	/**
	 * Initiate the Accessibility Bar
	 * 
	 */
	function initializeAccessibilityBar( barElement ){
		$( SHOW_SELECTOR ).click( revealAccessibilityBar );
		$( HIDE_SELECTOR ).click( hideAccessibilityBar );
	}

	/**
	 * Initialize the Font Size Controls
	 * 
	 * @param
	 * @return 
	 */
	function initializeFontSizeControls(fontSizeWidgetElement){
		var $fontSizeWidget = $( fontSizeWidgetElement );
		if ($fontSizeWidget.length){
			// set up the controls
			$(INCREASE_FONT_SELECTOR, $fontSizeWidget).click(increaseFontSize);
			$(DECREASE_FONT_SELECTOR, $fontSizeWidget).click(decreaseFontSize);
			$(RESET_FONT_SELECTOR, $fontSizeWidget).click(resetFontSize);
		}

		if ($.cookie(WP.COOKIE_FONT_BASE_SIZE))
			currentFontSizeEms = parseFloat($.cookie(WP.COOKIE_FONT_BASE_SIZE));
		if ($.cookie(WP.COOKIE_FONT_SCALED_SIZE))
			currentScaledSizeEms = parseFloat($.cookie(WP.COOKIE_FONT_SCALED_SIZE));
		//console.log(currentFontSizeEms, currentScaledSizeEms);

		// To prevent any kind of "jump" in font size when the page loads, make sure to also use
		// the cookie value in a style embedded on the page in PHP.
		setFontSize(currentFontSizeEms, currentScaledSizeEms);
	}
	
		function initializeAccessKeys(){
		// Ensure that clicking on an inline link also moves the focus to the closest
		// link to the named anchor you linked to (replicate the default behaviour of Mozilla)
		if ($.browser.webkit || ($.browser.msie && parseInt($.browser.version) < 9)){
			// only process anchors that have the Skip Link class
			$('a.'+SKIP_LINK_CLASS).each(function(){
				// only process anchors with an inline link target
				var inlineLink = /^#(.+)/.exec($(this).attr('href'));
				if (inlineLink){
					$(this).bind("click", {target: inlineLink[1]}, function(event){
						// Look for elements with that ID first (WAI-ARIA specification)
						// or if not, use the "name" attribute instead
						var target = $('#'+event.data.target + ', [name="'+event.data.target + '"]').first();

						if (target.length){
							// we've found it, so focus it!
							if (target.attr('href')){
								target.focus();
							} else {
								// Nope, it's either not an anchor element, or it's just a named anchor with no actual link
								// so let's find the next link and focus that instead
								var nextTarget = target.find('a').first();
								if (nextTarget.length == 0) nextTarget = target.nextAll('a').first();
								if (nextTarget.length == 0) nextTarget = target.nextAll().find('a').first();
								nextTarget.focus();
							}
						}
					});
				}
			});
		}

		// Build the keycode title for each browser. Mozilla uses Alt+Shift where the others use Alt only. Sigh.
		if ($.browser.mozilla){
			$('a.'+SKIP_LINK_CLASS).each(function(){
				$(this).attr('title', $(this).attr('title').replace(/\bAlt/, 'Alt+Shift'));
			});
		}

		// Add a .focus class to elements that gain focus for IE 6 and 7
		if ($.browser.msie && parseInt($.browser.version) < 8){
			$('a').focus(function(){$(this).addClass('focus')});
			$('a').blur(function(){$(this).removeClass('focus')});
		}
	}

	function revealAccessibilityBar( event ){
		var $accessibilityBar = $( ACCESSIBILITY_BAR_SELECTOR );

		$.cookie( WP.COOKIE_HIDE_ACCESSIBILITY_BAR, false, {
            expires: parseInt( WP.COOKIE_EXPIRY_LENGTH ), path: '/'
        } );

		$accessibilityBar.removeClass( WP.COLLAPSED_CLASS )
		$( SHOW_SELECTOR ).removeClass( WP.COLLAPSED_CLASS )
		$( HIDE_SELECTOR ).removeClass( WP.COLLAPSED_CLASS )

		event.preventDefault();
	}

	function hideAccessibilityBar( event ){
		var $accessibilityBar = $( ACCESSIBILITY_BAR_SELECTOR );

		$.cookie( WP.COOKIE_HIDE_ACCESSIBILITY_BAR, true, {
            expires: parseInt( WP.COOKIE_EXPIRY_LENGTH ), path: '/'
        } );

		$accessibilityBar.addClass( WP.COLLAPSED_CLASS )
		$( SHOW_SELECTOR ).addClass( WP.COLLAPSED_CLASS )
		$( HIDE_SELECTOR ).addClass( WP.COLLAPSED_CLASS )

		event.preventDefault();
	}

	function increaseFontSize(event){
		changeFontSize( +( WP.FONT_SCALE_INCREMENT ), event );
	}

	function decreaseFontSize(event){
		event.preventDefault();
		changeFontSize( -( WP.FONT_SCALE_INCREMENT ), event );
	}

	function resetFontSize(event){
		changeFontSize( 0, event );
	}

	function changeFontSize(delta, event ){
		// stop the page from jumping
		event.preventDefault();
		if (delta === 0) {
			currentFontSizeEms = 1;
			currentScaledSizeEms = 1;
		} else {
			if ((delta > 0 && currentFontSizeEms < WP.FONT_MAX_SIZE) || (delta < 0 && currentFontSizeEms > WP.FONT_MIN_SIZE )){
				currentFontSizeEms += delta;
				currentScaledSizeEms += (delta * WP.FONT_SCALE_FACTOR);
			}
		}

		setFontSize(currentFontSizeEms, currentScaledSizeEms);
	}

	function setFontSize(fontSizeEms, scaledSizeEms){
		// This only works if we don't have an !important rider
		//$(FONT_RESIZE_FULL_CLASS).css('font-size', fontSizeEms + 'em');
		//$(FONT_RESIZE_SCALED_CLASS).css('font-size', scaledSizeEms + 'em');

		// Use when we have an !important rider.
		$(FONT_RESIZE_FULL_CLASS).attr('style', 'font-size:' + fontSizeEms + 'em !important;');
		$(FONT_RESIZE_SCALED_CLASS).attr('style', 'font-size:' + scaledSizeEms + 'em !important;');

		// Highlight the appropriate font button
		var $increaseFontButton = $( INCREASE_FONT_SELECTOR, FONT_SIZE_WIDGET_SELECTOR );
		if ( $increaseFontButton.length ){
			if ( fontSizeEms > 1)
				$increaseFontButton.addClass( SELECTED_BUTTON_CLASS );
			else
				$increaseFontButton.removeClass( SELECTED_BUTTON_CLASS );
		}

		$.cookie(WP.COOKIE_FONT_BASE_SIZE, fontSizeEms, { path: '/', expires: parseInt( WP.COOKIE_EXPIRY_LENGTH ) });
		$.cookie(WP.COOKIE_FONT_SCALED_SIZE, scaledSizeEms, { path: '/', expires: parseInt( WP.COOKIE_EXPIRY_LENGTH ) });
	}
	
	// High Constrast Mode Stuff
	
	// FYI: Grayscaling background images is disabled until its fully debugged.
	
	// Notes - grayscaling images will only work if images are hosted in the same
	//		  domain. using images from other domains with render an error message:
	//		  "Canvas tainted by cross-origin data"
	
	function initializeHighConstrastMode(){
		//check to see if high contrast cookie is set to on,  if so update the id
		if(  $.cookie( WP.COOKIE_HIGH_CONTRAST_MODE ) == 'on' ) {
			$( HIGH_CONTRAST_TOGGLE ).attr( 'id', HIGH_CONTRAST_ON );
			grayscaleEverything();
		}
		$( HIGH_CONTRAST_TOGGLE ).on( 'click', toggleHighContrast );
	}
	
	function toggleHighContrast(){
		// by default the toggle button has an id='high-constrast-on'
		if( $( this ).attr('id') == HIGH_CONTRAST_OFF ) {
			$('body').addClass( HIGH_CONTRAST_CLASS );
			$( this ).attr( 'id', HIGH_CONTRAST_ON );
			grayscaleEverything();
			
			$.cookie( WP.COOKIE_HIGH_CONTRAST_MODE, 'on', { path: '/', expires: parseInt( WP.COOKIE_EXPIRY_LENGTH ) });
		} else {
			$('body').removeClass( HIGH_CONTRAST_CLASS );
			$( this ).attr( 'id', HIGH_CONTRAST_OFF );
			revertImages();
			$.cookie( WP.COOKIE_HIGH_CONTRAST_MODE, 'off', { path: '/', expires: parseInt( WP.COOKIE_EXPIRY_LENGTH ) });
		}	
	}
	
	function grayscaleEverything() {
		// grayscales the inline images
		grayscaleImages();
		
		// grayscales the background images (work in progress)
		// grayscaleBgImages();
	}	
	
	// turns images into a canvas and then greyscales them
	function grayscaleImages() {
		//console.log('grayscaled Images');
		$('img, canvas').each(function(){
			Caman(this, function () {
				this.greyscale().render(showImages);	
			});
		});
	}	
	
/*   Grayscale BG Images - Work in Progress, disabled ATM
*******************************************************************************/ 
//	
//	function grayscaleBgImages() {
//		
//		// finds elements with bg images and stores it in an object
//		console.log('grayscaled BG Images');
//		var elementsWithBgImages = $('*').filter(function() {
//			if (this.currentStyle) { 
//				return this.currentStyle['backgroundImage'] !== 'none';
//		    } else if (window.getComputedStyle) {
//				return document.defaultView.getComputedStyle(this,null).getPropertyValue('background-image') !== 'none';
//		    }
//		});
//		
//		// iterates through the object created above and grabs the url of the bg 
//		// image.
//		$( elementsWithBgImages ).each(function( index ){
//			
//			// object with all the elements with a bg image
//			var $this = $( this );
//			// original bg image url stored
//			var origBG = $this.attr( 'data-orig-bg' );
//			var grayBG = $this.attr( 'data-gray-bg' );
//						
//			if( $this.attr( 'data-gray' ) == 'on' ){
//				$this.css( 'background-image', 'url("' +origBG +'")' );
//				//console.log('switching background back to original');
//				$this.attr( 'data-gray', 'off');
//				//console.log('orig is back on');
//			} else if( $this.attr( 'data-gray' ) == 'off' ) {
//				$this.css( 'background-image', 'url("'+grayBG+'")' );
//				$this.attr( 'data-gray', 'on');
//				//console.log('gray is back on');
//			} else {
//				
//				// creates a canvas for each of the elements with bg image.
//
//				// grabs the background image Url
//				var bgImage = $this.css( 'background-image' );
//				//strips the url('  ') from the string
//				bgImage = bgImage.replace( /^url\(["']?/, '').replace(/["']?\)$/, '' );
//
//				$this.attr('data-orig-bg', bgImage );
//				$this.attr( 'data-gray', 'on');
//				//creates the canvas of the background image to be modified.
//				var $canvas = $( '<canvas id="grayBG-' + index + '" class="greyBG"></canvas>') ;
//				//appends the canvas to the end of the body just before the </body>
//				$this.append( $canvas );
//				//renders the canvas with the filters.
//								
//				Caman( '#grayBG-'+index, bgImage, function () {
//					this.greyscale();
//					// adds a semi transparent black layer over the image.  this helps
//					// give more contrast against the text.
//					this.newLayer(function () {
//						this.opacity(85);
//						this.fillColor( WP.HIGH_CONTRAST_BG_COLOR );
//					});
//					this.render(function () {
//
//						//console.log(this.imageUrl);
//						//converts the canvas to a base64 string so it can be 
//						//inserted back into the css in order to keep the same 
//						//positioning
//						var img = this.toBase64();
//						$this.attr('data-gray-bg', img );
//						$this.css( 'background-image', 'url("'+img+'")' );
//						$('.greyBG').remove();
//					});
//				});
//				//console.log(bgImage);					
//			}			
//		});
//	}
	
	// Resets images back to their original colors.
	function revertImages() {
		console.log('reverted');
		$('img, canvas').each(function(){
			Caman(this, function () {
				this.reset();	
			});
		});
		
		//grayscaleBgImages();
	}
	//  Displays the newly grayscaled images (hidden on generation), to avoid flicker */
	function showImages() {
		$( 'canvas, img').css( 'visibility', 'visible' );
	}
			
})(jQuery);

/* New stuff from Eric?
 *  - CRICH-based stuff, for hiding extra long titles. May see this with
 *  long/nested page titles */
(function ($) {
	var bar, height;
	function hide_the_rest( the_delay ) {
		//alert('out');
		if (the_delay === undefined)
			the_delay = 0;
		bar.children('.crumb').delay(the_delay).animate({'height': hidden_height}, 'fast' );
		if (!$.browser.msie)
			bar.animate({backgroundPosition: '50% 100%'}, 'fast' );
		else if ($.browser.msie && $.browser.version > 8)
			bar.animate({backgroundPositionY: '100%'}, 'fast' );
	}
	function show_the_rest() {
		//alert('in');
		bar.children('.crumb').animate({'height': height}, 'fast' );
		if (!$.browser.msie)
			bar.animate({backgroundPosition: '50% 120%'}, 'fast' );
		else if ($.browser.msie && $.browser.version > 8)
			bar.animate({backgroundPositionY: '120%'}, 'fast' );
	}

	$(window).load( function () {
		bar = $('#accessibilityBar')
		if( bar.outerHeight() > max_height ) {
			//bar.css({'overflow': 'hidden'});
			bar.addClass('extra-content');
			height = bar.children('.crumb').height();
			bar.children('.crumb').css({
				'overflow': 'hidden',
				'height': height,
				'position': 'relative'
			});
			hide_the_rest( 800 );
			bar.hover( show_the_rest, hide_the_rest );
		}
	});

});//(jQuery);

(function($) {
if(!document.defaultView || !document.defaultView.getComputedStyle){
    var oldCSS = jQuery.css;
    jQuery.css = function(elem, name, force){
        if(name === 'background-position'){
            name = 'backgroundPosition';
        }
        if(name !== 'backgroundPosition' || !elem.currentStyle || elem.currentStyle[ name ]){
            return oldCSS.apply(this, arguments);
        }
        var style = elem.style;
        if ( !force && style && style[ name ] ){
            return style[ name ];
        }
        return oldCSS(elem, 'backgroundPositionX', force) +' '+ oldCSS(elem, 'backgroundPositionY', force);
    };
}

var oldAnim = $.fn.animate;
$.fn.animate = function(prop){
    if('background-position' in prop){
        prop.backgroundPosition = prop['background-position'];
        delete prop['background-position'];
    }
    if('backgroundPosition' in prop){
        prop.backgroundPosition = '('+ prop.backgroundPosition + ')';
    }
    return oldAnim.apply(this, arguments);
};

function toArray(strg){
    strg = strg.replace(/left|top/g,'0px');
    strg = strg.replace(/right|bottom/g,'100%');
    strg = strg.replace(/([0-9\.]+)(\s|\)|$)/g,"$1px$2");
    var res = strg.match(/(-?[0-9\.]+)(px|\%|em|pt)\s(-?[0-9\.]+)(px|\%|em|pt)/);
    return [parseFloat(res[1],10),res[2],parseFloat(res[3],10),res[4]];
}

$.fx.step.backgroundPosition = function(fx) {
    if (!fx.bgPosReady) {
        var start = $.css(fx.elem,'backgroundPosition');

        if(!start){//FF2 no inline-style fallback
            start = '0px 0px';
        }

        start = toArray(start);

        fx.start = [start[0],start[2]];

        var end = toArray(fx.end);
        fx.end = [end[0],end[2]];

        fx.unit = [end[1],end[3]];
        fx.bgPosReady = true;
    }

    var nowPosX = [];
    nowPosX[0] = ((fx.end[0] - fx.start[0]) * fx.pos) + fx.start[0] + fx.unit[0];
    nowPosX[1] = ((fx.end[1] - fx.start[1]) * fx.pos) + fx.start[1] + fx.unit[1];
    fx.elem.style.backgroundPosition = nowPosX[0]+' '+nowPosX[1];
};

});//(jQuery);