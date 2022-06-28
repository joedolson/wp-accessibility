jQuery(document).ready(function ($) {
	$('.wpa-color-input').wpColorPicker();

	var default_css = document.getElementById( 'wpa_default_css' );
	if ( null !== default_css ) {

		default_css.style.display = 'none';

		var button = document.createElement( 'button' );
		button.type = 'button';
		button.innerHTML = 'Show Default CSS <span class="dashicons dashicons-arrow-right" aria-hidden="true"></span>';
		button.className =  'toggle-css button-secondary';
		button.setAttribute( 'aria-controls', 'wpa_default_css' );
		button.setAttribute( 'aria-expanded', false );
		default_css.insertAdjacentElement( 'beforebegin', button );

		var wpa_button = document.querySelector( 'button.toggle-css' );
		wpa_button.addEventListener( 'click', function() {
			var target  = this.getAttribute( 'aria-controls' );
			var css = document.getElementById( target );
			var visible = ( css.style['display'] == 'none' ) ? false : true;
			if ( ! visible ) {
				css.style.display = 'block';
				this.setAttribute( 'aria-expanded', true );
				this.querySelector( '.dashicons' ).classList.add( 'dashicons-arrow-down' );
				this.querySelector( '.dashicons' ).classList.remove( 'dashicons-arrow-right' );
			} else {
				css.style.display = 'none';
				this.setAttribute( 'aria-expanded', false );
				this.querySelector( '.dashicons' ).classList.remove( 'dashicons-arrow-down' );
				this.querySelector( '.dashicons' ).classList.add( 'dashicons-arrow-right' );
			}
		});
	}
});