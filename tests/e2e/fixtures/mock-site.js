const fs = require( 'fs' );
const path = require( 'path' );

const fixturePath = '/wpa-fixture.html';
const markup = fs.readFileSync( path.resolve( __dirname, 'test-markup.html' ), 'utf8' );
const pluginScript = fs.readFileSync( path.resolve( __dirname, '../../../src/js/wp-accessibility.js' ), 'utf8' );
const video = fs.readFileSync( path.resolve( __dirname, 'assets/wwa.mp4' ) );

// Long descriptions returned by the mocked REST API, keyed by attachment ID.
const descriptions = {
	9002: 'A blue rectangle filling the frame, used to test the longdesc attribute.',
	9003: 'A green rectangle filling the frame, used to test the long description block style.',
};

/**
 * Serve the fixture, the plugin script, and mocked WordPress responses.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
const mockSite = async ( page ) => {
	await page.route( /\/wpa-fixture\.html(\?.*)?$/, ( route ) =>
		route.fulfill( { contentType: 'text/html', body: markup } )
	);
	await page.route( /\/wpa-fixture\.js$/, ( route ) =>
		route.fulfill( { contentType: 'application/javascript', body: pluginScript } )
	);
	await page.route( /\/wpa-fixture\/wwa\.mp4$/, ( route ) =>
		route.fulfill( { contentType: 'video/mp4', body: video } )
	);
	// Media REST endpoint used to look up long descriptions.
	await page.route( /\/wp-json\/wp\/v2\/media\/\d+/, ( route ) => {
		const id = route.request().url().match( /media\/(\d+)/ )[ 1 ];
		route.fulfill( {
			contentType: 'application/json',
			body: JSON.stringify( { id: Number( id ), description: { rendered: `<p>${ descriptions[ id ] || '' }</p>` } } ),
		} );
	} );
	// Long description pages requested in button mode.
	await page.route(
		( url ) => url.pathname === '/' && url.searchParams.has( 'longdesc' ),
		( route ) => {
			const id = new URL( route.request().url() ).searchParams.get( 'longdesc' );
			route.fulfill( {
				contentType: 'text/html',
				body: `<html><body><div id="desc_${ id }">${ descriptions[ id ] || '' }</div></body></html>`,
			} );
		}
	);
};

module.exports = { fixturePath, markup, descriptions, mockSite };
