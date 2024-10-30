jQuery(function($){

	function coschool_loader( type ) {
		if ( type == 'hide' ) $('#coschool-loader-container').hide();
		else $('#coschool-loader-container').show();
	}
	
	$('.coschool-help-heading').click(function(e){
		var $this = $(this);
		var $target = $this.data('target');
		$('.coschool-help-text:not('+$target+')').slideUp();
		if($($target).is(':hidden')){
			$($target).slideDown();
		}
		else {
			$($target).slideUp();
		}
	});

	$(document).on('click', '.coschool-instructor-user-action', function(e){
		e.preventDefault();
		var this_row 	= $(this).closest('tr');
		var type 		= $(this).data('action');
		var user_id 	= $(this).data('user');
		var this_btn 	= $(this);
		var parent 		= $(this).closest('td');

		this_btn.attr('disabled', true);

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			data: { action: 'coschool-instructor-approval', type: type, user_id: user_id, _wpnonce: COSCHOOL._wpnonce },
			success: function(resp) {
				
				if ( resp.status == 1 ) {
					parent.html( resp.action_btns );
				}
				this_btn.attr('disabled', false);
			},
			error: function(err) {
				console.log(err);
				this_btn.attr('disabled', false);
			}
		});
	});

	$(document).on('click', '.coschool-enrollment-action', function(e){
		
		e.preventDefault();
		var this_row 		= $(this).closest('tr');
		var type 			= $(this).data('action');
		var enrollment_id 	= $(this).data('enrollment');
		var this_btn 		= $(this);
		var parent 			= $(this).closest('td');

		this_btn.attr('disabled', true);

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			data: { action: 'coschool-enrollment-approval', type: type, enrollment_id: enrollment_id, _wpnonce: COSCHOOL._wpnonce },
			success: function(resp) {
				
				if ( resp.status == 1 ) {
					parent.html( resp.action_btns );
				}
				this_btn.attr('disabled', false);
			},
			error: function(err) {
				console.log(err);
				this_btn.attr('disabled', false);
			}
		});
	});

	$(document).on('click','.coschool-banner-uploader-btn, #coschool-course-banner-preview img',function(e) {
        e.preventDefault();

        var image   = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image  = image.state().get('selection').first();
            var image_id        = uploaded_image.toJSON().id;
            var image_url       = uploaded_image.toJSON().url;
            $('#coschool-course-banner').val( image_id );
            $('#coschool-course-banner-preview').show();
            $('#coschool-course-banner-preview img').attr('src', image_url );

	        $('.coschool-course-banner-uploader').hide();
	        $('.coschool-course-banner-cancel').show();
        });
    });

	$(document).on('click','.coschool-banner-cancel-btn',function(e) {
        e.preventDefault();

        $('#coschool-course-banner').val( '' );
        $('#coschool-course-banner-preview').hide();
        $('#coschool-course-banner-preview img').attr('src', '' );

        $('.coschool-course-banner-uploader').show();
	    $('.coschool-course-banner-cancel').hide();
    });
})