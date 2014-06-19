<?php
/*
Plugin Name: Zeitguys Cookie Crumbs
Plugin URI: http://zeitguys.com/wordpress
Description: Enables navigation Cookie Crumbs.
Version: 1.2
Author: Tom Auger
Author URI: http://tomauger.com
*/

/**
 * 	Template tag inserts full cookie crumb with hyperlinks
 * 	@param array $opts parameter and display options.
 *
 *  Options include:
 *  container_tag: can be blank. If provided, will wrap the entire cookie crumb with this tag.
 *  container_id: Will add this id to the container_tag (if container tag is provided of course).
 *  crumb_wrapper_tag: can be blank. If provided, will wrap each crumb with that tag.
 *  crumb_divider_text: the divider between each tag. If using LI as crumb_wrapper_tag, you will need to manually wrap this divider text an LI, eg: '<li class="divider">&gt;</li>'
 *  label: text that is prepended to the cookie crumb like a label, such as "You are here:". You may wish to make this only visible for screen readers for example.
 *  home_crumb: if supplied, it will prepend a crumb pointing to the site root. If left blank, this is omitted.
 */


/**
 * Change Log
 * -----------------------------------------------------------------------------
 * v1.2
 *  - Race condition fixes
 *  - Additional logic around custom menus
 *
 * v1.1
 *  - PHP Notice Fixes
 */


if ( !function_exists( 'the_cookie_crumb' ) ){
	function the_cookie_crumb( $opts = array() ){
		$opts = wp_parse_args( $opts, array(
			'container_tag' => 'ul',
			'container_class' => 'crumb',
			'crumb_wrapper_tag' => 'li',
			'crumb_divider_text' => '<li class="divider">&gt;</li>',
			'label' => '<div class="accessibility-nonvisible">You are here: </div>',
			'home_crumb' => esc_attr( get_bloginfo( 'name', 'display' ) )
		));

		echo ZG_Cookie_Crumbs::get_cookie_crumb( $opts );
	}
}

class ZG_Cookie_Crumbs {
	const MAX_RECURSIONS = 25;
	const TD = 'zg-cookie-crumbs-textdomain';

	public static function get_cookie_crumb( $opts = array() ){
		$opts = wp_parse_args( $opts, array(
			'container_tag' => 'ul',
			'container_id' => 'zg-crumb',
			'crumb_wrapper_tag' => 'li',
			'crumb_divider_text' => '<li class="divider">&gt;</li>',
			'label' => '<div class="screen-reader-text">You are here: </div>',
			'home_crumb' => esc_attr( get_bloginfo( 'name', 'display' ) )
		));

		$crumb = "";

		global $post, $wp_query;

		/* We build the cookie crumb differently depending on what kind of page we're at. */
		if ( is_search() ){
			self::prepend_crumb( $crumb, 'Search Results ('.get_search_query().')', '?s='.urlencode( get_search_query() ), $opts );
		} elseif ( is_attachment() ) {
			self::build_hierarchical_crumb( $crumb, $post, $opts );
		} elseif ( is_post_type_hierarchical( get_post_type() ) ) {
			self::build_hierarchical_crumb( $crumb, $post, $opts );
		} elseif ( is_singular() ) {
			// Crumbs are built back-to-front
			static::prepend_crumb( $crumb, $post->post_title, get_permalink( $post ), $opts );
			$categories = get_the_category( $post->ID );
			if( count( $categories ) )
				static::prepend_crumb( $crumb, $categories[0]->cat_name, get_category_link( $categories[0]->term_id ), $opts );
			else {
				$post_type_obj = get_post_type_object( $post->post_type );
				static::prepend_crumb( $crumb, $post_type_obj->labels->name, get_post_type_archive_link( $post->post_type ), $opts );
			}

		} elseif ( is_post_type_archive() ){
			$post_type = $wp_query->get_queried_object();
			if( ! is_wp_error( $post_type ) )
				static::prepend_crumb( $crumb, $post_type->labels->name, get_post_type_archive_link( get_post_type() ), $opts );
		}

		/** @TODO: doesn't handle hierarchical taxonomies */
		elseif ( is_tax() ){
			$term = $wp_query->get_queried_object();
			if( ! is_wp_error( $term ) ){
				if ( is_taxonomy_hierarchical( $term->taxonomy ) ){
				} else {
				}
				static::prepend_crumb( $crumb, $term->name, get_term_link( $term ), $opts );

				static::build_menu_crumb( $crumb, $term, $opts );
			}
		}

		// Support ZG_P2P relationships
		if( ! empty( $_GET['related_to'] ) ) {
			$related_post_id = $_GET['related_to'];
			$related_post = get_post( $related_post_id );
			if( $related_post ) {
				static::prepend_crumb( $crumb, $related_post->post_title, get_permalink( $related_post->ID ), $opts );
			}
		}

		// Add in the "Home" link if requested in options
		if ( $opts['home_crumb'] ) static::prepend_crumb( $crumb, $opts['home_crumb'], get_home_url(), $opts );

		// Additional markup, if requested in options
		if ( $opts['container_tag'] ) $crumb = "<{$opts['container_tag']} id=\"{$opts['container_id']}\">$crumb</{$opts['container_tag']}>";
		if ( $opts['label'] ) $crumb = $opts['label'] . $crumb;

		echo $crumb;
	}


