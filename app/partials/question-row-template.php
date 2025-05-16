<div class="spq-question-block" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
	<label>Question:</label><br>
	<input type="text" name="spq_question_text[]" value="" style="width: 100%;"><br><br>

	<label>Options:</label><br>
	<?php for ( $i = 0; $i < 4; $i++ ) : ?>
		<input type="radio" name="__name__" value="<?php echo $i; ?>">
		<input type="text" name="__options__[]" value="" style="width: 90%;"><br>
	<?php endfor; ?>

	<button type="button" class="button spq-remove-question">Remove</button>
</div>
