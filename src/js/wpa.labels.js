(function( $ ) { 'use strict';
	var wpa_names = [ 's', 'author', 'email', 'url', 'comment' ];
	$.each( wpa_names, function( index, value ) {
		if ( value == 'comment' ) {
			var field = $( 'textarea[name=' + value + ']' );
		} else {
			var field = $( 'input[name=' + value + ']' );
		}
		var form_id = field.attr( 'id' );
		var implicit = $( field ).parent( 'label' );
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
	});
}(jQuery));