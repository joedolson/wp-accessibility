const { test, expect } = require( '../config/a11y-test' );

test.describe( 'WP Accessibility accessibility', () => {
	test( 'front page has no serious or critical axe violations', async ( { page, makeAxeBuilder } ) => {
		await page.goto( '/' );

		const accessibilityScanResults = await makeAxeBuilder().analyze();

		const seriousOrCritical = accessibilityScanResults.violations.filter( ( violation ) =>
			[ 'serious', 'critical' ].includes( violation.impact )
		);

		expect( seriousOrCritical ).toEqual( [] );
	} );
} );
