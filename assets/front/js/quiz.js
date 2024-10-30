jQuery(function($){

	var quiz_form 		= $('#coschool-quiz-form');

	/**
	 * Timer Countdown
	 */
	function coschool_quiz_timer() {
		if ( $('#coschool-quiz-progress-bar').hasClass('answer_progress') ) return;
		var timerDom 	= $(".coschool-quiz-timer");
		var quizTime 	= COSCHOOL_QUIZ.quiz_time * 1000;
		var timeNow 	= new Date().getTime();
		var endTime 	= coschool_getCookie('coschool_qet');

		//coschool_qet - coschool quiz end time
		//coschool_qst - coschool quiz start time
		if ( endTime == null ) {
			endTime = timeNow + quizTime;
			coschool_setCookie( 'coschool_qet', endTime, 1 );
			coschool_setCookie( 'coschool_qst', timeNow, 1 );
		}

		// $('<input type="hidden" name="start_time" value="'+Math.floor(timeNow/4)+'">').insertAfter( $('.coschool-quiz', quiz_form ) );

		// Update the count down every 1 second
		var x = setInterval(function() {
			var now 	= new Date().getTime();
			var distance = endTime - now;
			var hours 	= Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			if ( hours < 10 ) hours = '0'+hours;
			if ( minutes < 10 ) minutes = '0'+minutes;
			if ( seconds < 10 ) seconds = '0'+seconds;

			timerDom.html( hours + " : " + minutes + " : " + seconds);

			//progress bar
			var width = 100 - (((distance/1000)/COSCHOOL_QUIZ.quiz_time)*100);
			if( width <= 0  ) width = 100;
			$('#coschool-quiz-progress-bar').css('width', width+'%');

			if (distance < 0) {
				clearInterval(x);
				coschool_eraseCookie( 'coschool_qet' );
				coschool_eraseCookie( 'coschool_qst' );
				coschool_eraseCookie( 'quiz_screen' );
				timerDom.html( COSCHOOL_QUIZ.time_up );
				quiz_form.submit();
			}
		}, 1000);
	}

	/**
	 * track how many questions are answerd 
	 */
	function coschool_track_answer() {
		var form = $('#coschool-quiz-form').serializeArray();
		var answerd = [];

		$.each(form,function(index, item) {
			if( item.name.indexOf('answer') >= 0 && item.value != '' ){
				var exists = false;

				$.each(answerd, function(_index, _item) {
				    if(item.name == _item.name){ exists = true }; 
				});
				if(exists == false) { answerd.push(item); }
			}
		});
		$('.coschool-answered-questions').html(answerd.length);

		if ( $('#coschool-quiz-progress-bar').hasClass('answer_progress') ) {
			var total = $('#coschool-total-questions').text();
			var width = (answerd.length/total)*100;
			$('#coschool-quiz-progress-bar').css('width', width+'%');
		}
	}

	/**
	 * check is a element is on viewport or not
	 */
	function isScrolledIntoView(elem) {
	    var docViewTop 		= $(window).scrollTop();
	    var docViewBottom 	= docViewTop + $(window).height();
	    var elemTop 		= $(elem).offset().top;
	    var elemBottom 		= elemTop + $(elem).height();

	    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
	}

	/**
	 * start count if or when quiz page reload/refresh 
	 */
	if ( quiz_form.length > 0 ) {
		coschool_quiz_timer();
	}

	/**
	 * Starting quiz
	 */
	$('#coschool-quiz-start-form').on('submit', function(e) {
		e.preventDefault();
		var data = $(this).serializeArray();
		$('#coschool-modal').show();
		$.ajax({
			url: COSCHOOL.ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			data: data,
			success: function(resp) {
				if ( resp.status == 1 ) {
					coschool_setCookie( 'quiz_screen', 'questions', 1 );
					$('#coschool-quiz-instruction, #coschool-quiz-result').html('');
					$('#coschool-quiz-questions').html( resp.questions_html );
					coschool_quiz_timer();
					$('#coschool-modal').hide();
				}
			},
			error: function(err) {
				console.log(err);
				$('#coschool-modal').hide();
			}
		});
	});

	/**
	 * Submit a quiz 
	 */
	$(document).on('submit', '#coschool-quiz-form', function(e) {
		e.preventDefault();
		var data = $(this).serializeArray();

        var emptyFieldsParents = [];

        $('input, textarea, :radio, :checkbox ').each(function() {
			var parentQuestion = $(this).closest('.coschool-quiz-answer');
			if (parentQuestion.hasClass('required')) {
				if (
					($(this).is('input') || $(this).is('textarea')) && $(this).val().trim() === '' ||
					($(this).is(':radio') && !$(':radio[name="' + $(this).attr('name') + '"]:checked').length) ||
					($(this).is(':checkbox') && !$(':checkbox[name="' + $(this).attr('name') + '"]:checked').length)
				) 
				{	
					var parentQuestion = $(this).closest('.coschool-quiz-answer');					
					emptyFieldsParents.push(parentQuestion);
				}
			}
        });
		if ( emptyFieldsParents.length > 0 ) {
			console.log( 'emptyFieldsParents', emptyFieldsParents );
			$.each(emptyFieldsParents, function(index, parentQuestion) {
				parentQuestion.parents('.coschool-quiz-question').find('.coschool-quiz-message').slideDown(600, function() {
					setTimeout(function() {
					  $('.coschool-quiz-message').slideUp(600);
					}, 2000);
				});
			});
			return;
		}
		coschool_eraseCookie( 'coschool_qet' );
		coschool_eraseCookie( 'coschool_qst' );
		coschool_eraseCookie( 'quiz_screen' );
		$('#coschool-modal').show();
		$.ajax({
			url: COSCHOOL.ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			data: data,
			success: function(resp) {
				$('#coschool-modal').hide();
					console.log(resp)
				if ( resp.status == 1 ) {
					$('#coschool-quiz-instruction, #coschool-quiz-questions').html('');
					$('#coschool-quiz-result').html( resp.result_html );
					setTimeout(function() {
						window.location.href = '';
					}, 2000);
				}				
			},
			error: function(err) {
				console.log(err);
				$('#coschool-modal').hide();
			}
		});
	});

	$(document).on('click', '.coschool-quiz-question input', function(e) {
		coschool_track_answer();
	});
	$(document).on('focusout', '.coschool-quiz-question input[type="text"], .coschool-quiz-question textarea', function(e) {
		coschool_track_answer();
	});

	$(window).on('resize scroll', function() {
	    if ( isScrolledIntoView('#coschool-quiz-progress-bar') ) {
	        $('#coschool-floating-counter').fadeOut();
	    } else {
	        $('#coschool-floating-counter').fadeIn();
	    }
	});

	/**
	 * Show question one by one
	 */

    $(document).ready(function () {

		let currentQuestion = 0;

		function showQuestion(direction) {

			const nextBtn 		= $('#next-btn');
			const prevBtn 		= $('#prev-btn');
			const questions 	= $(".coschool-quiz-question");
			const hiddenDiv 	= $('.show-submit');

			questions.eq(currentQuestion).hide();
			
			if (direction === 'next') {
				currentQuestion = (currentQuestion + 1) % questions.length;
			} else if (direction === 'prev') {
				currentQuestion = (currentQuestion - 1 + questions.length) % questions.length;
			}

			questions.eq(currentQuestion).show();

			if (currentQuestion === 0) {
				prevBtn.css('display', 'none');
			} else {
				prevBtn.css('display', 'flex');
			}

			if (currentQuestion === questions.length - 1) {
				nextBtn.css('display', 'none');
				hiddenDiv.show(); 
				
			} else {
				nextBtn.css('display', 'flex');
				hiddenDiv.hide();
			}

			questions.eq(currentQuestion).show();
			coschool_track_answer();
		}

		$(document).on('click', '#next-btn', function(e) {
			var visible_question = $(".coschool-quiz-question:visible");
			if (
				(visible_question.find('.required').length > 0 && visible_question.find('.required input, .required textarea').val() === "") ||
				(visible_question.find('.required textarea').length > 0 && visible_question.find('.required textarea').val() === "") ||
				(visible_question.find('.required input[type="radio"]').length > 0 && visible_question.find('.required input[type="radio"]:checked').length === 0) ||
				(visible_question.find('.required input[type="checkbox"]').length > 0 && visible_question.find('.required input[type="checkbox"]:checked').length === 0)
			){
				visible_question.find('.coschool-quiz-message').slideDown(600, function() {
					setTimeout(function() {
						visible_question.find('.coschool-quiz-message').slideUp(600);
					}, 2000);
				});
				e.preventDefault();
			}
			else{
				showQuestion('next');
			}
		});
		$(document).on('click', '#prev-btn', function(e) {
			showQuestion('prev');
		});
    });
});