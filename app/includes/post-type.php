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

/**
 * Register Quiz Post Type.
 *
 * @return void
 */
function bwp_qm_register_quiz_post_type() {
	register_post_type(
		'quiz',
		array(
			'labels'       => array(
				'name'          => 'Quizzes',
				'singular_name' => 'Quiz',
			),
			'public'       => true,
			'menu_icon'    => 'dashicons-welcome-learn-more',
			'supports'     => array( 'title' ),
			'has_archive'  => true,
			'show_in_rest' => true,
		)
	);
}

add_action( 'init', 'bwp_qm_register_quiz_post_type' );
