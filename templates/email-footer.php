<?php
namespace Codexpert\CoSchool\App;
use Codexpert\CoSchool\Helper;
/**
 * The Template for displaying the content of the email footer
 *
 * This template can be overridden by copying it to yourtheme/coschool/email-footer.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>		</div><!-- #content -->
        </div><!-- #content-wrapper -->
        <div id="footer-wrapper" style="text-align: center;">
            <?php echo '<h3>' . coschool_site_name() . '</h3><p>' . get_option( 'blogdescription' ) . '</p>'; ?>
        </div>
    </div><!-- #wrap -->
</body>
</html>