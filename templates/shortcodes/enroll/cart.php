<?php
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\CourseBundle\Data as Bundle_Data;

global $coschool_cart;

$coupon = coschool_get_coupon();
?>

<div id="coschool-cart">
	<form id="coschool-cart-form" class="coschool-cart-wrapper">
		<input type="hidden" name="action" value="coschool-cart">
		<?php
		wp_nonce_field();
		
		if( $cart = coschool_get_cart_items() ) {
		    echo '
		    <table>
		    	<thead>
		    		<tr>
		    			<th>' . esc_html__( 'Course', 'coschool' ) . '</th>
		    			<th class="coschool-cart-price">' . esc_html__( 'Price', 'coschool' ) . '</th>
		    			<th></th>
		    		</tr>
		    	</thead>
		    	<tbody>';

		    $subtotal = $total = $discount = 0;

		    foreach ( $cart as $item ) {
		    	if( get_post_type( $item ) == 'course' ) {
		    		$item_data = new Course_Data( $item );
		    	}
		    	elseif( get_post_type( $item ) == 'bundle' ) {
		    		$item_data = new Bundle_Data( $item );
		    	}
		    	
		    	echo "<tr id='coschool-payment-" . esc_attr( $item ) . "'>
		    			<td class='coschool-cart-tiem-name'>" . esc_html( $item_data->get( 'title' ) ) . "</td>
		    			<td class='coschool-cart-tiem-price'><span>" . esc_html( coschool_price( $price = $item_data->get( 'price' ) ) ) . "</span></td>
		    			<td class='coschool-cart-tiem-remove'><a href='" . esc_url( add_query_arg( 'delist', $item_data->get( 'id' ) ) ) . "'>&times;</a></td>
		    		</tr>";		    	

		    	$subtotal += $price;
		    }

		    $coschool_cart['subtotal'] = $total = $subtotal;

		    echo '</tbody>
		    	<tfoot>';

		    	echo '<tr>
		    			<td class="coschool-cart-coupon" colspan=3>
		    				<label class="coschool-coupon-toggle-btn" for="coschool-coupon">' . esc_html__( 'Have a coupon code?', 'coschool' ) . ' <i class="far fa-caret-square-down"></i></label>
		    				<div class="coschool-coupon-fields" style="display: ' . ( $coupon ? 'block' : 'none' ) . '">
			    				<input type="text" id="coschool-coupon" placeholder="' . esc_html__( 'Coupon Code', 'coschool' ) . '" value="' . ( esc_attr( $coupon ) ) . '" />
			    				<button type="button" id="coschool-coupon-apply">' . esc_html__( 'Apply', 'coschool' ) . '</button>
		    					<div class="coschool-response-message"></div>
		    				</div>
		    			</td>
		    		</tr>';

		    	echo '
		    		<tr class="coschool-cart-subtotal-tr">
		    			<td>' . esc_html__( 'Subtotal', 'coschool' ) . '</td>
		    			<td class="coschool-cart-subtotal-price">' . esc_html( coschool_price( $subtotal ) ) . '</td>
		    			<td></td>
		    		</tr>';

		    	if( ( $discount = coschool_get_cart_totals( 'discount' ) ) > 0 ) {
			    	echo '<tr class="coschool-cart-discount-tr">
			    			<td>' . sprintf( __( 'Discount (%s)', 'coschool' ), esc_html( $coupon ) ) . '</td>
			    			<td class="coschool-cart-discount-price">- ' . esc_html( coschool_price( $discount ) ) . '</td>
			    			<td class="coschool-cart-tiem-remove"><a href="' . esc_url( add_query_arg( 'coupon-remove', 'true' ) ) . '" id="coschool-coupon-remove">&times;</a></td>
			    		</tr>';

		    		$coschool_cart['discount'] = $discount;
		    	}

		    	$total = $subtotal - $discount;
		    	$coschool_cart['total'] = $total;

		    	echo '<tr class="coschool-cart-total-tr">
		    			<td>' . esc_html__( 'Total', 'coschool' ) . '</td>
		    			<td class="coschool-cart-total-price">' . esc_html( coschool_price( $total ) ) . '</td>
		    			<td></td>
		    		</tr>
		    	</tfoot>
		    </table>';
		}
		?>
	</form>
</div>