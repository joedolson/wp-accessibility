const { test, expect } = require( '../config/a11y-test' );
const { fixturePath, mockSite } = require( '../fixtures/mock-site' );

test.describe( 'WP Accessibility accessibility', () => {
	test.beforeEach( async ( { page } ) => {
		await mockSite( page );
	} );

	test( 'has no axe violations after remediation in link mode', async ( { page, makeAxeBuilder } ) => {
		await page.goto( fixturePath );
		// Wait for the asynchronous long description links to appear before scanning.
		await expect( page.locator( 'figure.is-style-longdesc .longdesc-link' ) ).toHaveCount( 1 );

		const results = await makeAxeBuilder().analyze();

		expect( results.violations ).toEqual( [] );
	} );

	test( 'has no axe violations after remediation in button mode', async ( { page, makeAxeBuilder } ) => {
		await page.goto( `${ fixturePath }?ldType=button` );
		await expect( page.locator( '.wpa-ld button.wpa-toggle' ) ).toHaveCount( 2 );

		const results = await makeAxeBuilder().analyze();

		expect( results.violations ).toEqual( [] );
	} );
} );
