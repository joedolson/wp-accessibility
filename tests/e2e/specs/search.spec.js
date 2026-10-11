const path = require( 'path' );
const { execFileSync } = require( 'child_process' );
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const wpEnv = path.resolve( __dirname, '../../../node_modules/@wordpress/env/bin/wp-env' );

/**
 * Run a WP-CLI command inside the wp-env environment.
 *
 * @param {...string} args WP-CLI arguments.
 */
const wp = ( ...args ) => {
	execFileSync( process.execPath, [ wpEnv, 'run', 'cli', 'wp', ...args ], { stdio: 'pipe' } );
};

// The plugin reads this option when it loads, so it must be set before each page request.
const setSearchOption = ( value ) => wp( 'option', 'update', 'wpa_search', value );

test.describe( 'WP Accessibility empty search', () => {
	test.describe.configure( { mode: 'serial' } );

	test.afterAll( () => {
		// Restore the plugin default.
		setSearchOption( 'on' );
	} );

	test( 'with the option enabled, an empty search returns search results', async ( { page } ) => {
		setSearchOption( 'on' );
		await page.goto( '/?s=' );

		await expect( page.locator( 'body' ) ).toHaveClass( /(^|\s)search(\s|$)/ );
		await expect( page.locator( 'body' ) ).not.toHaveClass( /(^|\s)home(\s|$)/ );
	} );

	test( 'with the option enabled, a whitespace-only search returns search results', async ( { page } ) => {
		setSearchOption( 'on' );
		await page.goto( '/?s=%20%20' );

		await expect( page.locator( 'body' ) ).toHaveClass( /(^|\s)search(\s|$)/ );
	} );

	test( 'with the option disabled, an empty search returns the home page', async ( { page } ) => {
		setSearchOption( '' );
		await page.goto( '/?s=' );

		await expect( page.locator( 'body' ) ).toHaveClass( /(^|\s)home(\s|$)/ );
		await expect( page.locator( 'body' ) ).not.toHaveClass( /(^|\s)search(\s|$)/ );
	} );
} );
