const { defineConfig, devices } = require( '@playwright/test' );

/**
 * Playwright configuration for WP Accessibility end-to-end tests.
 *
 * Tests run against a local wp-env instance (see .wp-env.json).
 * Start the environment with `npm run wp-env start` before running tests,
 * or use `npm run test:e2e` which starts wp-env automatically.
 */
module.exports = defineConfig( {
	testDir: './tests/e2e/specs',
	outputDir: './tests/e2e/artifacts/test-results',
	globalSetup: require.resolve( './tests/e2e/config/global-setup.js' ),
	fullyParallel: true,
	forbidOnly: !! process.env.CI,
	retries: process.env.CI ? 2 : 0,
	workers: process.env.CI ? 1 : undefined,
	reporter: process.env.CI
		? [ [ 'html', { outputFolder: './tests/e2e/artifacts/report', open: 'never' } ], [ 'github' ] ]
		: [ [ 'html', { outputFolder: './tests/e2e/artifacts/report', open: 'never' } ], [ 'list' ] ],
	use: {
		baseURL: process.env.WP_BASE_URL || 'http://localhost:8888',
		storageState: './tests/e2e/artifacts/storage-states/admin.json',
		trace: 'on-first-retry',
		screenshot: 'only-on-failure',
		video: 'retain-on-failure',
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
	],
} );
