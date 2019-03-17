(function ($) {
	$(function() {
		$( '.current-menu-item a, .current_page_item a' ).attr( 'aria-current', 'true' ).append( "<span class='screen-reader-text'>(current)</span>" );
	});
}(jQuery));
