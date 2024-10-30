jQuery(function ($) {
    $(".coschool-course-bundle-help-heading").click(function (e) {
        var $this = $(this);
        var $target = $this.data("target");
        $(".coschool-course-bundle-help-text:not(" + $target + ")").slideUp();
        if ($($target).is(":hidden")) {
            $($target).slideDown();
        } else {
            $($target).slideUp();
        }
    });

    $("#coschool-course-bundle_report-copy").click(function (e) {
        e.preventDefault();
        $("#coschool-course-bundle_tools-report").select();

        try {
            var successful = document.execCommand("copy");
            if (successful) {
                $(this).html('<span class="dashicons dashicons-saved"></span>');
            }
        } catch (err) {
            console.log("Oops, unable to copy!");
        }
    });

    $(".course-bundle-icon").on("click", function () {
        let value = $(this).parent().children(".coschool-bundle-url").val();
        let $temp = $("<input>");
        $("body").append($temp);
        $temp.val(value).select();
        document.execCommand("copy");
        $temp.remove();
        // console.log(value);
    });
});
