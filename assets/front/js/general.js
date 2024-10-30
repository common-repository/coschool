function coschool_setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function coschool_getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function coschool_eraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

jQuery(function($){
	$( ".coschool-chapter-list ul" ).sortable({ axis : 'y', containment : 'parent' });

	$('.course-layout-toggle-btn i').on('click', function(e){
		$('.course-layout-toggle-btn i').toggle();
		$('#coschool-courses, .course-card').toggleClass('list')
	});

	$('.coschool-tab-item').on('click', function(e){
		e.preventDefault();
		
		var tab = $(this).data('tab');
		$('.coschool-tab-item, .coschool-tab-content').removeClass('active');
		$(this).addClass('active');
		$('#coschool-tab-content-'+tab).addClass('active');
	});


	setTimeout(function() {
		$('.coschool-coures-items ul li.current-item').parents('.coschool-accordion').children('.coschool-accordion-header').click();
	}, 50);

	$('.coschool-accordion-header').on('click', function(e){
		e.preventDefault();

		var parent = $(this).closest('.coschool-accordion');

		if ( parent.hasClass('active') ) {
			$('.coschool-accordion').removeClass('active');
			$('.coschool-accordion .coschool-accordion-body').slideUp();
		}
		else {
			$('.coschool-accordion').removeClass('active');
			$('.coschool-accordion .coschool-accordion-body').slideUp();
			parent.addClass('active');		
			$('.coschool-accordion-body', parent).slideToggle();
		}
	});

	$(document).on( 'change', '#coschool-course-type', function (e) {
		$('.coschool-course-price').show();
		if ( 'free' == this.value ) {
			$('.coschool-course-price').hide();
		}
	} );

	$(document).on( 'click', '.coschool-add-content-btn', function (e) {
		e.preventDefault();
		$('.coschool-add-single-chapter').removeClass('active');
		
		$(this).parents('.coschool-add-single-chapter').addClass('active');
		$('.coschool-modal-wrap').addClass('is-visible');
	} );

	$(document).on( 'click', '.coschool-modal-close', function (e) {
		e.preventDefault();
		$('.coschool-modal-wrap').removeClass('is-visible');
	} );

	$(document).on( 'click', '#coschool-coupon-apply', function (e) {
		e.preventDefault();
		var $coupon = $('#coschool-coupon').val();

		if( '' == $coupon ) return;

		$('#coschool-modal').show();
		$.ajax({
			url: COSCHOOL.ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			data: { action: 'coschool-apply-coupon', _wpnonce: COSCHOOL.nonce, coupon: $coupon },
			success: function(resp) {
				console.log(resp);
				if( resp.status == 1 ) {
					// store to cookie
					coschool_setCookie( 'coschool_coupon', resp.coupon, 1 );
					location.reload();
				}
				else {
					$('.coschool-response-message').addClass('error').html( resp.message );
				}
				$('#coschool-modal').hide();
			},
			error: function(err) {
				console.log(err);
				$('#coschool-modal').hide();
			}
		});
	} );

	$(document).on( 'click', '.coschool-coupon-toggle-btn', function (e) {
		$(this).next('.coschool-coupon-fields').slideToggle('active');
	} );
	$(document).on( 'click', '.coschool-login-toggle-btn', function (e) {
		$('.coschool-login-form-container').slideToggle();
	} );

	$(document).on( 'click', '.coschool-payment-method-input', function (e) {
		method = $(this).data('method');
		button = $(this).data('button');
		$('#coschool-payment-button').removeClass().addClass('coschool-payment-button-'+method).val(button).attr('disabled',false);

		$('.coschool-payment-method').val(method);
	} );

	$(document).on( 'click', '.coschool-payment-button', function (e) {
		e.preventDefault();

		console.log($(this));
		$('#coschool-payment-form').submit();
	} );

	$('.coschool-mark-complete').click(function(e){
		e.preventDefault();
		var $this = $(this);
		var $content_id = $this.data('content_id');
		$this.attr('disabled',true);
		$('#coschool-modal').show();
		$.ajax({
			url: COSCHOOL.ajaxurl,
			data: { action : 'mark-complete', _wpnonce: COSCHOOL.nonce, content_id : $content_id },
			type: 'POST',
			dataType: 'JSON',
			success: function(resp) {
				location.reload();
			},
			error: function(err) {
				console.log(err);

			},
		});
	});

	$(document).on('click','#coschool-upload-btn',function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $('#image_url').val( image_url );
            $('#coschool-avatar img').attr('src', image_url );
            $('.coschool-avatar img').attr('src', image_url );
        });
    });

    $('#coschool-dashboard-profile-form').submit( function (e) {
    	e.preventDefault();
    	var data = $(this).serializeArray();
    	$.ajax({
    		url: COSCHOOL.ajaxurl,
    		data: data,
    		type: 'POST',
    		dataType: 'JSON',
    		success: function(resp) {
    			$('.coschool-response-message').html( resp.message ).show();
    			if ( resp.status == 1 ) {
    				setTimeout( function(){
	    				location.reload();
	    			}, 2000 );
    			}
    			else {
    				setTimeout( function(){
	    				$('.coschool-response-message').html( resp.message ).hide();
	    			}, 2000 );
    			}
    		},
    		error: function (error) {
    			console.log(error);	
    		}
    	});
    });

})