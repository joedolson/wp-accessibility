(function( $ ) { 'use strict';
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
						field.before( "<label for='" + form_id + "' class='wpa-screen-reader-text'>" + wpalabels[value] + "</label>" );
					}
				} else {
					if ( !implicit.length ) {
						field.attr( 'id', 'wpa_label_' + value ).before( "<label for='wpa_label_" + value + "' class='wpa-screen-reader-text'>" + wpalabels[value] + "</label>" );
					}
				}
			}
		}
	});
}(jQuery));