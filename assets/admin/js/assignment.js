jQuery(function($){
	$('.coschool-assignment-help-heading').click(function(e){
		var $this = $(this);
		var $target = $this.data('target');
		$('.coschool-assignment-help-text:not('+$target+')').slideUp();
		if($($target).is(':hidden')){
			$($target).slideDown();
		}
		else {
			$($target).slideUp();
		}
	});

	$('#coschool-assignment_report-copy').click(function(e) {
		e.preventDefault();
		$('#coschool-assignment_tools-report').select();

		try {
			var successful = document.execCommand('copy');
			if( successful ){
				$(this).html('<span class="dashicons dashicons-saved"></span>');
			}
		} catch (err) {
			console.log('Oops, unable to copy!');
		}
	});
})