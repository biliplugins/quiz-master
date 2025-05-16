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
if ( ! class_exists( 'BWPQM_Actions_Filters' ) ) {

	/**
	 * Class for custom actions and filters.
	 */
	class BWPQM_Actions_Filters {

		/**
		 * Constructor for class.
		 */
		public function __construct() {
			// Hook goes here.
		}
	}

	new BWPQM_Actions_Filters();
}
