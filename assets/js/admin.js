jQuery(document).ready(function ($) {
  let $container = $('#quiz-questions');
  let questionIndex = 0;

  function renderQuestion(data = {}) {

    let index = questionIndex++;

    let html = `
      <div class="quiz-question-accordion">
      <div class="quiz-question-header">Question: ${index + 1}</div>
      <div class="quiz-question-content">
      <div class="quiz-question">
        <label>Question: ${index + 1}</label>
        <input type="text" name="quiz_questions[${index}][question]" value="${data.question || ''}" style="width: 100%;" />

        <div class="answers">
          <label>Answers:</label>`;

    let answers = data.answers || ['', ''];
    let correct = data.correct || 0;

    answers.forEach((answer, i) => {
      html += `
        <div class="answer">
          <input type="radio" name="quiz_questions[${index}][correct]" value="${i}" ${parseInt(correct) === i ? 'checked' : ''}>
          <input type="text" name="quiz_questions[${index}][answers][]" value="${answer}" class="quiz-input-answer"/>
          <button type="button" class="remove-answer button-link">Remove</button>
        </div>`;
    });

    html += `
        </div>
        <button type="button" class="add-answer button">Add More Answers</button>
        <textarea class="quiz-textarea" name="quiz_questions[${index}][explanation]" placeholder="Explanation (optional)">${data.explanation || ''}</textarea>
        <button type="button" class="remove-question button-link">Remove Question</button>
      </div>
      </div>
      </div>
      </div>
      </div>`;

    let $block = $(html);
    $container.append($block);
  }

  // Load existing questions from PHP
  if (typeof window.quizQuestions !== 'undefined' && Array.isArray(window.quizQuestions)) {
    window.quizQuestions.forEach(q => renderQuestion(q));
  }

  // Add new question
  $(document).on('click', '#add-question', function () {
    renderQuestion();
  });

  // Add new answer
  $container.on('click', '.add-answer', function () {
    let $answers = $(this).siblings('.answers');
    let qName = $(this).closest('.quiz-question').find('input[type="radio"]').attr('name');
    let answerCount = $answers.find('.answer').length;

    let html = `
      <div class="answer" style="margin-bottom: 5px;">
        <input type="radio" name="${qName}" value="${answerCount}">
        <input type="text" name="${qName.replace('[correct]', '[answers][]')}" value="" class="quiz-input-answer" />
        <button type="button" class="remove-answer button-link">Remove</button>
      </div>`;

    $answers.append(html);
  });

  // Remove answer
  $container.on('click', '.remove-answer', function () {
    $(this).closest('.answer').remove();
  });

  // Remove question
  $container.on('click', '.remove-question', function () {
    $(this).closest('.quiz-question').remove();
  });

  $(document).on('click', '.quiz-question-header', function () {

    $(this).toggleClass('active');

    var content = $(this).next('.quiz-question-content');
    content.toggleClass('hide');
  });

  let resultIndex = 1;

  $(document).on('click', '#add-result-message', function () {

    resultIndex = $( '.result-message' ).length;

    const newResult = `
      <div class="result-message" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px;">
        <label>Score Range:</label><br>
        <input type="number" name="quiz_result_messages[${resultIndex}][min]" placeholder="Min %" style="width: 80px;"> -
        <input type="number" name="quiz_result_messages[${resultIndex}][max]" placeholder="Max %" style="width: 80px;"><br><br>

        <label>Message:</label><br>
        <textarea class="result-message-editor" name="quiz_result_messages[${resultIndex}][message]"></textarea><br>

        <button type="button" class="remove-result-message button-link">Remove</button>
      </div>
    `;

    $('#quiz-results').append(newResult);

    // Initialize TinyMCE for new textarea
    if (typeof tinymce !== 'undefined') {
      tinymce.init({
        selector: 'textarea.result-message-editor',
        menubar: false,
        toolbar: 'bold italic underline bullist numlist link',
        height: 150
      });
    } else {
      console.warn('TinyMCE not loaded');
    }

    resultIndex++;
  });

  // Remove a result message
  $(document).on('click', '.remove-result-message', function () {
    $(this).closest('.result-message').remove();
  });

  // Init TinyMCE for initial message field.
  tinymce.init({
    selector: 'textarea.result-message-editor',
    menubar: false,
    toolbar: 'bold italic underline bullist numlist link',
    height: 150
  });

});
