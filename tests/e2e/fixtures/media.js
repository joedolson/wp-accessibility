const path = require( 'path' );
const { execFileSync } = require( 'child_process' );

const wpEnv = path.resolve( __dirname, '../../../node_modules/@wordpress/env/bin/wp-env' );

/**
 * Attachments referenced by the test markup (see test-markup.html).
 * Fixed IDs let the theme reference them without looking anything up.
 */
const mediaFixtures = [
	{
		id: 9001,
		title: 'Grey rectangle',
		alt: 'A grey rectangle used as test content',
		description: '',
	},
	{
		id: 9002,
		title: 'Blue rectangle',
		alt: 'A blue rectangle',
		description: 'A blue rectangle filling the frame, used to test the longdesc attribute.',
	},
	{
		id: 9003,
		title: 'Green rectangle',
		alt: 'A green rectangle',
		description: 'A green rectangle filling the frame, used to test the long description block style.',
	},
];

/**
 * Run a WP-CLI command inside the wp-env environment.
 *
 * @param {...string} args WP-CLI arguments.
 *
 * @return {string} Command output.
 */
const wp = ( ...args ) =>
	execFileSync( process.execPath, [ wpEnv, 'run', 'cli', 'wp', ...args ], { stdio: 'pipe' } ).toString();

/**
 * Remove the fixture attachments. Missing attachments are ignored.
 */
const removeMedia = () => {
	for ( const { id } of mediaFixtures ) {
		try {
			wp( 'post', 'delete', String( id ), '--force' );
		} catch ( e ) {
			// Nothing to delete.
		}
	}
};

/**
 * Create the fixture attachments with fixed IDs, alt text, and descriptions.
 * Descriptions are what WP Accessibility reads for long descriptions.
 */
const createMedia = () => {
	removeMedia();
	for ( const { id, title, alt, description } of mediaFixtures ) {
		wp(
			'post',
			'create',
			'--post_type=attachment',
			'--post_status=inherit',
			'--post_mime_type=image/png',
			`--import_id=${ id }`,
			`--post_title=${ title }`,
			`--post_content=${ description }`,
			`--guid=http://localhost:8888/wp-content/uploads/wpa-test-${ id }.png`
		);
		wp( 'post', 'meta', 'update', String( id ), '_wp_attachment_image_alt', alt );
	}
};

module.exports = { mediaFixtures, createMedia, removeMedia, wp };
