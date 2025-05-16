<?php
/**
 * Class for custom work.
 *
 * @package Quiz_Master
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'BWPQM_Core' ) ) {

	/**
	 * Class for fofc core.
	 */
	class BWPQM_Core {

		/**
		 * Constructor for class.
		 */
		public function __construct() {

			$files = array(
				'app/main/class-custom-actions-filters',
				'app/main/class-quiz-master',
			);

			foreach ( $files as $file ) {
				// Include functions file.
				if ( file_exists( BWP_QM_PATH . $file . '.php' ) ) {
					require BWP_QM_PATH . $file . '.php';
				}
			}

			// Add custom style and script.
			add_action( 'wp_enqueue_scripts', array( $this, 'bwp_qm_enqueue_style_script' ) );
		}


		/**
		 * Plugin Styles.
		 *
		 * @return void
		 */
		public function bwp_qm_enqueue_style_script() {

			$css = 'plugin.min.css';

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$css = 'plugin.css';
			}

			// Plugin script.
			wp_enqueue_style(
				'plugin-name-style',
				BWP_QM_URL . 'assets/css/' . $css,
				'',
				BWP_QM_VERSION
			);
		}
	}

	new BWPQM_Core();
}
