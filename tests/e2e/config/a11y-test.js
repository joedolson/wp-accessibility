const base = require( '@wordpress/e2e-test-utils-playwright' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

/**
 * Extends the WordPress Playwright test base with an axe-core builder fixture,
 * pre-configured against WCAG 2.1 A/AA rules for accessibility assertions.
 */
const test = base.test.extend( {
	makeAxeBuilder: async ( { page }, use ) => {
		// Accept an override page (e.g. a preview tab opened outside the editor) since
		// the fixture would otherwise always scan the default `page`.
		const makeAxeBuilder = ( { page: targetPage = page } = {} ) =>
			new AxeBuilder( { page: targetPage } ).withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] );

		await use( makeAxeBuilder );
	},
} );

const { expect } = base;

module.exports = { test, expect };
