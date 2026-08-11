(() => {
	'use strict';
	// Inverts logic on the infinite scrolling toggle in the media library.
	const scrolling = document.getElementById('infinite_scrolling');
	if ( null !== scrolling ) {
		scrolling.value = 'true';
		if ( scrolling.checked === true ) {
			// Reverse checked state, due to reversed logic.
			scrolling.checked = false;
		} else {
			scrolling.checked = true;
		}
		const label = scrolling.labels[0];
		label.lastChild.nodeValue = wpa11y.infinite_scrolling_label;
		scrolling.addEventListener('change', function() {
			// If the checkbox is unchecked, add a hidden input with the same name and value of true.
			// This is because unchecked checkboxes do not submit a value, and we want to ensure
			// the setting is saved as true.
			if ( this.checked !== true ) {
				const hidden = document.createElement( 'input' );
				hidden.type = 'hidden';
				hidden.name = this.name;
				hidden.value = 'false';
				this.parentNode.appendChild( hidden );
			}
		});
	}
})();