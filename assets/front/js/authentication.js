jQuery(function($){

	function pass_length_check(pass) {
		if ( pass.length < 8 ) {
			$('#coschool-register-password1-notice').html( COSCHOOL.pass1_error ).addClass('error');
			$('.coschool-form-submit-btn').attr('disabled', true);
		}
		else{
			$('#coschool-register-password1-notice').html('').removeClass('error');
			$('.coschool-form-submit-btn').attr('disabled', false);
		}
	}

	function match_the_passwords() {
		var form  = $('#coschool-registarion-form');
		var pass1 = $('input[name="password"]', form).val();
		var pass2 = $('input[name="confirm_password"]', form).val();
		if ( pass2 == '' ) { return; }
		if( pass1 != pass2 ) {
			$('#coschool-register-password2-notice').html( COSCHOOL.pass2_error ).addClass('error');
			$('.coschool-form-submit-btn').attr('disabled', true);
		}
		else{
			$('#coschool-register-password2-notice').html('').removeClass('error');
			$('.coschool-form-submit-btn').attr('disabled', false);
			pass_length_check(pass1);
		}
	}

	$(document).on('keyup', '#coschool-registarion-form input[type="password"]', function(e){
		var form  = $('#coschool-registarion-form');
		var pass1 = $('input[name="password"]', form).val();

		pass_length_check(pass1);
		match_the_passwords();

	});
});