	/**
	 * Takes a reference to the crumb content string and prepends the new crumb
	 *  to it. Designed to build the crumb "backwards" from back to front.
	 * @param string &$crumb reference to the $crumb string that is built 
	 *        back-to-front
	 * @param string $title the Text that will be displayed for that crumb
	 * @param string $link the URL that clicking the crumb will take you to
	 * @param array $opts a list of options for building the crumb. 
	 *        See {@link the_cookie_crumb()} for a complete list of options.
	 * @see the_cookie_crumb()
	 */
	static function prepend_crumb( &$content, $title, $link, $opts ){
		extract( $opts, EXTR_SKIP );

		$crumb = '<a href="'.$link.'">'.$title.'</a>';
		if ($opts['crumb_wrapper_tag']) $crumb = "<{$opts['crumb_wrapper_tag']}>$crumb</{$opts['crumb_wrapper_tag']}>";
		if ($opts['crumb_divider_text'] && $content) $crumb .= $opts['crumb_divider_text'];

		$content = $crumb . $content;
	}

	/**
	 * Recursively builds the cookiecrumb. The First iteration is the current 
	 * page, whether its a post or an archive. Recursively we try to find a post
	 * parent, with a fallback of nav menu hierarchy.
	 * 
	 * @param string &$crumb
	 * @param WP_Post
	 * @param array $opts
	 * @param array $_post_IDs
	 * @param int $_recursion_level   
	 */

	static function build_hierarchical_crumb( &$crumb, $the_post, $opts, $_post_IDs = array(), $_recursion_level = 0 ){
		// Safe recursion check
		if ( self::MAX_RECURSIONS > $_recursion_level++ ){
			// Never repeat a link!
			if ( ! in_array( $the_post->ID, $_post_IDs ) ){
				$_post_IDs[] = $the_post->ID;
				self::prepend_crumb( $crumb, $the_post->post_title, get_permalink( $the_post->ID ), $opts );

				if ( $the_post->post_parent ) {
					self::build_hierarchical_crumb( $crumb, get_post( $the_post->post_parent ), $opts, $_post_IDs, $_recursion_level );
				} else {
					// we're at the top level of the post hierarchy. Is there a menu hierarchy we should be considering?
					$menu_items = wp_get_associated_nav_menu_items( $the_post->ID, 'post_type' );
					// We may have found more than one menus this item appears in. Arbitrarily select the first one that has a parent

					foreach ( $menu_items as $item_ID ){
						// does the item have a parent? If so, go with that.
						$item = get_post( $item_ID );
						if ( $item && is_nav_menu_item( $item->ID ) ){
							$item = wp_setup_nav_menu_item( $item );
							$menu_parent = $item->menu_item_parent;

							/**
							 * @TODO: this COULD jump menus if an item's parent is in multiple menus. Not sure if this is a problem.
							 * Might want to spawn a specific build_menu_crumb that follows only a single nav menu hierarchy up to the top.
							 */
							if ( $menu_parent ){
								$menu_parent = wp_setup_nav_menu_item( get_post( $menu_parent ) );

								return self::build_hierarchical_crumb( $crumb, get_post( $menu_parent->object_id ), $opts, $_post_IDs, $_recursion_level );
							}
						}
					}
				}
			}
		} else {
			trigger_error( __( 'ZG_Cookie_Crumbs::build_hierarchical_crumb() MAX_RECURSIONS reached.'), E_USER_WARNING );
		}
	}

