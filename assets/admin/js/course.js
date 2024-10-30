jQuery(function($){
	$( ".course-content-list, .course-chapter-wrap, .course-faq-list" ).sortable({ axis : 'y', containment : 'parent' });

	$(document).on('click','.course-btn-add-content', function(e){
		e.preventDefault();
		var type = $(this).data('type');
		$('.modal-list-container').hide();

		$('[name="content_type"]').val(type);

		$('.course-content-btn').removeClass('active');
		$('.course-content-btn[data-type="'+type+'"]').addClass('active');

		$('#lesson-list-container').show();

		$('#course-content-modal').show();

		$('.course-content').removeClass('active-chapter');
		$(this).closest('.course-content').addClass('active-chapter');

	});

	$(document).on('click','.course-chapter-input', function(e){
		e.preventDefault();
		$('.course-content').removeClass('active-chapter');
		$(this).closest('.course-content').addClass('active-chapter');

	});

	$('.course-content-btn').on('click',function(e){
		e.preventDefault();
		var type = $(this).data('type');

		$('.course-content-btn').removeClass('active');
		$(this).addClass('active');

		$('[name="content_type"]').val(type);

		$('.modal-list-container').hide();
		$('#'+type+'-list-container').show();

		$('#course-content-modal').show();
	});

	$('.modal-close-btn').on('click',function(e){
		e.preventDefault();
		$('#course-content-modal').hide();
	});

	/**
	 * Insert html for course content into course content
	 */
	function coschool_insert_course_content( id, label, scope ) {
		// console.log(scope.parent().parent().hide());return;
		var content = '<li class="course-content-item lesson">\
					  	<span class="dashicons dashicons-menu"></span>\
					  	<span class="course-content-title">'+label+'</span>\
					  	<div class="course-content-actions">\
					  		<a href="'+COSCHOOL.edit_base+id+'" target="_blank" class="dashicons dashicons-edit course-content-edit"></a>\
					  		<a href="'+COSCHOOL.site_url+'/?p='+id+'" target="_blank" class="dashicons dashicons-visibility course-content-view"></a>\
					  		<a href="#" class="dashicons dashicons-no-alt course-content-remove"></a>\
					  	</div>\
					  	<input type="hidden" name="course_contents[##chapter##][]" value="'+id+'">\
					  </li>';

		if( $('.active-chapter .course-content-item').length > 0 ) {
			$(content).insertAfter('.active-chapter .course-content-item:last-child');
		}
		else {
			$('.active-chapter .course-content-list').html(content);
		}
	}

	/**
	 * Insert html for course content into course content
	 */
	function coschool_add_chapter( scope ) {
		var content = '<div class="course-content">\
							<span class="remove-chapter">&times;</span>\
							<div class="course-chapter-input-wrap">\
								<input type="text" name="course-chapter" class="course-chapter-input" placeholder="'+COSCHOOL.chapter_name+'" required>\
								<button class="course-btn-add-content" data-type="lesson">'+COSCHOOL.add_content+'</button>\
							</div>\
							<ul class="course-content-list">\
							</ul>\
						</div>';

		$(content).insertBefore($(scope));
	}

	/**
	 * Insert item into course content list options
	 */
	function coschool_insert_option( id, label, type ) {
		var option = '<option value="'+id+'">'+label+'</option>';

		if( $('#'+type+'-list option').length > 0 ) $(option).insertAfter('#'+type+'-list option:last-child');
		else $('#'+type+'-list option').html(option);
	}

	function coschool_fix_chapter_index() {
		$('.course-chapter-input').each(function(index, element){
			var $chapter = $(element).val();
			var $wrap = $(element).closest('.course-content');
			var $new_name = 'course_contents['+$chapter+'][]';
			$('.course-content-item input', $wrap).attr('name',$new_name);
		});
	}

	$('.add-new-item').on('click',function(e){
		e.preventDefault();
		var parent = $(this).parent();
		var type = $('[name="content_type"]').val();
		var value = $('#'+type+'-list').val();
		var label = $('#'+type+'-list option:selected').text();
		coschool_insert_course_content( value, label, $(this) );
		coschool_fix_chapter_index();
				
	});

	$('#create-new-item').on('click', function(e){
		e.preventDefault(e);
		var name = $('#new-item-title').val();
		var type = $('[name="content_type"]').val();
		var action = 'create-new-'+type;
		var button = $('#course-content-modal button');

		if( name == '' ){
			$('#new-item-error-notice').slideDown();
			return;
		}

		$('#new-item-error-notice').slideUp();
		button.attr('disabled', true);
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: { action: action, name: name, type: type, _wpnonce: COSCHOOL._wpnonce },
			dataType: 'JSON',
			success: function( resp ) {
				if ( resp.status == 1 ) {
					coschool_insert_course_content( resp.item_id, name, $(this) );
					coschool_insert_option( resp.item_id, name, type );
					coschool_fix_chapter_index();
				}
				button.attr('disabled', false);
				console.log(resp)
			},
			error: function( resp ) {
				console.log(resp)
			}
		});
	});

	$(document).on('click','.course-content-remove', function(e){
		e.preventDefault();
		$(this).closest('.course-content-item').remove();
	});

	$(document).on('click','.course-add-chapter', function(e){
		e.preventDefault();
		coschool_add_chapter($(this).parent());
		coschool_fix_chapter_index();
	});

	$(document).on('keyup','.course-chapter-input', function(e){
		e.preventDefault();
		coschool_fix_chapter_index();
	});

	$(document).on('click','.remove-chapter', function(e){
		e.preventDefault();
		if($('.course-content').length>1) {
			$(this).closest('.course-content').remove();
		}
		coschool_fix_chapter_index();
	});

	$('#publish').click(function(e){
		coschool_fix_chapter_index();
	});

	$(document).on( 'click', '.course-faq-single-list h4', function (e) {
		var parent = $(this).parent();
		$('.course-faq-single-content', parent).slideToggle();
	} );

	$(document).on( 'click', '.course-add-faq.button', function (e) {
		e.preventDefault();

		var count 	= $(".course-faq-list").children().length;

		var parent 	= $(this).parent();
		var clone 	= $('.course-faq-single-list-clone').clone();
		var _html 	= clone.removeClass('course-faq-single-list-clone');
		var html 	= _html.html().replace(/count/g, count)
		$('.course-faq-list').append('<div class="course-faq-single-list">'+ html +'</div>');
	} );

	$(document).on('keyup','.course-faq-title input[type="text"]',function(e) {
		e.preventDefault();
		var value 	= $(this).val();
		var parent 	= $(this).parents('.course-faq-single-list');
		$('h4 .course-faq-header', parent).text(value);
	});

	$(document).on('click','.course-faq-remove', function(e){
		e.preventDefault();
		$(this).closest('.course-faq-single-list').remove();
	});
})