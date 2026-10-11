const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );
const { createMedia, removeMedia, wp } = require( '../fixtures/media' );

/**
 * Options that are off by default but required to exercise every fix.
 * Fixes that are on by default (lang, viewport, labels, titles) are not listed.
 */
const enabledOptions = {
	wpa_target: 'on',
	wpa_tabindex: 'on',
	wpa_underline: 'on',
	wpa_videos: 'on',
	wpa_show_alt: 'on',
	asl_enable: 'on',
	asl_content: '#content',
	wpa_longdesc: 'link',
};

/**
 * Fetch the unmodified server-rendered markup, before WP Accessibility's script runs.
 *
 * @param {import('@playwright/test').Page} page Page.
 *
 * @return {Promise<string>} HTML source.
 */
const getSource = async ( page ) => ( await page.request.get( '/' ) ).text();

test.describe( 'WP Accessibility front end', () => {
	// Some tests change the long description mode, which alters the page for every other test.
	test.describe.configure( { mode: 'serial' } );

	test.beforeAll( async ( { requestUtils } ) => {
		await requestUtils.activateTheme( 'wp-accessibility-test' );
		createMedia();
		for ( const [ name, value ] of Object.entries( enabledOptions ) ) {
			wp( 'option', 'update', name, value );
		}
	} );

	test.afterAll( () => {
		removeMedia();
		for ( const name of Object.keys( enabledOptions ) ) {
			wp( 'option', 'delete', name );
		}
	} );

	test.beforeEach( async ( { page } ) => {
		await page.goto( '/' );
	} );

	test( 'site front page loads', async ( { page } ) => {
		await expect( page.locator( 'body' ) ).toBeVisible();
	} );

	test( 'adds a missing lang attribute to the html element', async ( { page } ) => {
		const source = await getSource( page );
		expect( source ).not.toMatch( /<html[^>]*\slang=/i );

		await expect( page.locator( 'html' ) ).toHaveAttribute( 'lang', /.+/ );
	} );

	test( 'makes the viewport scalable', async ( { page } ) => {
		const source = await getSource( page );
		expect( source ).toMatch( /<meta[^>]+name=["']viewport["'][^>]+(user-scalable=no|maximum-scale)/i );

		const content = await page.locator( 'meta[name="viewport"]' ).getAttribute( 'content' );
		expect( content ).not.toContain( 'user-scalable=no' );
		expect( content ).not.toContain( 'maximum-scale' );
	} );

	test( 'adds skip links as the first element in the body', async ( { page } ) => {
		const skiplinks = page.locator( 'body > #skiplinks' ).first();

		await expect( skiplinks ).toHaveAttribute( 'role', 'navigation' );
		await expect( skiplinks.locator( 'a[href="#content"]' ) ).toHaveCount( 1 );
		expect( await page.evaluate( () => document.body.firstElementChild.id ) ).toBe( 'skiplinks' );
	} );

	test( 'labels search and comment fields', async ( { page } ) => {
		const unlabeled = await page.evaluate( () => {
			const fields = document.querySelectorAll(
				'input[name="s"]:not(#adminbar-search), input[name="author"], input[name="email"], input[name="url"], textarea[name="comment"]'
			);
			return [ ...fields ]
				.filter( ( field ) => {
					const explicit = field.id && document.querySelector( `label[for="${ field.id }"]` );
					const wrapping = field.closest( 'label' );
					const labelledby = field.getAttribute( 'aria-labelledby' );
					const named =
						( explicit && explicit.textContent.trim() ) ||
						( wrapping && wrapping.textContent.trim() ) ||
						field.getAttribute( 'aria-label' ) ||
						( labelledby && document.getElementById( labelledby )?.textContent.trim() );
					return ! named;
				} )
				.map( ( field ) => field.name );
		} );

		expect( unlabeled ).toEqual( [] );
	} );

	test( 'removes title attributes from images, links, buttons and fields', async ( { page } ) => {
		const source = await getSource( page );
		expect( source ).toMatch( /<(img|a|button|input|select|textarea)\b[^>]*\stitle=/i );

		const remaining = await page.evaluate( () =>
			[ ...document.querySelectorAll( 'img, a, input, textarea, select, button' ) ]
				.filter( ( el ) => el.hasAttribute( 'title' ) && ! el.classList.contains( 'nturl' ) )
				// Links and buttons without text keep a title only when the plugin exempts the element.
				.map( ( el ) => el.outerHTML.slice( 0, 120 ) )
		);

		expect( remaining ).toEqual( [] );
	} );

	test( 'removes target attributes from links', async ( { page } ) => {
		const source = await getSource( page );
		expect( source ).toMatch( /<a\b[^>]*\starget=/i );

		const targeted = await page.evaluate( () =>
			[ ...document.querySelectorAll( 'a[target]:not(.wpa-allow-target)' ) ]
				.filter( ( a ) => {
					try {
						return ! new URL( a.getAttribute( 'href' ) ).hostname.includes( 'facebook' );
					} catch ( e ) {
						return true;
					}
				} )
				.map( ( a ) => a.outerHTML.slice( 0, 120 ) )
		);

		expect( targeted ).toEqual( [] );
	} );

	test( 'removes tabindex from natively focusable elements', async ( { page } ) => {
		const source = await getSource( page );
		expect( source ).toMatch( /<(a|button|input|select|textarea)\b[^>]*\stabindex=/i );

		const remaining = await page.evaluate( () =>
			[ ...document.querySelectorAll( 'input:not(.wpa-ignore-tabindex), a[href]:not(.wpa-ignore-tabindex), select:not(.wpa-ignore-tabindex), textarea:not(.wpa-ignore-tabindex), button:not(.wpa-ignore-tabindex)' ) ]
				.filter( ( el ) => {
					if ( ! el.hasAttribute( 'tabindex' ) ) {
						return false;
					}
					const hidden = el.getAttribute( 'aria-hidden' ) === 'true' || el.hasAttribute( 'hidden' );
					return ! ( el.getAttribute( 'tabindex' ) === '-1' && hidden );
				} )
				.map( ( el ) => el.outerHTML.slice( 0, 120 ) )
		);

		expect( remaining ).toEqual( [] );
	} );

	test( 'makes fake buttons and links keyboard focusable', async ( { page } ) => {
		const fakeControls = page.locator( '[role="button"]:not(a):not(button), [role="link"]:not(a), a[role="button"]:not([href])' );
		const count = await fakeControls.count();
		expect( count ).toBeGreaterThan( 0 );

		for ( let i = 0; i < count; i++ ) {
			const control = fakeControls.nth( i );
			await expect( control ).toHaveAttribute( 'tabindex', '0' );
			await expect( control ).toHaveClass( /wpa-focusable/ );
		}

		const anchorsWithoutHref = page.locator( 'a:not([href])' );
		for ( let i = 0; i < await anchorsWithoutHref.count(); i++ ) {
			await expect( anchorsWithoutHref.nth( i ) ).toHaveAttribute( 'tabindex', '0' );
		}
	} );

	test( 'underlines links outside navigation', async ( { page } ) => {
		const links = page.locator( 'a[href]:not(nav a):not(#wpadminbar a):not(#skiplinks a)' );
		const count = await links.count();
		expect( count ).toBeGreaterThan( 0 );

		for ( let i = 0; i < count; i++ ) {
			const decoration = await links.nth( i ).evaluate( ( el ) => el.style.textDecoration );
			expect( decoration ).toContain( 'underline' );
		}
	} );

	test( 'adds a working pause button to autoplaying videos without controls', async ( { page } ) => {
		const videos = page.locator( 'video[autoplay]:not([controls])' );
		expect( await videos.count() ).toBeGreaterThan( 0 );

		const video = videos.first();
		const button = video.locator( 'xpath=..' ).locator( 'button.wpa-video' );
		await expect( button ).toHaveCount( 1 );
		await expect( button ).toHaveAttribute( 'aria-pressed', /true|false/ );

		// Ensure the video is playing before toggling.
		await video.evaluate( ( el ) => el.play().catch( () => {} ) );
		await button.evaluate( ( el ) => el.setAttribute( 'aria-pressed', 'false' ) );

		await button.click();
		await expect( button ).toHaveAttribute( 'aria-pressed', 'true' );
		expect( await video.evaluate( ( el ) => el.paused ) ).toBe( true );

		await button.click();
		await expect( button ).toHaveAttribute( 'aria-pressed', 'false' );
	} );

	test( 'adds a toggle to display image alt text', async ( { page } ) => {
		const toggle = page.locator( '.wpa-alt button.wpa-toggle' ).first();
		await expect( toggle ).toBeVisible();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );

		const altText = page.locator( '.wpa-alt' ).first().locator( '.wpa-alt-text' );
		await expect( altText ).toBeHidden();

		await toggle.click();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( altText ).toBeVisible();

		await toggle.click();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( altText ).toBeHidden();
	} );

	test( 'adds long description links to images with a longdesc attribute', async ( { page } ) => {
		const images = page.locator( 'img[longdesc]' );
		const count = await images.count();
		expect( count ).toBeGreaterThan( 0 );

		for ( let i = 0; i < count; i++ ) {
			const image = images.nth( i );
			const wrapper = image.locator( 'xpath=..' );
			await expect( wrapper ).toHaveClass( /wpa-ld/ );

			const link = wrapper.locator( 'a.longdesc-link' );
			await expect( link ).toHaveCount( 1 );
			await expect( link ).toHaveAttribute( 'href', ( await image.getAttribute( 'longdesc' ) ) );
			await expect( link ).toContainText( 'Description' );
		}
	} );

	test( 'adds long description links to images using the longdesc block style', async ( { page } ) => {
		const images = page.locator( 'figure.is-style-longdesc img' );
		expect( await images.count() ).toBeGreaterThan( 0 );

		// Links are added after the media REST request resolves.
		const links = page.locator( 'figure.is-style-longdesc .wpa-ld a.longdesc-link' );
		await expect( links ).toHaveCount( await images.count() );
		await expect( links.first() ).toHaveAttribute( 'href', /longdesc=\d+/ );
		await expect( links.first().locator( '.screen-reader-text' ) ).toContainText( 'of' );
	} );

	test( 'shows long descriptions in an expandable region in button mode', async ( { page } ) => {
		try {
			wp( 'option', 'update', 'wpa_longdesc', 'button' );
			await page.goto( '/' );

			const wrapper = page.locator( '.wpa-ld' ).first();
			const toggle = wrapper.locator( 'button.wpa-toggle' );
			const description = wrapper.locator( '.longdesc' );

			await expect( toggle ).toBeVisible();
			await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
			await expect( description ).toBeHidden();

			await toggle.click();
			await expect( toggle ).toHaveAttribute( 'aria-expanded', 'true' );
			await expect( description ).toBeVisible();
			await expect( description ).not.toBeEmpty();

			await toggle.click();
			await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
			await expect( description ).toBeHidden();
		} finally {
			wp( 'option', 'update', 'wpa_longdesc', 'link' );
		}
	} );
} );
