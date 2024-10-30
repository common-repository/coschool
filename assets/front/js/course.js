jQuery(function($){
	$(document).on( 'click', '.coschool-course-dashboard-my-course-btn.completed', function (e) {
		e.preventDefault();
		var course_id = $(this).data('course_id');
		$('#course_id').val( course_id );
		$('.coschool-my-course-review-modal').show();
	} );

	$(document).on( 'click', '.coschool-review-modal-close', function (e) {
		e.preventDefault();
		$('.coschool-my-course-review-modal').hide();
	} );

	$('.coschool-my-course-review-form form').submit(function(e){
		e.preventDefault();
		var $form = $(this);
		var $data = $form.serialize();
		$.ajax({
			url: COSCHOOL.ajaxurl,
			data: $data,
			type: 'POST',
			dataType: 'JSON',
			success: function(resp){
				$('.novelpress-response-message').html( resp.message ).show();
				$('.coschool-my-course-review-modal').hide();
				location.reload();
			},
			error: function (error) {
				console.log(error);				
			}
		})
	});

	$(document).on( 'click', '.coschool-wishlist-btn', function (e) {
		e.preventDefault();
		var _this 		= $(this);
		var course_id 	= _this.data('course_id');
		var added 		= _this.attr('data-added');

		$.ajax({
			url: COSCHOOL.ajaxurl,
			data: { action: 'wishlist', course_id: course_id, added: added, _nonce: COSCHOOL.nonce }, 
			type: 'POST',
			dataType: 'JSON',
			success: function (resp) {
				if ( resp.status == 1 ) {
					_this.addClass('added');
					_this.attr('data-added', '1');
				}
				
				if ( resp.action == 'remove' ) {
					_this.removeClass('added');
					_this.attr('data-added', '0');
				}
			},
			error: function (error) {
				console.log(error);				
			}
		});
	} );

	$(".coschool-share-button, .coschool-share-buttons").hover( function (e) {
		$('.coschool-share-buttons').addClass('hover');
	},
	function (e) {
		$('.coschool-share-buttons').removeClass('hover');
	} )
});