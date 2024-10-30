jQuery(function($) {
    $(document).on( 'keyup', '.coschool-student-info input[name="first_name"], .coschool-student-info input[name="email"], .coschool-student-info input[name="password"]', function (e) {
    	$(this).parent().removeClass('required');
    } );

    // store user input
    $(document).on( 'keyup', '.coschool-student-info input', function (e) {
        localStorage.setItem($(this).attr('name'), $(this).val());
    } );
});