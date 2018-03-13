<?php
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
} else {
	delete_option( 'rta_from_nav_menu' );
	delete_option( 'rta_from_page_lists' );
	delete_option( 'rta_from_category_lists' );
	delete_option( 'rta_from_archive_links' );
	delete_option( 'rta_from_tag_clouds' );
	delete_option( 'rta_from_category_links' );
	delete_option( 'rta_from_post_edit_links' );
	delete_option( 'rta_from_edit_comment_links' );
	delete_option( 'wpa_installed' );
	delete_option( 'wpa_version' );
	delete_option( 'asl_enable' );
	delete_option( 'asl_content' );
	delete_option( 'asl_navigation' );
	delete_option( 'asl_sitemap' );
	delete_option( 'asl_extra_target' );
	delete_option( 'asl_extra_text' );
	delete_option( 'asl_visible' );
	delete_option( 'asl_styles_focus' );
	delete_option( 'asl_styles_passive' );
	delete_option( 'wpa_lang' );
	delete_option( 'wpa_target' );
	delete_option( 'wpa_search' );
	delete_option( 'wpa_tabindex' );
	delete_option( 'wpa_more' );
	delete_option( 'wpa_continue' );
	delete_option( 'wpa_toolbar' );
	delete_option( 'wpa_diagnostics' );
	delete_option( 'wpa_longdesc' );
	delete_option( 'wpa_underline' );
	delete_option( 'wpa_insert_roles' );
	delete_option( 'wpa_focus' );
	delete_option( 'wpa_focus_color' );
	delete_option( 'wpa_complementary_container' );
}