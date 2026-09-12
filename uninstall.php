<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.kybernetik-services.com
 * @since      3.0
 *
 * @package    Hinjipwpm
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// if not allowed to delete plugins go to plugins page
if ( ! current_user_can( 'delete_plugins' ) ) {
	$text = 'Sorry, you are not allowed to delete plugins for this site.';
	wp_die( esc_html__( $text ) );
}

/*
// if wrong referer exit
check_admin_referer( 'bulk-plugins' );

//$_POST = from the plugin form; $_GET = from the FTP details screen.
$status	= isset( $_GET[ 'plugin_status' ] )	? $_GET[ 'plugin_status' ] : 'all';
$page 	= isset( $_GET[ 'paged' ] )			? $_GET[ 'paged' ] : '1';
$s		= isset( $_GET[ 's' ] )				? $_GET[ 's' ] : '';

$plugins = isset( $_REQUEST[ 'checked' ] ) ? (array) $_REQUEST[ 'checked' ] : array();

// if no plugins to delete go to plugins page
if ( empty( $plugins ) ) {
	wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
	exit;
}

// if current plugin not in list go to plugins page
if ( false === array_search ( dirname( plugin_basename( __FILE__ ) ) . '/purify_wp_menues.php', $plugins ) ) {
	wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
	exit;
}
*/

// clean up the database considering multisite installation
if ( is_multisite() ) {

	// Retrieve all site IDs using the API available since WordPress 4.6.
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $site_ids as $site_id ) {
		// switch to next blog
		switch_to_blog( $site_id );

		// remove settings
		delete_option( 'purify_wp_menu_options_set' );

		// Restore the previous site after each switch.
		restore_current_blog();
	}
} else {
	// remove settings
	delete_option( 'purify_wp_menu_options_set' );
}
