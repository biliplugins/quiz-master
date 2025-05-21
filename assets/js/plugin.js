/* global  */

/**
 * Custom JS
 */
(function ($) {

	"use strict";

	window.BWPQM_Script = {

		init: function () {
			console.log('Custom Script Loaded.');
		}
	}

	jQuery(document).ready(function ($) {
		const container = $('#quiz-container');
		const questions = quizData.questions || [];
		const requireEmail = quizData.require_email === '1';
		const messages = quizData.messages || [];

		let current = 0;
		let answers = [];

		function renderQuestion(index) {

			const question = questions[index];
			if (typeof question === 'undefined') return showResult();

			const total = questions.length;
			const savedAnswer = answers[index];

			container.html(`
				<div class="quiz-progress">Question ${index + 1} of ${total}</div>
				<div class="quiz-question">
					<h3>${question.question}</h3>
					<ul class="blp-question-wrapper" data-key="${index}">
						${question.answers.map((opt, i) => `
							<li>
								<label>
									<input type="radio" name="quiz-answer" value="${i}" ${savedAnswer === i ? 'checked' : ''}> ${opt}
								</label>
							</li>
						`).join('')}
					</ul>
					<div class="quiz-nav-buttons">
						<button id="prev-question" class="navigate-question" data-type="prev" style="${index === 0 ? 'display:none;' : 'display:block;'}">Previous</button>
						<button id="next-question" class="navigate-question" data-type="next">Next</button>
					</div>
				</div>
			`);
		}

		function showEmailPrompt(scorePercent, resultMsg) {
			container.html(`
				<div class="quiz-email-form">
					<p class="email-label">Please enter your email to see your result:</p>
					<input type="email" id="quiz-user-email" class="quiz-email-input" placeholder="you@example.com">
					<button id="submit-email" class="quiz-email-button">Submit</button>
					<div id="quiz-loading" class="quiz-spinner" style="display: none;"></div>
					<p id="email-error" class="quiz-email-error"></p>
				</div>
			`);

			$('#submit-email').on('click', function () {
				const email = $('#quiz-user-email').val().trim();
				if (!email.match(/^\S+@\S+\.\S+$/)) {
					$('#email-error').text("Please enter a valid email address.");
					return;
				}

				$('#quiz-loading').show();

				showResultFinal(scorePercent, resultMsg);
			});
		}

		function showResult() {
			let correct = 0;

			questions.forEach((q, i) => {
				if (parseInt(answers[i]) === parseInt(q.correct)) correct++;
			});

			const scorePercent = Math.round((correct / questions.length) * 100);
			let resultMsg = '';

			if ( typeof( messages ) ) {

				console.log('messages',messages);

				messages.forEach(msg => {
					if (scorePercent >= parseInt(msg.min) && scorePercent <= parseInt(msg.max)) {
						resultMsg = msg.message;
					}
				});
			}

			if (requireEmail) {
				showEmailPrompt(scorePercent, resultMsg);
			} else {
				showResultFinal(scorePercent, resultMsg);
			}
		}

		function showResultFinal(scorePercent, resultMsg) {
			const email = $('#quiz-user-email').val() || '';
			const payload = {
				action: 'simple_quiz_save_result',
				quiz_id: quizData.quizId,
				answers: answers,
				email: email,
				score: scorePercent,
				message: resultMsg
			};

			$.post(quizData.ajaxurl, payload, function (response) {

				var current = 0;

				container.html(`
					<div class="quiz-result">
						<div class="quiz-progress-bar">
							<div class="quiz-progress-fill" style="width: 0%"></div>
						</div>
						<h3>Your Score: ${scorePercent}%</h3>
						<p>${resultMsg}</p>
					</div>
				`);

				var $fill = $('.quiz-progress-fill');

				var interval = setInterval(function() {
					if (current >= scorePercent) {
						clearInterval(interval);
					} else {
						current++;
						$fill.css('width', current + '%');
					}
				}, 10);
			});
		}

		// Next button handler
		container.on('click', '.navigate-question', function () {

			const type = $( this ).data( 'type' );

			const selected = $('input[name="quiz-answer"]:checked').val();
			if (typeof selected === 'undefined' && 'next' === type) {
				alert("Please select an answer.");
				return;
			}

			let currentIndex = $('.blp-question-wrapper').data('key');

			let question_index = 0;

			if ( 'next' === type ) {
				question_index = currentIndex + 1
			} else {
				question_index = currentIndex - 1
			}

			if ( 'next' === type ) {
				answers.push(parseInt(selected));
			}

			const isCorrect = parseInt(selected) === parseInt(quizData.questions[currentIndex].correct);
			const explanation = quizData.questions[currentIndex].explanation;

			if (explanation && 'next' === type) {
				container.find('.quiz-question').append(`
					<div class="quiz-explanation">
						<strong>${isCorrect ? 'Correct!' : 'Incorrect.'}</strong>
						<p>${explanation}</p>
						<button id="continue-question">Continue</button>
					</div>
				`);

				$('#next-question').hide(); // hide original next button
				$('#continue-question').on('click', () => {
					renderQuestion(currentIndex + 1);
				});
			} else {
				renderQuestion( question_index );
			}
		});

		// Init
		renderQuestion(current);
	});

})(jQuery);
