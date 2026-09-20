const { request } = require( '@playwright/test' );
const { RequestUtils } = require( '@wordpress/e2e-test-utils-playwright' );

/**
 * Global setup: logs in as an admin via the REST API and stores the
 * authenticated session so individual tests don't need to log in again.
 */
module.exports = async ( config ) => {
	const { storageState, baseURL } = config.projects[ 0 ].use;

	const requestContext = await request.newContext( {
		baseURL,
	} );

	const requestUtils = new RequestUtils( requestContext, {
		storageStatePath: storageState,
	} );

	await requestUtils.setupRest();

	await requestContext.dispose();
};
