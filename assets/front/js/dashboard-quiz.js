jQuery(function($){

	if( $( ".quiz-question-options, .quiz-question-wrap" ).length > 0 ){
		$( ".quiz-question-options, .quiz-question-wrap" ).sortable({ axis : 'y', containment : 'parent' });
	}
	/**
	 * Generate answer option
	 *
	 * type string input type checkbox|radio 
	 * count int the index of question set
	 */
	function generate_quiz_option( type, count ){
		var option = '<div class="quiz-question-option">\
			<input type="'+type+'" name="questions['+count+'][correct][]" class="quiz-question-correct">\
			<input type="text" name="questions['+count+'][options][]">';

		if( type != 'radio' ) option+= '<span class="dashicons dashicons-no-alt"></span>';
		
		option += '</div>';

		return option;
	}

	function attempt_notification(message) {
		var element = $('#coschool-attempt-notification');
		element.fadeOut().html( message ).fadeIn();
		setTimeout(function() {
			element.fadeOut();
		},1500);
	}

	/**
	 * set input value to closest checkbox|radio input value 
	 */
	$(document).on('keyup','.quiz-question-option input[type="text"]', function(e){
		var parent = $(this).parent();
		$('.quiz-question-correct', parent).val( $(this).val() );
	});

	/**
	 * Generate answer options to the respected section based on question type 
	 */
	$(document).on('change','.quiz-question-type select',function(e){
		var type 	= $(this).val();
		var parent 	= $(this).closest('.quiz-question-set');
		var count 	= parent.data('count');
		var section = $('.quiz-question-options-section', parent);

		if( type == 'true_false' ){
			var options = '<div class="quiz-question-options">';
				options += generate_quiz_option( 'radio', count );
				options += generate_quiz_option( 'radio', count );
				options += '</div>';

			section.html(options).slideDown();
		}
		else if( type == 'mcq' ){
			var options = '<div class="quiz-question-options">';
				options += generate_quiz_option( 'checkbox', count );
				options += '</div>';
				options += $('#quiz-question-option-btn-hidden').html();

			section.html(options).slideDown();
		}
		else{
			section.slideUp().html('');
		}
	});

	/**
	 * Generate new option input set 
	 */
	$(document).on('click','.quiz-question-option-btn button', function(e){
		e.preventDefault();
		var parent = $(this).closest('.quiz-question-set');
		var count 	= parent.data('count');
		var option = generate_quiz_option( 'checkbox', count );
		$(option).insertAfter( $('.quiz-question-option:last-child', parent) );
	});

	/**
	 * Remove a input option 
	 */
	$(document).on('click', '.quiz-question-option .dashicons', function(e){
		e.preventDefault();

		if( $('.quiz-question-option .dashicons').length > 1 ){
			$(this).parent().remove();
		}
	});

	/**
	 * Generate New question set 
	 */
	$(document).on('click','#quiz-add-new-question',function(e){
		e.preventDefault();
		var _set = $('.quiz-question-wrap .quiz-question-set');
		
		var count = 1;
		if( _set.length > 0 ){
			count += $(_set[_set.length-1]).data('count');
		}

		var question_set = $('#quiz-question-set-hidden').html().replace(/%%ques_set%%/g, count);
		$(question_set).insertBefore( $(this).parent() );
	});

	/**
	 * Remove a question set 
	 */
	$(document).on('click', '.remove-quiz-question-set span', function(e){
		e.preventDefault();
		var parent = $(this).closest('.quiz-question-set');
		var sets = $('.quiz-question-set');
		parent.remove();
	});

	$('#quiz-retake').on('change',function(e){
		if ( $(this).prop('checked') ) {
			$('.quiz-retake-count-container').slideDown();
			$('.quiz-retake-delay-container').slideDown();
		}
		else {
			$('.quiz-retake-count-container').slideUp();
			$('.quiz-retake-delay-container').slideUp();
		}
	}).change();

	$('#quiz-deadline-enabling').on('change',function(e){
		if ( $(this).prop('checked') ) {
			$('.quiz-deadline-date-container').slideDown();
		}
		else {
			$('.quiz-deadline-date-container').slideUp();
		}
	}).change();

	/**
	 * Review attempt
	 */

	 $('#coschool-attept-review-form .coschool-review-btn').on('click', function(e){
	 	e.preventDefault();
	 	var parent     	= $(this).closest('tr');
	 	var row_id 		= $('td.column-id', parent).text();
	 	var type 		= $(this).data('type');
	 	var point 		= $('.coschool-attempt-point', parent).val();
	 	$('#coschool-modal').show();
	 	$.ajax({
	 		url: ajaxurl,
	 		type: 'POST',
	 		dataType: 'JSON',
	 		data: { action: 'coschool-attempt-point', point: point, type: type, row_id: row_id, _wpnonce:COSCHOOL._wpnonce },
	 		success: function(resp) {
	 			console.log(resp)
	 			if ( resp.status == 1 ) {
	 				$('.coschool-attempt-point', parent).val( resp.point );
	 				attempt_notification( resp.message );
	 			}
	 		},
	 		error: function(err) {
	 			console.log(err);
	 			$('#coschool-modal').hide();
	 		}
	 	});
	 });

	 /**
	  * Review point 
	  */
	 $('.coschool-attempt-point').on('change', function(e){
	 	e.preventDefault();
	 	var parent     	= $(this).closest('tr');
	 	var row_id 		= $('td.column-id', parent).text();
	 	var point 		= $(this).val();
	 	$('#coschool-modal').show();
	 	$.ajax({
	 		url: ajaxurl,
	 		type: 'POST',
	 		dataType: 'JSON',
	 		data: { action: 'coschool-attempt-point', point: point, row_id: row_id, _wpnonce:COSCHOOL._wpnonce },
	 		success: function(resp) {
	 			console.log(resp)
	 			if ( resp.status == 1 ) {
	 				$('.coschool-attempt-point', parent).val( resp.point );
	 				attempt_notification( resp.message );
	 			}
	 		},
	 		error: function(err) {
	 			console.log(err);
	 			$('#coschool-modal').hide();
	 		}
	 	});
	 });

	 /**
	  * Review Feedback 
	  */
	 $('.coschool-attempt-review').on('change', function(e){
	 	var parent     	= $(this).closest('tr');
	 	var row_id 		= $('td.column-id', parent).text();
	 	var feedback 	= $(this).val();
	 	$('#coschool-modal').show();
	 	$.ajax({
	 		url: ajaxurl,
	 		type: 'POST',
	 		dataType: 'JSON',
	 		data: { action: 'coschool-attempt-feedback', feedback: feedback, row_id: row_id, _wpnonce:COSCHOOL._wpnonce },
	 		success: function(resp) {
	 			console.log(resp)
	 			if ( resp.status == 1 ) {
	 				$('.coschool-attempt-review', parent).val( resp.feedback );
	 				attempt_notification( resp.message );
	 			}
	 		},
	 		error: function(err) {
	 			console.log(err);
	 			$('#coschool-modal').hide();
	 		}
	 	});
	});

	$(document).on('change', '.coschool-switch-btn input[type="checkbox"]', function() {
		var parent = $(this).parents('.coschool-checkbox-enable-parent');

	    if(this.checked) {
	    	$('.coschool-checkbox-enable-content', parent).slideDown();
	    }
	    else {
	    	$('.coschool-checkbox-enable-content', parent).slideUp();
	    }
	});
});