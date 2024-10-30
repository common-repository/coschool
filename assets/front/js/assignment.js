jQuery(function($){

	function is_file_attached() {
		var files =	$('.coschool-assignment-upload-list ul li');
		if ( files.length < 1 ) return false;

		return true; 
	}

	$(document).on('click','.coschool-upload-btn',function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: true
        }).open()
        .on('select', function(e){
            var selection = image.state().get('selection');
            var html = '';
            selection.each(function(attachment) {
            	var id 			= attachment.toJSON().id;
            	var url 		= attachment.toJSON().url;
            	var filename 	= attachment.toJSON().filename;
            	html += '<li><input type="hidden" name="attachment_id[]" value="'+ id +'">'+ filename +' <span class="coschool-assignment-remove" data-attachment_id="'+ id +'"><i class="fas fa-times-circle"></i></span></li>';
		    });
		    $('.coschool-assignment-upload-list ul').append( html );
		    $('.coschool-submit-assignment').attr('disabled', ! is_file_attached() );
        });
    });

	$(document).on('click','.coschool-assignment-remove',function(e) {
        e.preventDefault();

    	var attachment_id 	= $(this).data('attachment_id');
    	var _this 			= $(this);
    	$.ajax({
			url: COSCHOOL.ajaxurl,
			data: { action: 'assignment-remove', attachment_id: attachment_id, _nonce: COSCHOOL.nonce },
			type: 'POST',
			dataType: 'JSON',
			success: function(resp){
        		_this.parent('li').remove();
				console.log(resp)
				$('.coschool-submit-assignment').attr('disabled', ! is_file_attached() );
			}
		})

    });

    $('#coschool-assignment-submit-form').submit(function (e) {
    	e.preventDefault();

    	if ( ! is_file_attached() ) {
    		$('.coschool-submit-assignment').attr('disabled', ! is_file_attached() );
    		return;
    	}

    	$('#coschool-modal').show();

		var $form = $(this);
		var $data = $form.serialize();
		$.ajax({
			url: COSCHOOL.ajaxurl,
			data: $data,
			type: 'POST',
			dataType: 'JSON',
			success: function(resp) {
				location.reload();
			},
			error: function(err) {
				console.log(err);
			},
		})
    });
});