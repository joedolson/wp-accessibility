(function ($) {
	$(function() {
		var current_item;
		current_item = $( '.current-menu-item a, .current_page_item a' );
		if ( ! current_item.hasAttribute( 'aria-current' ) ) {
			current_item.attr( 'aria-current', 'page' ).append( "<span class='screen-reader-text'>(current)</span>" );
		}
	});
}(jQuery));
