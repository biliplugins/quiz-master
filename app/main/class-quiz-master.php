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
if ( ! class_exists( 'BWPQM_Quiz_Master' ) ) {

	/**
	 * Class for fofc core.
	 */
	class BWPQM_Quiz_Master {

		/**
		 * Constructor for class.
		 */
		public function __construct() {

			add_shortcode( 'simple_quiz', array( $this, 'blp_qm_quiz_shortcode' ) );

			add_action( 'wp_ajax_nopriv_simple_quiz_save_result', array( $this, 'simple_quiz_save_result' ) );
			add_action( 'wp_ajax_simple_quiz_save_result', array( $this, 'simple_quiz_save_result' ) );
		}

		public function blp_qm_quiz_shortcode( $atts ) {
			$atts    = shortcode_atts( array( 'id' => 0 ), $atts );
			$quiz_id = intval( $atts['id'] );

			if ( ! $quiz_id ) {
				return 'Invalid Quiz ID.';
			}

			$questions = get_post_meta( $quiz_id, 'quiz_questions', true );
			if ( ! $questions || ! is_array( $questions ) ) {
				return 'No questions found for this quiz.';
			}

			ob_start();

			wp_enqueue_script( 'simple-quiz-frontend', BWP_QM_URL . 'assets/js/plugin.js', array( 'jquery' ), BWP_QM_VERSION, true );

			wp_localize_script(
				'simple-quiz-frontend',
				'quizData',
				array(
					'quizId'        => $quiz_id,
					'questions'     => $questions,
					'require_email' => get_post_meta( $quiz_id, 'quiz_email_required', true ),
					'messages'      => get_post_meta( $quiz_id, 'quiz_result_messages', true ),
					'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				)
			);

			?>
	<div id="quiz-container"></div>
			<?php

			return ob_get_clean();
		}

		public function simple_quiz_save_result() {
				global $wpdb;
				$table = $wpdb->prefix . 'quiz_results';

				$quiz_id = intval( $_POST['quiz_id'] );
				$email   = sanitize_email( $_POST['email'] );
				$answers = maybe_serialize( $_POST['answers'] );
				$score   = intval( $_POST['score'] );
				$message = sanitize_text_field( $_POST['message'] );

				$wpdb->insert(
					$table,
					array(
						'quiz_id'        => $quiz_id,
						'user_email'     => $email,
						'user_answers'   => $answers,
						'score_percent'  => $score,
						'result_message' => $message,
					)
				);

				wp_send_json_success( array( 'status' => 'saved' ) );
		}
	}

	new BWPQM_Quiz_Master();
}
