(function ($) {
	$(function() {
		$( '.current-menu-item' ).attr( 'aria-current', 'true' ).append( "<span class='screen-reader-text'>(current)</span>" );
		$( '.current_page_item' ).attr( 'aria-current', 'true' ).append( "<span class='screen-reader-text'>(current)</span>" );
    });
}(jQuery));