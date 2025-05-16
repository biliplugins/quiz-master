<?php
/**
 * Plugin Name:     Quiz Master
 * Description:     A quiz for your WordPress website.
 * Author:          Bili Plugins
 * Author URI:      https://biliplugins.com/
 * Text Domain:     quiz-master
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Quiz_Master
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package    Quiz_Master
 */
if ( ! defined( 'BWP_QM_VERSION' ) ) {
	/**
	 * The version of the plugin.
	 */
	define( 'BWP_QM_VERSION', '1.0.0' );
}

if ( ! defined( 'BWP_QM_PATH' ) ) {
	/**
	 *  The server file system path to the plugin directory.
	 */
	define( 'BWP_QM_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BWP_QM_URL' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'BWP_QM_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BWP_QM_BASE_NAME' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'BWP_QM_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'BWP_QM_MAIN_FILE' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'BWP_QM_MAIN_FILE', __FILE__ );
}

/**
 * Include files.
 */
$files = array(
	'app/includes/common-functions',
	'app/main/class-main',
	'app/admin/class-admin-main',
);

if ( ! empty( $files ) ) {

	foreach ( $files as $file ) {

		// Include functions file.
		if ( file_exists( BWP_QM_PATH . $file . '.php' ) ) {
			require BWP_QM_PATH . $file . '.php';
		}
	}
}

/**
 * Plugin Setting page.
 *
 * @param array $links Array of plugin links.
 * @return array Array of plugin links.
 */
function bwp_qm_settings_link( $links ) {

	$settings_link = sprintf(
		'<a href="%1$s">%2$s</a>',
		'admin.php?page=ai-audio-responder',
		esc_html__( 'Settings', 'quiz-master' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . BWP_QM_BASE_NAME, 'bwp_qm_settings_link' );

/**
 * Apply translation file as per WP language.
 */
function bwp_qm_text_domain_loader() {

	// Get mo file as per current locale.
	$mofile = BWP_QM_PATH . 'languages/bp-ai-' . get_locale() . '.mo';

	// If file does not exists, then apply default mo.
	if ( ! file_exists( $mofile ) ) {
		$mofile = BWP_QM_PATH . 'languages/default.mo';
	}

	if ( file_exists( $mofile ) ) {
		load_textdomain( 'quiz-master', $mofile );
	}
}

add_action( 'plugins_loaded', 'bwp_qm_text_domain_loader' );

/**
 * Create table.
 *
 * @return void
 */
function blp_qm_quiz_create_results_table() {

	global $wpdb;
	$table_name = $wpdb->prefix . 'quiz_results';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        quiz_id BIGINT UNSIGNED NOT NULL,
        user_email VARCHAR(255) DEFAULT NULL,
        user_answers TEXT NOT NULL,
        score_percent INT NOT NULL,
        result_message TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

register_activation_hook( __FILE__, 'blp_qm_quiz_create_results_table' );

require_once BWP_QM_PATH . 'app/includes/post-type.php';
require_once BWP_QM_PATH . 'app/includes/install.php';
