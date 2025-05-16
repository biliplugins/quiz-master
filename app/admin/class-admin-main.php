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
if ( ! class_exists( 'BWPQM_Admin_Core' ) ) {

	/**
	 * Class for fofc core.
	 */
	class BWPQM_Admin_Core {

		/**
		 * Constructor for class.
		 */
		public function __construct() {

			add_action( 'save_post_quiz', array( $this, 'blp_qm_save_quiz_questions' ) );
			add_action( 'add_meta_boxes', array( $this, 'blp_qm_add_quiz_metabox' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'blp_qm_enqueue_admin_script' ) );
		}

		/**
		 * Admin Scripts.
		 *
		 * @return void
		 */
		public function blp_qm_enqueue_admin_script() {

			$js = 'admin.min.js';

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$js = 'admin.js';
			}

			wp_enqueue_editor();

			wp_enqueue_script(
				'bwp-qm-admin-script',
				BWP_QM_URL . 'assets/js/' . $js,
				array( 'jquery' ),
				BWP_QM_VERSION,
				true
			);

			$admin_css = 'admin.min.css';

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$admin_css = 'admin.css';
			}

			wp_enqueue_style(
				'bwp-qm-admin-style',
				BWP_QM_URL . 'assets/css/' . $admin_css,
				array(),
				BWP_QM_VERSION,
				'all'
			);
		}

		/**
		 * Quiz metabox for questions.
		 *
		 * @return void
		 */
		public function blp_qm_add_quiz_metabox() {
			add_meta_box(
				'spq_quiz_questions',
				esc_html__( 'Quiz Questions', 'quiz-master' ),
				array( $this, 'blp_qm_render_questions_metabox' ),
				'quiz',
				'normal',
				'high'
			);
		}

		/**
		 * Metabox render for quiz.
		 *
		 * @param object $post Quiz object.
		 * @return void
		 */
		public function blp_qm_render_questions_metabox( $post ) {

			$questions       = get_post_meta( $post->ID, 'quiz_questions', true );
			$email_required  = get_post_meta( $post->ID, 'quiz_email_required', true );
			$result_messages = get_post_meta( $post->ID, 'quiz_result_messages', true );

			// echo '<pre>';print_r($questions);echo '</pre>';

			?>
			<div id="quiz-questions-wrapper">
				<div class="email-require-wrap">
					<label>
						<input type="checkbox" name="quiz_email_required" value="1" <?php echo checked( $email_required, '1', false ); ?>>
						<?php esc_html_e( 'Require email before showing result' ); ?>
					</label>
				</div>

				<hr>
				<h3><?php esc_html_e( 'Questions' ); ?></h3>
				<div id="quiz-questions">
					<!-- Render each question block -->
				</div>
				<button type="button" class="button button-primary" id="add-question"><?php esc_html_e( 'Add Question' ); ?></button>

				<hr>
				<div id="quiz-results">
					<h3><?php esc_html_e( 'Result Messages' ); ?></h3>
					<?php

					if ( ! empty( $result_messages ) ) {

						$c = 0;

						foreach ( $result_messages as $result_message ) {

							?>
							<div class="result-message" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px;">
								<label><?php esc_html_e( 'Score Range:' ); ?></label>
								<input type="number" name="quiz_result_messages[<?php echo esc_attr( $c ); ?>][min]" placeholder="<?php esc_html_e( 'Min %' ); ?>" style="width: 80px;" value="<?php echo esc_attr( $result_message['min'] ); ?>"> -
								<input type="number" name="quiz_result_messages[<?php echo esc_attr( $c ); ?>][max]" placeholder="<?php esc_html_e( 'Max %' ); ?>" style="width: 80px;" value="<?php echo esc_attr( $result_message['max'] ); ?>">

								<label>Message:</label>
								<textarea class="result-message-editor" name="quiz_result_messages[<?php echo esc_attr( $c ); ?>][message]"><?php echo wp_kses_post( $result_message['message'] ); ?></textarea>
								<button type="button" class="remove-result-message button-link"><?php esc_html_e( 'Remove' ); ?></button>
							</div>
							<?php
							++$c;
						}
					} else {

						?>
					<div class="result-message" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px;">
						<label><?php esc_html_e( 'Score Range:' ); ?></label>
						<input type="number" name="quiz_result_messages[0][min]" placeholder="Min %" style="width: 80px;"> -
						<input type="number" name="quiz_result_messages[0][max]" placeholder="Max %" style="width: 80px;">

						<label><?php esc_html_e( 'Message:' ); ?></label>
						<textarea class="result-message-editor" name="quiz_result_messages[0][message]"></textarea>
						<button type="button" class="remove-result-message button-link"><?php esc_html_e( 'Remove' ); ?></button>
					</div>
					<?php } ?>
				</div>
				<button type="button" id="add-result-message" class="button button-primary"><?php esc_html_e( 'Add More Result Messages' ); ?></button>
			</div>
			<script>
				window.quizQuestions = <?php echo wp_json_encode( $questions ); ?>;
			</script>
			<?php
		}

		/**
		 * Save quiz data.
		 *
		 * @param integer $post_id Quiz ID.
		 * @return void
		 */
		public function blp_qm_save_quiz_questions( $post_id ) {

			remove_action( 'save_post_quiz', array( $this, 'blp_qm_save_quiz_questions' ) );

			$content = '[simple_quiz id="' . $post_id . '"]';

			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => $content,
				)
			);

			if ( isset( $_POST['quiz_questions'] ) ) {
				update_post_meta( $post_id, 'quiz_questions', $_POST['quiz_questions'] );
			}
			if ( isset( $_POST['quiz_email_required'] ) ) {
				update_post_meta( $post_id, 'quiz_email_required', $_POST['quiz_email_required'] );
			} else {
				delete_post_meta( $post_id, 'quiz_email_required' );
			}
			if ( isset( $_POST['quiz_result_messages'] ) ) {
				update_post_meta( $post_id, 'quiz_result_messages', $_POST['quiz_result_messages'] );
			}
		}
	}

	new BWPQM_Admin_Core();
}
