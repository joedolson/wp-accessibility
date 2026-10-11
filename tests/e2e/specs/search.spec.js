const path = require( 'path' );
const { execFileSync } = require( 'child_process' );
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const wpEnv = path.resolve( __dirname, '../../../node_modules/@wordpress/env/bin/wp-env' );
const postTitle = 'WPA empty search test post';

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

const hasClass = ( name ) => new RegExp( `(^|\\s)${ name }(\\s|$)` );

// The search page can include post lists outside the main query (e.g. a More Posts block),
// so results are detected from WordPress's body classes instead of page text.
test.describe( 'WP Accessibility empty search', () => {
	test.describe.configure( { mode: 'serial' } );

	let post;

	test.beforeAll( async ( { requestUtils } ) => {
		// Ensure there is published content that an unfiltered empty search would return.
		post = await requestUtils.createPost( { title: postTitle, status: 'publish' } );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		// Restore the plugin default and remove the test post.
		setSearchOption( 'on' );
		await requestUtils.rest( { method: 'DELETE', path: `/wp/v2/posts/${ post.id }`, params: { force: true } } );
	} );

	test( 'with the option enabled, an empty search returns no results', async ( { page } ) => {
		setSearchOption( 'on' );
		await page.goto( '/?s=' );

		await expect( page.locator( 'body' ) ).toHaveClass( hasClass( 'search' ) );
		await expect( page.locator( 'body' ) ).toHaveClass( hasClass( 'search-no-results' ) );
	} );

	test( 'with the option enabled, a whitespace-only search returns no results', async ( { page } ) => {
		setSearchOption( 'on' );
		await page.goto( '/?s=%20%20' );

		await expect( page.locator( 'body' ) ).toHaveClass( hasClass( 'search' ) );
		await expect( page.locator( 'body' ) ).toHaveClass( hasClass( 'search-no-results' ) );
	} );

	test( 'with the option disabled, an empty search returns all posts', async ( { page } ) => {
		setSearchOption( '' );
		await page.goto( '/?s=' );

		await expect( page.locator( 'body' ) ).toHaveClass( hasClass( 'search' ) );
		await expect( page.locator( 'body' ) ).not.toHaveClass( hasClass( 'search-no-results' ) );
	} );
} );
