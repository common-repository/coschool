<?php
use Codexpert\CoSchool\Helper;
$disabled = '';
?>

<div id="coschool-payment">
	<div id="coschool-payment-student">
		<?php
		if( ! is_user_logged_in() ) {
			echo '
			<div class="coschool-enroll-login">
		    	<label class="coschool-login-toggle-btn" for="coschool-login">' . esc_html__( 'Have an account? Click to Login', 'coschool' ) . ' <i class="far fa-caret-square-down"></i></label>
			</div>';	
			echo do_shortcode( '[coschool_login]' );
		}
		?>
			
	<h2><?php _e( 'Personal Details', 'coschool' ); ?></h2>
		<form id="coschool-payment-form" method="post">
			<input type="hidden" name="action" value="coschool-payment">
			<input type="hidden" name="coschool-enrollment" value="1">
			<input type="hidden" name="payment-method" class="coschool-payment-method" value="1">
			<?php
			wp_nonce_field();

			do_action( 'coschool_enroll_before_payment_methods' );
			
			if( coschool_get_cart_totals( 'total' ) > 0 ) :

			echo '<div class="coschool-payment-methods" id="coschool-payment-methods">';
				echo '<h2>'. esc_html__( 'Payment method', 'coschool' ) .'</h2>';
				echo '<div class="coschool-payment-methods-wrap">';

				if( count( $methods = coschool_payment_methods() ) > 0 && 'test-payment' != coschool_payment_handler() ) :

					foreach ( $methods as $method ) :
					$label 	= Helper::get_option( 'coschool_payment', "{$method}_label", coschool_payment_providers( $method ) );
					$desc 	= Helper::get_option( 'coschool_payment', "{$method}_desc", coschool_payment_providers( $method ) );
					$button = Helper::get_option( 'coschool_payment', "{$method}_button", __( 'Pay', 'coschool' ) );

					echo "<div class='payment_method' id='payment_method-". esc_attr( $method ) ."'>

						<input type='radio' id='method-" . esc_attr( $method ) . "' name='payment_method' class='coschool-payment-method-input' value='" . esc_attr( $method ) . "' data-method='" . esc_attr( $method ) . "' data-button='" . esc_attr( $button ) . "' />
						<label for='method-" . esc_attr( $method ) . "' class='coschool-payment-method-label'>" . esc_html( $label ) . "</label>";

						do_action( 'coschool_payment_form', $method );

						echo "<div class='coschool_payment_form' id='coschool_payment_form_" . esc_attr( $method ) ."'>";

							echo '<label for="coschool-cart-element">' . 'Pay with' . esc_html( $label ) . '</label>';

							do_action( "coschool_payment_form_{$method}" );
						echo '</div>';


					echo "</div><!-- #payment_method-{$method} -->";
					endforeach;

				elseif ( 'test-payment' == coschool_payment_handler() ) :
					$method = 'test-payment';
					$button = __( 'Pay', 'coschool' );
					$label 	= __( 'Test Payment', 'coschool' );
					echo "<div class='payment_method' id='payment_method-{$method}'>

						<input type='radio' id='method-" . esc_attr( $method ) . "' name='payment_method' class='coschool-payment-method-input' value='" . esc_attr( $method ) . "' data-method='" . esc_attr( $method ) . "' data-button='" . esc_attr( $button ) . "' />
						<label for='method-" . esc_attr( $method ) . "' class='coschool-payment-method-label'>" . esc_html( $label ) . "</label>";

						do_action( 'coschool_payment_form', $method );

						echo "<div class='coschool_payment_form' id='coschool_payment_form_" . esc_attr( $method ) . "'>";

							echo '<label for="coschool-cart-element">' . 'Pay with ' . esc_html( $label ) . '</label>';

							do_action( "coschool_payment_form_{$method}" );
						echo '</div>';


					echo "</div><!-- #payment_method-{$method} -->";
				else:
					if( current_user_can('administrator') ){
						printf( __( 'No payment methods are enabled. Please <a href="%s">go to settings</a> and configure.', 'coschool' ), admin_url( 'admin.php?page=coschool' ) );
					}
					else {
						esc_html_e( 'No payment methods are enabled.', 'coschool' );
					}
				endif;

				echo '</div><!-- .coschool-payment-methods-wrap -->';

			echo '</div><!-- #coschool-payment-methods -->';

			$disabled = 'disabled';

			endif; // if( coschool_get_cart_totals( 'total' ) > 0 ) :
		
			echo '<div id="coschool-payment-submit">';
				echo '<input type="submit" id="coschool-payment-button" class="coschool-payment-button '. esc_attr( $disabled ) .'" value="' . esc_attr__( 'Enroll Now', 'coschool' ) . '" ' . esc_attr( $disabled ) . ' />';
			echo '</div>';

			do_action( 'coschool_enroll_after_payment_methods' );
			?>
		</form>
	</div>
</div>