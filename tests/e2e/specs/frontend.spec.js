const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'WP Accessibility front end', () => {
	test( 'site front page loads', async ( { page } ) => {
		await page.goto( '/' );

		await expect( page.locator( 'body' ) ).toBeVisible();
	} );
} );