	/**
	 * Builds the cookiecrumb. The First iteration is the current 
	 * page, whether its a post or an archive. 
	 * 
	 * @param string &$crumb
	 * @param WP_Post|WP_Term $object
	 * @param array $opts
	 * @param array $_object_IDs
	 * @param int $_recursion_level 
	 */
	static function build_menu_crumb( &$crumb, $object, $opts, $_object_IDs = array(), $_recursion_level = 0 ){
		if ( self::MAX_RECURSIONS > $_recursion_level++ ){

			if ( is_object( $object ) ){
				// We need the ID and the object_type for associated nav menu items
				if ( isset( $object->ID ) )
					$object_id = $object->ID; $object_type = 'post_type';
				if ( isset( $object->term_id ) )
					$object_id = $object->term_id; $object_type = 'taxonomy';

				if ( ! is_null( $object_id ) && ! in_array( $object_id, $_object_IDs ) ){
					$_object_IDs[] = $object_id;

					$menu_items = wp_get_associated_nav_menu_items( $object_id, $object_type );
					// We may have found more than one menu this item appears in. Arbitrarily select the first one that has a parent

					foreach ( $menu_items as $item_ID ){
						// does the item have a parent? If so, go with that.
						$item = get_post( $item_ID );
						if ( $item && is_nav_menu_item( $item->ID ) ){
							$item = wp_setup_nav_menu_item( $item );
							$menu_parent = $item->menu_item_parent;

							if ( $menu_parent ){
								$menu_parent = wp_setup_nav_menu_item( get_post( $menu_parent ) );
								$parent_post = get_post( $menu_parent->object_id );

								self::prepend_crumb( $crumb, $parent_post->post_title, get_permalink( $parent_post ), $opts );

								return self::build_menu_crumb( $crumb, $parent_post, $opts, $_object_IDs, $_recursion_level );
							}
						}
					}
					// @TODO : Consider removal, may be deprecated
					// No direct menu parent found. Let's see if the taxonomy has been associated with any menu items
					if ( 'taxonomy' == $object_type ){
						if ( $tax_pages = get_site_option( 'zg_cookiecrumbs_tax_pages' ) ){
							// Iterate through the menu items that have been tagged as taxonomy landing pages. Pick the first one (for now)
							foreach ( $tax_pages as $menu_item_id => $taxonomy ){
								if ( $taxonomy == $object->taxonomy ){
									if ( $menu_item = wp_setup_nav_menu_item( get_post( $menu_item_id ) ) ){
										if ( $menu_item_page = get_post( $menu_item->object_id ) ){
											self::prepend_crumb( $crumb, $menu_item_page->post_title, get_permalink( $menu_item_page ), $opts );
											return self::build_menu_crumb ( $crumb, $menu_item_page, $opts, $_object_IDs, $_recursion_level );
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			trigger_error( __( 'ZG_Cookie_Crumbs::build_menu_crumb() MAX_RECURSIONS reached.'), E_USER_WARNING );
		}

		return false; // in case anyone cares
	}
}


// Hook into the Menu Editor (as best we can) by over-riding the default Nav Walker that creates the draggable menu items.
// Only do this on the appropriate admin page (nav-menus.php).
global $pagenow;
if ( 'nav-menus.php' == $pagenow ){
	// This plugin is loaded before nav-menus.php gets a chance to load the Walker class, so we'll load it now
	require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

	// Extra safety check. We're extending the class, so make sure it exists!
	if ( class_exists( 'Walker_Nav_Menu_Edit' ) ){
		// use our own Walker, but only if it's the vanilla walker that is being replaced.
		add_filter( 'wp_edit_nav_menu_walker', function( $w ){ if ( 'Walker_Nav_Menu_Edit' == $w ) return 'Walker_Nav_Menu_Edit_Crumb'; else return $w; } );
		add_action( 'wp_update_nav_menu_item', 'zg_cookiecrumbs_update_nav_menu_item', 10, 3 );

		class Walker_Nav_Menu_Edit_Crumb extends Walker_Nav_Menu_Edit {
			function start_el( &$output, $item, $depth = 0, $args = array( ), $id = 0 ) {
				parent::start_el( $output, $item, $depth, $args, $id );

				$item_id = esc_attr( $item->ID );

				$tax_pages = get_site_option( 'zg_cookiecrumbs_tax_pages' );
				$tax_archive = isset( $tax_pages[$item_id] ) ? $tax_pages[$item_id] : "";

				$label = __( 'Page is Tax Archive For' );
				$taxonomy_select = "<option value=''>" . __( 'Select a Taxonomy' ) . "</option>";
				$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
				foreach( $taxonomies as $tax_obj ){
					$selected = $tax_obj->name == $tax_archive ? " selected='selected'" : "";
					$taxonomy_select .= "<option value='{$tax_obj->name}'{$selected}>{$tax_obj->label}</option>";
				}
				$taxonomy_select = "<select id='edit-menu-item-taxonomy-$item_id' class='widefat edit-menu-item-taxonomy' name='menu-item-taxonomy[$item_id]'>" . $taxonomy_select . "</select>";

				$insert = <<<NAV_MENU_INSERT
				<p class="description description-thin">
					<label for="edit-menu-item-taxonomy-$item_id">
						$label<br />
						$taxonomy_select
					</label>
				</p>
NAV_MENU_INSERT;

				$output = preg_replace( '/(<p[^>]+>\s*<label\s+for\s?=\s?\"edit-menu-item-attr-title-' . $item_id . '.+?<\/label>\s*<\/p>\s*)/s', "$1{$insert}", $output );

			}
		}

		/**
		 * Save out our custom menu item fields 
		 * 
		 * @TODO : Consider removal, may be deprecated
		 *
		 * @param int $menu_id
		 * @param int $menu_item_db_id
		 * @param array $args
		 */
		function zg_cookiecrumbs_update_nav_menu_item( $menu_id, $menu_item_db_id, $args ){
			if ( isset( $_POST['menu-item-taxonomy'] ) && isset( $_POST['menu-item-taxonomy'][$menu_item_db_id] ) )
				$menu_item_taxonomy = $_POST['menu-item-taxonomy'][$menu_item_db_id];

			// Save tax_pages in site options
			$tax_pages = get_site_option( 'zg_cookiecrumbs_tax_pages' );
			if ( ! empty( $menu_item_taxonomy ) )
				$tax_pages[$menu_item_db_id] = $menu_item_taxonomy;
			else
				unset( $tax_pages[$menu_item_db_id] );

			update_site_option( 'zg_cookiecrumbs_tax_pages' , $tax_pages );
		}
	}
}