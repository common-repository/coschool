<?php
namespace Codexpert\CoSchool\App;
/**
 * The Template for displaying no-access message
 *
 * This template can be overridden by copying it to yourtheme/coschool/no-access.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $course_data;
?>
<div class="coschool-restriction-notice">
	<img src="<?php echo esc_url( plugins_url( 'assets/img/sad.png', COSCHOOL ) ); ?>" alt="<?php esc_html_e( 'This lesson restriction', 'coschool' ); ?>">
	<p><?php esc_html_e( 'You don\'t have purchase this lesson.', 'coschool' ); ?></p>
	<a href="<?php echo esc_url( $course_data->get_purchase_url() ); ?>"><?php esc_html_e( 'Enroll Now', 'coschool' ); ?></a>
</div>