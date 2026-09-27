const { test, expect } = require( '../config/a11y-test' );

test.describe( 'WP Accessibility accessibility', () => {

    test.beforeAll(async ({ requestUtils }) => {
        // Activates the theme instantly via the REST API.
        // Use the folder name/slug of your custom theme.
        await requestUtils.activateTheme('wp-accessibility-test');
    });

	test( 'front page has no serious or critical axe violations', async ( { page, makeAxeBuilder } ) => {
		await page.goto( '/' );

		const accessibilityScanResults = await makeAxeBuilder().analyze();

		const seriousOrCritical = accessibilityScanResults.violations.filter( ( violation ) =>
			[ 'serious', 'critical' ].includes( violation.impact )
		);

		expect( seriousOrCritical ).toEqual( [] );
	} );
} );
