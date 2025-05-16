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

$question_id = 'q_' . $index;
?>
<div class="spq-question-block" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
	<label>Question:</label><br>
	<input type="text" name="spq_question_text[]" value="<?php echo esc_attr( $question['question'] ); ?>" style="width: 100%;"><br><br>

	<label>Options:</label><br>
	<?php foreach ( $question['options'] as $i => $option ) : ?>
		<input type="radio" name="spq_correct_option[<?php echo $index; ?>]" value="<?php echo $i; ?>" <?php echo $question['correct'] == $i ? 'checked' : ''; ?>>
		<input type="text" name="spq_question_options[<?php echo $index; ?>][]" value="<?php echo esc_attr( $option ); ?>" style="width: 90%;"><br>
	<?php endforeach; ?>

	<button type="button" class="button spq-remove-question">Remove</button>
</div>
