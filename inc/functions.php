<?php
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Payment\Cart;
use Codexpert\CoSchool\App\Coupon\Data as Coupon_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\Course_Bundle\Data as Bundle_Data;

if( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * Determines if the current page is an CoSchool page
 * 
 * @since 0.9
 * 
 * @return bool
 */
if( ! function_exists( 'is_coschool' ) ) :
	function is_coschool() {
		$coschool  = is_archive( 'course' ) ||
					is_singular( [ 'course', 'lesson', 'quiz', 'assignment' ] ) ||
					is_page( [ coschool_enroll_page(), coschool_dashboard_page() ] );

		return apply_filters( 'is_coschool', $coschool );
	}
endif;

/**
 * Gets the site's Name
 * 
 * @since 0.9
 * 
 * @return string $url the site name
 */
if( ! function_exists( 'coschool_site_name' ) ) :
	function coschool_site_name( $args = [] ) {
		$url = get_bloginfo( 'blogname' );

		return apply_filters( 'coschool-site_name', $url );
	}
endif;

/**
 * Gets the site's base URL
 * 
 * @uses get_bloginfo()
 * 
 * @since 0.9
 * 
 * @return string $url the site URL
 */
if( ! function_exists( 'coschool_site_url' ) ) :
	function coschool_site_url( $args = [] ) {
		$url = get_bloginfo( 'url' );

		if( count( $args ) > 0 ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}
endif;

/**
 * Capability to access CoSchool menu based on user role
 * 
 * @since 0.9
 * 
 * @return string|bool
 */
if( ! function_exists( 'coschool_menu_cap' ) ) :
	function coschool_menu_cap() {
		$user 		= wp_get_current_user();
		$user_roles = (array) $user->roles;

		if ( in_array( 'administrator', $user_roles ) ) {
			return 'manage_options';
		}

		if( in_array( 'instructor', $user_roles ) ) {
			return 'create_courses';
		}

		return false;
	}
endif;

/**
 * The payment handler
 *
 * What are you going to handle the payment with? Native processor or third-party plugins?
 * 
 * @since 0.9
 * 
 * @return string
 */
if( ! function_exists( 'coschool_payment_handler' ) ) :
	function coschool_payment_handler() {
		$payment_handler = Helper::get_option( 'coschool_payment', 'handler', 'native' );
		return $payment_handler;
	}
endif;

/**
 * Gets a list of (all) currencies
 * 
 * @param bool $simplified If set to true, returns this format `[ 'USD' => $ ]`
 * @param string $currency returns value for a given currency
 */
if( ! function_exists( 'coschool_currencies' ) ) :
	function coschool_currencies( $simplified = false, $currency = '' ) {

		$currencies = apply_filters(
			'coschool_currencies',
			array(
				'AED' => [
					'code'      => 'AED',
					'symbol'    => '&#x62f;.&#x625;',
				],
				'AFN' => [
					'code'      => 'AFN',
					'symbol'    => '&#x60b;',
				],
				'ALL' => [
					'code'      => 'ALL',
					'symbol'    => 'L',
				],
				'AMD' => [
					'code'      => 'AMD',
					'symbol'    => 'AMD',
				],
				'ANG' => [
					'code'      => 'ANG',
					'symbol'    => '&fnof;',
				],
				'AOA' => [
					'code'      => 'AOA',
					'symbol'    => 'Kz',
				],
				'ARS' => [
					'code'      => 'ARS',
					'symbol'    => '&#36;',
				],
				'AUD' => [
					'code'      => 'AUD',
					'symbol'    => '&#36;',
				],
				'AWG' => [
					'code'      => 'AWG',
					'symbol'    => 'Afl.',
				],
				'AZN' => [
					'code'      => 'AZN',
					'symbol'    => 'AZN',
				],
				'BAM' => [
					'code'      => 'BAM',
					'symbol'    => 'KM',
				],
				'BBD' => [
					'code'      => 'BBD',
					'symbol'    => '&#36;',
				],
				'BDT' => [
					'code'      => 'BDT',
					'symbol'    => '&#2547;&nbsp;',
				],
				'BGN' => [
					'code'      => 'BGN',
					'symbol'    => '&#1083;&#1074;.',
				],
				'BHD' => [
					'code'      => 'BHD',
					'symbol'    => '.&#x62f;.&#x628;',
				],
				'BIF' => [
					'code'      => 'BIF',
					'symbol'    => 'Fr',
				],
				'BMD' => [
					'code'      => 'BMD',
					'symbol'    => '&#36;',
				],
				'BND' => [
					'code'      => 'BND',
					'symbol'    => '&#36;',
				],
				'BOB' => [
					'code'      => 'BOB',
					'symbol'    => 'Bs.',
				],
				'BRL' => [
					'code'      => 'BRL',
					'symbol'    => '&#82;&#36;',
				],
				'BSD' => [
					'code'      => 'BSD',
					'symbol'    => '&#36;',
				],
				'BTC' => [
					'code'      => 'BTC',
					'symbol'    => '&#3647;',
				],
				'BTN' => [
					'code'      => 'BTN',
					'symbol'    => 'Nu.',
				],
				'BWP' => [
					'code'      => 'BWP',
					'symbol'    => 'P',
				],
				'BYR' => [
					'code'      => 'BYR',
					'symbol'    => 'Br',
				],
				'BYN' => [
					'code'      => 'BYN',
					'symbol'    => 'Br',
				],
				'BZD' => [
					'code'      => 'BZD',
					'symbol'    => '&#36;',
				],
				'CAD' => [
					'code'      => 'CAD',
					'symbol'    => '&#36;',
				],
				'CDF' => [
					'code'      => 'CDF',
					'symbol'    => 'Fr',
				],
				'CHF' => [
					'code'      => 'CHF',
					'symbol'    => '&#67;&#72;&#70;',
				],
				'CLP' => [
					'code'      => 'CLP',
					'symbol'    => '&#36;',
				],
				'CNY' => [
					'code'      => 'CNY',
					'symbol'    => '&yen;',
				],
				'COP' => [
					'code'      => 'COP',
					'symbol'    => '&#36;',
				],
				'CRC' => [
					'code'      => 'CRC',
					'symbol'    => '&#x20a1;',
				],
				'CUC' => [
					'code'      => 'CUC',
					'symbol'    => '&#36;',
				],
				'CUP' => [
					'code'      => 'CUP',
					'symbol'    => '&#36;',
				],
				'CVE' => [
					'code'      => 'CVE',
					'symbol'    => '&#36;',
				],
				'CZK' => [
					'code'      => 'CZK',
					'symbol'    => '&#75;&#269;',
				],
				'DJF' => [
					'code'      => 'DJF',
					'symbol'    => 'Fr',
				],
				'DKK' => [
					'code'      => 'DKK',
					'symbol'    => 'DKK',
				],
				'DOP' => [
					'code'      => 'DOP',
					'symbol'    => 'RD&#36;',
				],
				'DZD' => [
					'code'      => 'DZD',
					'symbol'    => '&#x62f;.&#x62c;',
				],
				'EGP' => [
					'code'      => 'EGP',
					'symbol'    => 'EGP',
				],
				'ERN' => [
					'code'      => 'ERN',
					'symbol'    => 'Nfk',
				],
				'ETB' => [
					'code'      => 'ETB',
					'symbol'    => 'Br',
				],
				'EUR' => [
					'code'      => 'EUR',
					'symbol'    => '&euro;',
				],
				'FJD' => [
					'code'      => 'FJD',
					'symbol'    => '&#36;',
				],
				'FKP' => [
					'code'      => 'FKP',
					'symbol'    => '&pound;',
				],
				'GBP' => [
					'code'      => 'GBP',
					'symbol'    => '&pound;',
				],
				'GEL' => [
					'code'      => 'GEL',
					'symbol'    => '&#x20be;',
				],
				'GGP' => [
					'code'      => 'GGP',
					'symbol'    => '&pound;',
				],
				'GHS' => [
					'code'      => 'GHS',
					'symbol'    => '&#x20b5;',
				],
				'GIP' => [
					'code'      => 'GIP',
					'symbol'    => '&pound;',
				],
				'GMD' => [
					'code'      => 'GMD',
					'symbol'    => 'D',
				],
				'GNF' => [
					'code'      => 'GNF',
					'symbol'    => 'Fr',
				],
				'GTQ' => [
					'code'      => 'GTQ',
					'symbol'    => 'Q',
				],
				'GYD' => [
					'code'      => 'GYD',
					'symbol'    => '&#36;',
				],
				'HKD' => [
					'code'      => 'HKD',
					'symbol'    => '&#36;',
				],
				'HNL' => [
					'code'      => 'HNL',
					'symbol'    => 'L',
				],
				'HRK' => [
					'code'      => 'HRK',
					'symbol'    => 'kn',
				],
				'HTG' => [
					'code'      => 'HTG',
					'symbol'    => 'G',
				],
				'HUF' => [
					'code'      => 'HUF',
					'symbol'    => '&#70;&#116;',
				],
				'IDR' => [
					'code'      => 'IDR',
					'symbol'    => 'Rp',
				],
				'ILS' => [
					'code'      => 'ILS',
					'symbol'    => '&#8362;',
				],
				'IMP' => [
					'code'      => 'IMP',
					'symbol'    => '&pound;',
				],
				'INR' => [
					'code'      => 'INR',
					'symbol'    => '&#8377;',
				],
				'IQD' => [
					'code'      => 'IQD',
					'symbol'    => '&#x62f;.&#x639;',
				],
				'IRR' => [
					'code'      => 'IRR',
					'symbol'    => '&#xfdfc;',
				],
				'IRT' => [
					'code'      => 'IRT',
					'symbol'    => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
				],
				'ISK' => [
					'code'      => 'ISK',
					'symbol'    => 'kr.',
				],
				'JEP' => [
					'code'      => 'JEP',
					'symbol'    => '&pound;',
				],
				'JMD' => [
					'code'      => 'JMD',
					'symbol'    => '&#36;',
				],
				'JOD' => [
					'code'      => 'JOD',
					'symbol'    => '&#x62f;.&#x627;',
				],
				'JPY' => [
					'code'      => 'JPY',
					'symbol'    => '&yen;',
				],
				'KES' => [
					'code'      => 'KES',
					'symbol'    => 'KSh',
				],
				'KGS' => [
					'code'      => 'KGS',
					'symbol'    => '&#x441;&#x43e;&#x43c;',
				],
				'KHR' => [
					'code'      => 'KHR',
					'symbol'    => '&#x17db;',
				],
				'KMF' => [
					'code'      => 'KMF',
					'symbol'    => 'Fr',
				],
				'KPW' => [
					'code'      => 'KPW',
					'symbol'    => '&#x20a9;',
				],
				'KRW' => [
					'code'      => 'KRW',
					'symbol'    => '&#8361;',
				],
				'KWD' => [
					'code'      => 'KWD',
					'symbol'    => '&#x62f;.&#x643;',
				],
				'KYD' => [
					'code'      => 'KYD',
					'symbol'    => '&#36;',
				],
				'KZT' => [
					'code'      => 'KZT',
					'symbol'    => '&#8376;',
				],
				'LAK' => [
					'code'      => 'LAK',
					'symbol'    => '&#8365;',
				],
				'LBP' => [
					'code'      => 'LBP',
					'symbol'    => '&#x644;.&#x644;',
				],
				'LKR' => [
					'code'      => 'LKR',
					'symbol'    => '&#xdbb;&#xdd4;',
				],
				'LRD' => [
					'code'      => 'LRD',
					'symbol'    => '&#36;',
				],
				'LSL' => [
					'code'      => 'LSL',
					'symbol'    => 'L',
				],
				'LYD' => [
					'code'      => 'LYD',
					'symbol'    => '&#x644;.&#x62f;',
				],
				'MAD' => [
					'code'      => 'MAD',
					'symbol'    => '&#x62f;.&#x645;.',
				],
				'MDL' => [
					'code'      => 'MDL',
					'symbol'    => 'MDL',
				],
				'MGA' => [
					'code'      => 'MGA',
					'symbol'    => 'Ar',
				],
				'MKD' => [
					'code'      => 'MKD',
					'symbol'    => '&#x434;&#x435;&#x43d;',
				],
				'MMK' => [
					'code'      => 'MMK',
					'symbol'    => 'Ks',
				],
				'MNT' => [
					'code'      => 'MNT',
					'symbol'    => '&#x20ae;',
				],
				'MOP' => [
					'code'      => 'MOP',
					'symbol'    => 'P',
				],
				'MRU' => [
					'code'      => 'MRU',
					'symbol'    => 'UM',
				],
				'MUR' => [
					'code'      => 'MUR',
					'symbol'    => '&#x20a8;',
				],
				'MVR' => [
					'code'      => 'MVR',
					'symbol'    => '.&#x783;',
				],
				'MWK' => [
					'code'      => 'MWK',
					'symbol'    => 'MK',
				],
				'MXN' => [
					'code'      => 'MXN',
					'symbol'    => '&#36;',
				],
				'MYR' => [
					'code'      => 'MYR',
					'symbol'    => '&#82;&#77;',
				],
				'MZN' => [
					'code'      => 'MZN',
					'symbol'    => 'MT',
				],
				'NAD' => [
					'code'      => 'NAD',
					'symbol'    => 'N&#36;',
				],
				'NGN' => [
					'code'      => 'NGN',
					'symbol'    => '&#8358;',
				],
				'NIO' => [
					'code'      => 'NIO',
					'symbol'    => 'C&#36;',
				],
				'NOK' => [
					'code'      => 'NOK',
					'symbol'    => '&#107;&#114;',
				],
				'NPR' => [
					'code'      => 'NPR',
					'symbol'    => '&#8360;',
				],
				'NZD' => [
					'code'      => 'NZD',
					'symbol'    => '&#36;',
				],
				'OMR' => [
					'code'      => 'OMR',
					'symbol'    => '&#x631;.&#x639;.',
				],
				'PAB' => [
					'code'      => 'PAB',
					'symbol'    => 'B/.',
				],
				'PEN' => [
					'code'      => 'PEN',
					'symbol'    => 'S/',
				],
				'PGK' => [
					'code'      => 'PGK',
					'symbol'    => 'K',
				],
				'PHP' => [
					'code'      => 'PHP',
					'symbol'    => '&#8369;',
				],
				'PKR' => [
					'code'      => 'PKR',
					'symbol'    => '&#8360;',
				],
				'PLN' => [
					'code'      => 'PLN',
					'symbol'    => '&#122;&#322;',
				],
				'PRB' => [
					'code'      => 'PRB',
					'symbol'    => '&#x440;.',
				],
				'PYG' => [
					'code'      => 'PYG',
					'symbol'    => '&#8370;',
				],
				'QAR' => [
					'code'      => 'QAR',
					'symbol'    => '&#x631;.&#x642;',
				],
				'RMB' => [
					'code'      => 'RMB',
					'symbol'    => '&yen;',
				],
				'RON' => [
					'code'      => 'RON',
					'symbol'    => 'lei',
				],
				'RSD' => [
					'code'      => 'RSD',
					'symbol'    => '&#1088;&#1089;&#1076;',
				],
				'RUB' => [
					'code'      => 'RUB',
					'symbol'    => '&#8381;',
				],
				'RWF' => [
					'code'      => 'RWF',
					'symbol'    => 'Fr',
				],
				'SAR' => [
					'code'      => 'SAR',
					'symbol'    => '&#x631;.&#x633;',
				],
				'SBD' => [
					'code'      => 'SBD',
					'symbol'    => '&#36;',
				],
				'SCR' => [
					'code'      => 'SCR',
					'symbol'    => '&#x20a8;',
				],
				'SDG' => [
					'code'      => 'SDG',
					'symbol'    => '&#x62c;.&#x633;.',
				],
				'SEK' => [
					'code'      => 'SEK',
					'symbol'    => '&#107;&#114;',
				],
				'SGD' => [
					'code'      => 'SGD',
					'symbol'    => '&#36;',
				],
				'SHP' => [
					'code'      => 'SHP',
					'symbol'    => '&pound;',
				],
				'SLL' => [
					'code'      => 'SLL',
					'symbol'    => 'Le',
				],
				'SOS' => [
					'code'      => 'SOS',
					'symbol'    => 'Sh',
				],
				'SRD' => [
					'code'      => 'SRD',
					'symbol'    => '&#36;',
				],
				'SSP' => [
					'code'      => 'SSP',
					'symbol'    => '&pound;',
				],
				'STN' => [
					'code'      => 'STN',
					'symbol'    => 'Db',
				],
				'SYP' => [
					'code'      => 'SYP',
					'symbol'    => '&#x644;.&#x633;',
				],
				'SZL' => [
					'code'      => 'SZL',
					'symbol'    => 'L',
				],
				'THB' => [
					'code'      => 'THB',
					'symbol'    => '&#3647;',
				],
				'TJS' => [
					'code'      => 'TJS',
					'symbol'    => '&#x405;&#x41c;',
				],
				'TMT' => [
					'code'      => 'TMT',
					'symbol'    => 'm',
				],
				'TND' => [
					'code'      => 'TND',
					'symbol'    => '&#x62f;.&#x62a;',
				],
				'TOP' => [
					'code'      => 'TOP',
					'symbol'    => 'T&#36;',
				],
				'TRY' => [
					'code'      => 'TRY',
					'symbol'    => '&#8378;',
				],
				'TTD' => [
					'code'      => 'TTD',
					'symbol'    => '&#36;',
				],
				'TWD' => [
					'code'      => 'TWD',
					'symbol'    => '&#78;&#84;&#36;',
				],
				'TZS' => [
					'code'      => 'TZS',
					'symbol'    => 'Sh',
				],
				'UAH' => [
					'code'      => 'UAH',
					'symbol'    => '&#8372;',
				],
				'UGX' => [
					'code'      => 'UGX',
					'symbol'    => 'UGX',
				],
				'USD' => [
					'code'      => 'USD',
					'symbol'    => '&#36;',
				],
				'UYU' => [
					'code'      => 'UYU',
					'symbol'    => '&#36;',
				],
				'UZS' => [
					'code'      => 'UZS',
					'symbol'    => 'UZS',
				],
				'VEF' => [
					'code'      => 'VEF',
					'symbol'    => 'Bs F',
				],
				'VES' => [
					'code'      => 'VES',
					'symbol'    => 'Bs.S',
				],
				'VND' => [
					'code'      => 'VND',
					'symbol'    => '&#8363;',
				],
				'VUV' => [
					'code'      => 'VUV',
					'symbol'    => 'Vt',
				],
				'WST' => [
					'code'      => 'WST',
					'symbol'    => 'T',
				],
				'XAF' => [
					'code'      => 'XAF',
					'symbol'    => 'CFA',
				],
				'XCD' => [
					'code'      => 'XCD',
					'symbol'    => '&#36;',
				],
				'XOF' => [
					'code'      => 'XOF',
					'symbol'    => 'CFA',
				],
				'XPF' => [
					'code'      => 'XPF',
					'symbol'    => 'Fr',
				],
				'YER' => [
					'code'      => 'YER',
					'symbol'    => '&#xfdfc;',
				],
				'ZAR' => [
					'code'      => 'ZAR',
					'symbol'    => '&#82;',
				],
				'ZMW' => [
					'code'      => 'ZMW',
					'symbol'    => 'ZK',
				],
			)
		);

		if( $simplified ) {
			$_currencies = [];
			foreach ( $currencies as  $_currency ) {
				$_currencies[ $_currency['code'] ] = "{$_currency['code']} ({$_currency['symbol']})";
			}

			$currencies = $_currencies;
		}

		if( '' != $currency ) {
			return $currencies[ $currency ];
		}

		return $currencies;
	}
endif;

/**
 * Is test mode enabled?
 * 
 * @since 0.9
 * 
 * @return bool
 */
if( ! function_exists( 'coschool_test_mode' ) ) :
	function coschool_test_mode() {
		$test_mode 	= Helper::get_option( 'coschool_payment', 'test_mode' );

		return $test_mode == 'on';
	}
endif;

/**
 * The currency symbol
 * 
 * @param string $show symbol|code
 * 
 * @since 0.9
 * 
 * @return string the currency symbol
 */
if( ! function_exists( 'coschool_get_currency' ) ) :
	function coschool_get_currency( $show = 'symbol' ) {
		$set_currency 	= Helper::get_option( 'coschool_payment', 'currency', 'USD' );
		$currency 		= coschool_currencies( false, $set_currency );

		return $currency[ $show ];
	}
endif;

/**
 * Formats price with currency
 * 
 * @param float|int $price the course price
 * @param bool      $verbal either we want a `free` text or zero value for free courses
 * 
 * @since 0.9
 * 
 * @return string $formatted_price the site URL
 */
if( ! function_exists( 'coschool_price' ) ) :
	function coschool_price( $price, $verbal = true ) {
		
		if( $price == 0 && $verbal ) {
			return __( 'Free', 'coschool' );
		}
		
		$formatted_price = coschool_get_currency() . round( $price, 2 );

		return apply_filters( 'coschool_price', $formatted_price, $price );
	}
endif;

/**
 * Populates rating stars
 * 
 * @since 0.9
 * 
 * @return string $stars
 */
if( ! function_exists( 'coschool_populate_stars' ) ) :
	function coschool_populate_stars( $rating = 5, $max = 5 ) {
		$stars 		= '';

		$full_stars = floor( $rating );
		$fraction 	= $rating - $full_stars;

		
		$half_stars = 0;
		if( $fraction >= 0.25 && $fraction < 0.75 ) {
			$half_stars = 1;
		}
		elseif( $fraction >= 0.75 ) {
			$full_stars++;
		}

		$empty_stars = $max - ( $full_stars + $half_stars );

		$stars .= str_repeat( '<span class="dashicons dashicons-star-filled"></span>', $full_stars );
		$stars .= str_repeat( '<span class="dashicons dashicons-star-half"></span>', $half_stars );
		$stars .= str_repeat( '<span class="dashicons dashicons-star-empty"></span>', $empty_stars );

		return $stars;
	}
endif;

/**
 * List of available payment integration plugins
 * 
 * @since 0.9
 * 
 * @return array|string $dependencies[] or dependency name based on if a param is passed
 */
if( ! function_exists( 'coschool_dependencies' ) ) :
	function coschool_dependencies( $dependency = '' ) {

		$dependencies = [
			'test-payment' 	=> __( 'Test Payment', 'coschool' ),
			'native' 		=> __( 'Native', 'coschool' ),
		];

		$dependencies = apply_filters( 'coschool_payment_methods', $dependencies );

		if( $dependency != '' && array_key_exists( $dependency, $dependencies ) ) {
			return $dependencies[ $dependency ];
		}

		return $dependencies;
	}
endif;

/**
 * Available payment providers to be used
 * 
 * @param string $provider A payment provider's name
 * 
 * @return array | [array]
 */
if( ! function_exists( 'coschool_payment_providers' ) ) :
	function coschool_payment_providers( $provider = '' ) {
		
		$providers = apply_filters( 'coschool_payment-providers', [
			'paypal' => __( 'PayPal', 'coschool' )
		] );

		if( $provider != '' && array_key_exists( $provider, $providers ) ) {
			return $providers[ $provider ];
		}

		return $providers;
	}
endif;

/**
 * Chosen payments methods from the settings
 * 
 * @return []
 */
if( ! function_exists( 'coschool_payment_methods' ) ) :
	function coschool_payment_methods() {
		$methods = Helper::get_option( 'coschool_payment', 'methods', [] );

		return apply_filters( 'coschool_active_payment_methods', $methods );
	}
endif;

/**
 * Is the given payment method active?
 * 
 * @return bool
 */
function coschool_payment_method_active( $method ) {
	return 'native' == coschool_payment_handler() && in_array( $method, coschool_payment_methods() );
}

/**
 * Sanitizes an input
 * 
 * @param mix $value the input to sanitize
 * @param string $type method to use text|textarea|email|file|class|key|title|user|option|meta
 * 
 * @since 0.9
 * 
 * @return mix the sanitized value
 */
// if( ! function_exists( 'coschool_sanitize' ) ) :
// 	function coschool_sanitize( $value, $type = 'text' ) {
// 		if( ! in_array( $type, [ 'textarea', 'email', 'file', 'class', 'key', 'title', 'user', 'option', 'meta' ] ) ) {
// 			$type = 'text';
// 		}

// 		if( array_key_exists( $type,
// 			$maps = [
// 				'text'      => 'text_field',
// 				'textarea'  => 'textarea_field',
// 				'file'      => 'file_name',
// 				'class'     => 'html_class',
// 			]
// 		) ) {
// 			$type = $maps[ $type ];
// 		}

// 		$fn = "sanitize_{$type}";

// 		return $fn( $value );
// 	}
// endif;

/**
 * Publish Course Issue
 * 
 * @author Sadekur Rahman <shadekur.rahman60@gmail.com>
 * 
 * @return input string|int
 */

if( ! function_exists( 'coschool_sanitize' ) ) :
	function coschool_sanitize( $input, $type = 'text' ) {
		if ( is_array($input) ) {
			$sanitized = [];

			foreach ( $input as $key => $value ) {
				$sanitized[$key] = coschool_sanitize( $value, $type );
			}

			return $sanitized;
		}

		if ( ! in_array($type, ['textarea', 'email', 'file', 'class', 'key', 'title', 'user', 'option', 'meta']) ) {
			$type = 'text';
		}

		if ( array_key_exists($type, $maps = ['text' => 'text_field', 'textarea' => 'textarea_field', 'file' => 'file_name', 'class' => 'html_class']) ) {
			$type = $maps[$type];
		}

		if ( preg_match('/<[^>]*>/', $input) ) {
			return wp_kses_post( $input );
		} else {
			$fn = "sanitize_{$type}";
			return $fn( $input );
		}
	}
endif;

/**
 * Student dashboard tab items
 * 
 * @author Jakaria Istauk <jakariamd35@gmail.com>
 * 
 * @return array tab items
 */
if( ! function_exists( 'coschool_student_dashboard_nav_items' ) ) :
	function coschool_student_dashboard_nav_items() {
		$nav_items = [
			''     => [
				'label' => __( 'Dashboard', 'coschool' ),
				'icon'  => '<i class="fas fa-tachometer-alt"></i>'
			],
			'courses' 	=> [
				'label' => __( 'Courses', 'coschool' ),
				'icon'  => '<i class="fas fa-graduation-cap"></i>'
			],
			'wishlist'      	=> [
				'label' => __( 'Wishlist', 'coschool' ),
				'icon'  => '<i class="fas fa-heart"></i>'
			],
			'transaction'      	=> [
				'label' => __( 'Transactions', 'coschool' ),
				'icon'  => '<i class="fas fa-dollar-sign"></i>'
			],
			'my-profile'    => [
				'label' => __( 'Profile', 'coschool' ),
				'icon'  => '<i class="fas fa-user"></i>'
			],
			'logout'      		=> [
				'label' => __( 'Logout', 'coschool' ),
				'icon'  => '<i class="fas fa-sign-out-alt"></i>'
			],
		];

		return apply_filters( 'coschool_student_dashboard_nav_items', $nav_items );
	}
endif;

/**
 * Enrollment page
 * 
 * @param bool $url Either we need the URL or the page ID
 * 
 * @return string|int
 */
if( ! function_exists( 'coschool_enroll_page' ) ) :
	function coschool_enroll_page( $url = false ) {
		$enroll = Helper::get_option( 'coschool_general', 'enroll_page' );

		if( $url ) {
			return get_permalink( $enroll );
		}

		return $enroll;
	}
endif;

/**
 * Dashboard page
 * 
 * @param bool $url Either we need the URL or the page ID
 * 
 * @return string|int
 */
if( ! function_exists( 'coschool_dashboard_page' ) ) :
	function coschool_dashboard_page( $url = false ) {
		$dashboard = Helper::get_option( 'coschool_general', 'dashboard_page' );

		if( $url ) {
			return get_permalink( $dashboard );
		}

		return $dashboard;
	}
endif;

/**
 * Dashboard endpoint
 * 
 * @param string $endpoint
 * 
 * @return string
 */
if( ! function_exists( 'coschool_dashoard_endpoint' ) ) :
	function coschool_dashoard_endpoint( $endpoint = '' ) {
		$endpoint_url = trailingslashit( coschool_dashboard_page( true ) ) . $endpoint;

		return apply_filters( 'coschool_dashoard_endpoint', $endpoint_url, $endpoint );
	}
endif;

/**
 * Top selling courses
 * 
 * @return array
 */
if( ! function_exists( 'coschool_top_selling' ) ) :
	function coschool_top_selling() {
		
		$top_courses = new \WP_Query( [ 'post_type' => 'course', 'return' ] );
		return $top_courses->posts;
	}
endif;

/**
 * Checks if a user has access to a given content
 * 
 * @param int $content_id ID of the course, lesson, quiz or assignment
 * @param int $user_id The user ID
 * 
 * @uses Codexpert\CoSchool\App\Course\Data
 * @uses Codexpert\CoSchool\App\Lesson\Data
 * @uses Codexpert\CoSchool\App\Quiz\Data
 * @uses Codexpert\CoSchool\App\Assignment\Data
 * 
 * @return bool
 */
if( ! function_exists( 'coschool_has_access' ) ) :
	function coschool_has_access( $content_id, $user_id = null ) {

		if( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$class = 'Codexpert\\CoSchool\\App\\' . ucfirst( get_post_type( $content_id ) ) . '\\Data';

		//assignment plugin active
		if ( get_post_type( $content_id ) == 'assignment' ) {
			if ( ! function_exists( 'coschool_assignment_site_url') ) return;
		}


		$content_data = new $class( $content_id );

		return $content_data->has_access( $user_id );
	}
endif;

/**
 * Current cart items
 */
if( ! function_exists( 'coschool_get_cart_items' ) ) :
	function coschool_get_cart_items() {
		$cart = new Cart;
		return $cart->get_contents();
	}
endif;

/**
 * Gte post id by title
 * 
 * @param string $type post title and post type
 * 
 * @return int
 */
if( ! function_exists( 'coschool_get_post_id_by_title' ) ) :
	function coschool_get_post_id_by_title( $post_title, $post_type = 'post' ) {
		global $wpdb;

		return $post_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s",
				$post_title,
				$post_type
			)
		);
		
	}
endif;
/**
 * Current cart items
 * 
 * @param string $type type of value returned
 * 
 * @return array|(int|float) Array of subtotal, discount and total if $type not supplied, int|float otherwise
 */
if( ! function_exists( 'coschool_get_cart_totals' ) ) :
	function coschool_get_cart_totals( $type = '' ) {

		$cart_totals = [
			'subtotal'  => 0,
			'discount'  => 0,
			'total'     => 0,
		];

		if( ! coschool_get_cart_items() ) {
			return $cart_totals;
		}

		$subtotal = 0;
		foreach ( coschool_get_cart_items() as $item ) {
			if( get_post_type( $item ) == 'course' ) {
				$item_data = new Course_Data( $item );
			}

			elseif( get_post_type( $item ) == 'bundle' ) {
				$item_data = new Bundle_Data( $item );
			}

			$cart_totals['subtotal'] += $item_data->get( 'price' );
		}

		if( false !== ( $_coupon = coschool_get_coupon() ) && null != ( $coupon_id = coschool_get_post_id_by_title( $_coupon, 'coupon' ) ) ) {
			$discount		= 0;
			$coupon_data	= new Coupon_Data( $coupon_id );

			if( 'percent' == $coupon_data->get_type() ) {
				foreach ( coschool_get_cart_items() as $item ) {
					$discount += $coupon_data->discount_amount( $item );
				}
			}
			else {
				$discount = $coupon_data->get_amount();
			}

			$discount = min( $discount, $cart_totals['subtotal'] );

			$cart_totals['discount'] = $discount;
		}

		$cart_totals['total'] = $cart_totals['subtotal'] - $cart_totals['discount'];

		if( array_key_exists( $type, $cart_totals ) ) {
			return $cart_totals[ $type ];
		}

		return $cart_totals;
	}
endif;

/**
 * Get active/applied coupon
 * 
 * @return string
 */
if( ! function_exists( 'coschool_get_coupon' ) ) :
	function coschool_get_coupon() {
		$cart = new Cart;
		return $cart->get_coupons();
	}
endif;

/**
 * Get the icon for the given content type
 * 
 * @return string the URL to the icon
 */
if( ! function_exists( 'coschool_get_icon' ) ) :
	function coschool_get_icon( $type = 'course', $img = false ) {
		
		$icon = '';

		if( in_array( $type , [ 'assignment', 'check', 'lesson', 'quiz', 'assignment', 'certificate', 'lifetime-access' ] ) ) {
			$icon = COSCHOOL_ASSET . "/icon/{$type}.svg";
		}

		if( $img ) {
			return "<img src='{$icon}' alt='{$type}'>";
		}

		return $icon;

	}
endif;

/**
 * Database table prefix
 * 
 * @return string
 */
if( ! function_exists( 'coschool_db_prefix' ) ) :
	function coschool_db_prefix() {
		return 'coschool_';
	}
endif;

/**
 * Automatically log a user in
 */
if( ! function_exists( 'coschool_auto_login' ) ) :
	function coschool_auto_login( $username, $remember = 1 ) {
	    if( is_user_logged_in() ) return;
	    
	    $user 		= get_user_by( 'login', $username );
	    $user_id 	= $user->ID;

	    wp_set_current_user( $user_id, $username );
	    wp_set_auth_cookie( $user_id, ( $remember == 1 ) );
	    do_action( 'wp_login', $username, $user );
	}
endif;

/**
 * Unserializes a data
 * 
 * @param mix $data
 * 
 * @return string
 */
if( ! function_exists( 'coschool_unserialize' ) ) :
	function coschool_unserialize( $data, $implode = false ) {
		if( is_null( $data ) ) {
		    return null;
		}
		
		if( is_array( $data = maybe_unserialize( $data ) ) ) {
			return false !== $implode ? implode( $implode, array_values( $data ) ) : $data;
		}

		if( is_string( $data ) ) {
			return $data;
		}

		return $data;
	}
endif;

/**
 * Serializes a data
 * 
 * @param mix $data
 * 
 * @return array
 */
if( ! function_exists( 'coschool_serialize' ) ) :
	function coschool_serialize( $data ) {
		
		if ( ( ! is_array( $data ) && ! is_object( $data ) ) || is_serialized( $data ) ) return $data;

		return serialize( $data );
	}
endif;

/**
 * Time filter options
 */
if( ! function_exists( 'coschool_time_range' ) ) :
	function coschool_intervals() {
		return $intervals 	= [
			'today'			=> __( 'Today', 'coschool' ),
			'yesterday'		=> __( 'Yesterday', 'coschool' ),
			'this week'		=> __( 'This Week', 'coschool' ),
			'last week'		=> __( 'Last Week', 'coschool' ),
			'7 days ago'	=> __( 'Last 7 Days', 'coschool' ),
			'this month'	=> __( 'This Month', 'coschool' ),
			'last month'	=> __( 'Last Month', 'coschool' ),
			'30 days ago'	=> __( 'Last 30 Days', 'coschool' ),
			'this year'		=> __( 'This Year', 'coschool' ),
			'last year'		=> __( 'Last Year', 'coschool' ),
			'12 months ago'	=> __( 'Last 12 Months', 'coschool' ),
			'custom'		=> __( 'Custom', 'coschool' ),
		];
	}
endif;

/**
 * Generates `from` and `to` time from a given period
 * 
 * @param string $period today|yesterday|thisweek|lastweek|thismonth|lastmonth|last7days|last30days|thisyear|lastyear|last12months
 * 
 * @todo NOT COMPLETE
 * 
 * @return array
 */
if( ! function_exists( 'coschool_time_range' ) ) :
	function coschool_time_range( $period = '' ) {

		$periods = array_keys( coschool_intervals() );
		
		if( ! in_array( $period, $periods ) ) return [];

		$range 		= [
			'from'	=> false,
			'to'	=> false,
			'format'=> '',
		];

		switch ( $period ) {
			case 'today':
				$range['from']		= wp_date( 'd F Y 00:00:00', time() );
				$range['to']		= wp_date( 'd F Y H:i:s', time() );
				$range['format']	= 'h A';
				break;

			case 'yesterday':
				$range['from']		= wp_date( 'd F Y 00:00:00', strtotime( 'yesterday' ) );
				$range['to']		= wp_date( 'd F Y 23:59:59', strtotime( 'yesterday' ) );
				$range['format']	= 'h A';
				break;

			case 'this week':
				$range['from']		= wp_date( 'd F Y 00:00:00', strtotime( 'this week' ) );
				$range['to']		= wp_date( 'd F Y H:i:s', time() );
				$range['format']	= 'd F, D';
				break;

			case 'last week':
				$range['from']		= wp_date( 'd F Y 00:00:00', strtotime( 'last week' ) );
				$range['to']		= wp_date( 'd F Y 23:59:59', strtotime( 'this week' ) - DAY_IN_SECONDS );
				$range['format']	= 'd F, D';
				break;

			case '7 days ago':
				$range['from']		= wp_date( 'd F Y H:i:s', strtotime( '7 days ago' ) );
				$range['to']		= wp_date( 'd F Y H:i:s', time() );
				$range['format']	= 'd F Y';
				break;

			case 'this month':
				$range['from']		= wp_date( '1 F Y 00:00:00', strtotime( 'this month' ) );
				$range['to']		= wp_date( 'd F Y H:i:s', time() );
				$range['format']	= 'd F Y';
				break;

			case 'last month':
				$range['from']		= wp_date( '1 F Y 00:00:00', strtotime( 'last month' ) );
				$range['to']		= wp_date( 't F Y 23:59:59', strtotime( 'last month' ) );
				$range['format']	= 'd F Y';
				break;

			case '30 days ago':
				$range['from']		= wp_date( 'd F Y H:i:s', strtotime( '30 days ago' ) );
				$range['to']		= wp_date( 'd F Y H:i:s', time() );
				$range['format']	= 'd F Y';
				break;

			case 'this year':
				$range['from']		= '1 January ' . wp_date( 'Y 00:00:00' );
				$range['to']		= wp_date( 'd F Y H:i:s', time() );
				$range['format']	= 'F Y';
				break;

			case 'last year':
				$range['from']		= '1 January ' . wp_date( 'Y 00:00:00', strtotime( 'last year' ) );
				$range['to']		= '31 December ' . wp_date( 'Y 23:59:59', strtotime( 'last year' ) );
				$range['format']	= 'F Y';
				break;

			case '12 months ago':
				$range['from']		= wp_date( 'd F Y H:i:s', strtotime( '12 months ago' ) );
				$range['to']		= wp_date( 'd F Y H:i:s', strtotime( 'last month' ) );
				$range['format']	= 'F Y';
				break;
			
			default:
				break;
		}
		
		return $range;
	}
endif;

if( ! function_exists( 'coschool_reports_enrollments' ) ) :
	function coschool_reports_enrollments( $group_by = 'course', $item = null, $from = null, $to = null, $format = 'd F Y' ) {
		$intervals	= [];
		$reports	= [];

		global $wpdb;
		$coschool_prefix = coschool_db_prefix();

		$sql = "SELECT * FROM `{$wpdb->prefix}{$coschool_prefix}enrollments` WHERE `status` != 'pending'";
		if( $group_by == 'course' && ! is_admin() ) {
			$sql .= $wpdb->prepare( " AND `course_id` IN (SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_author` = %d)", get_current_user_id() );
		}

		// if we need to show results for a specific item (e.g. course, category or instructor)
		if( ! is_null( $item ) ) {
			if( $group_by == 'course' ) {
				$sql .= $wpdb->prepare( " AND `course_id` = %d", $item );
			}
			elseif( $group_by == 'category' ) {
				$sql .= $wpdb->prepare( " AND `course_id` IN (SELECT `object_id` FROM `{$wpdb->term_relationships}` WHERE `term_taxonomy_id` = %d)", $item );
				if ( ! is_admin() ) {
					$sql .= $wpdb->prepare( " AND `course_id` IN (SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_author` = %d)", get_current_user_id() );
				}
			}
			elseif( $group_by == 'instructor' ) {
				$sql .= $wpdb->prepare( " AND `course_id` IN (SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_author` = %d)", $item );
			}
		}

		if( ! is_null( $from ) ) {
			$sql .= $wpdb->prepare( " AND `time` > %d", coschool_sanitize( $from ) );
		}

		if( ! is_null( $to ) ) {
			$sql .= $wpdb->prepare( " AND `time` <= %d", coschool_sanitize( $to ) );
		}

		// helper::pri($sql);
		$results = $wpdb->get_results( $sql );
		foreach ( $results as $result ) {
			$_index = wp_date( $format, $result->time );

			if( ! isset( $intervals[ $_index ]['earnings'] ) ) {
				$intervals[ $_index ]['sales']		= 0;
				$intervals[ $_index ]['earnings']	= 0;
			}

			$intervals[ $_index ]['sales']		+= 1;
			$intervals[ $_index ]['earnings']	+= $result->price;
		}

		if( strpos( $format, 'H' ) === 0 || strpos( $format, 'h' ) === 0 ) {
			$increament = HOUR_IN_SECONDS;
		}
		elseif( strpos( $format, 'F' ) === 0 ) {
			// we're using 31 days to avoid a conflict
			// @todo error in `last 12 months`
			$increament = 31 * DAY_IN_SECONDS;
		}
		else {
			$increament = DAY_IN_SECONDS;
		}

		$ranges = [];
		for ( $_range = $from; $_range < $to; $_range += $increament ) {
			$ranges[] = wp_date( $format, $_range );
		}

		// Helper::pri( ( $ranges ) );
		foreach ( array_unique( $ranges ) as $range ) {
			if( array_key_exists( $range, $intervals ) ) {
				$amounts = $intervals[ $range ];
				$reports['intervals'][]	= $range;
				$reports['sales'][]		= $amounts['sales'];
				$reports['earnings'][]	= $amounts['earnings'];
			}
			else {
				$reports['intervals'][]	= $range;
				$reports['sales'][]		= 0;
				$reports['earnings'][]	= 0;
			}
		}

		return $reports;
	}
endif;

if( ! function_exists( 'coschool_reports_top_sales' ) ) :
	function coschool_reports_top_sales( $group_by = 'course', $from = null, $to = null ) {
		
		$items		= [];
		$_reports	= [];

		global $wpdb;
		$coschool_prefix 	= coschool_db_prefix();
		$sql				= "SELECT * FROM `{$wpdb->prefix}{$coschool_prefix}enrollments` WHERE `status` != 'pending'";
		
		if ( ! is_admin() ) {
			$sql .= $wpdb->prepare( " AND `course_id` IN (SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_author` = %d)", get_current_user_id() );
		}

		if( ! is_null( $from ) ) {
			$sql .= $wpdb->prepare( " AND `time` > %d", coschool_sanitize( $from ) );
		}

		if( ! is_null( $to ) ) {
			$sql .= $wpdb->prepare( " AND `time` <= %d", coschool_sanitize( $to ) );
		}

		$results = $wpdb->get_results( $sql );
		$reports = [];

		if( count( $results ) ) {
			foreach ( $results as $result ) {
				
				if( 'category' == $group_by ) {
					if( false !== $terms = get_the_terms( $result->course_id, 'course-category' ) ) {
						$_index = $terms[0]->name;
					}
					else {
						$_index = __( '[Not set]', 'coschool' );
					}
				}
				elseif( 'instructor' == $group_by ) {
					$_index = get_userdata( get_post( $result->course_id )->post_author )->display_name;
				}
				else {
					$_index = get_the_title( $result->course_id );
				}

				if( ! isset( $items[ $_index ]['earnings'] ) ) {
					$items[ $_index ]['sales']		= 0;
					$items[ $_index ]['earnings']	= 0;
				}

				$items[ $_index ]['sales']		+= 1;
				$items[ $_index ]['earnings']	+= $result->price;
			}

			foreach ( $items as $item => $amounts ) {
				$_reports['sales'][ $item ]		= $amounts['sales'];
				$_reports['earnings'][ $item ]	= $amounts['earnings'];
			}
			
			arsort( $_reports['sales'] );
			arsort( $_reports['earnings'] );

			foreach ( $_reports as $type => $values ) {
				$reports[ $type ]['labels'] = array_keys( $values );
				$reports[ $type ]['data'] = array_values( $values );
			}
		}

		return $reports;
	}
endif;

if( ! function_exists( 'front_student_report' ) ) :
	function front_student_report( ) {

		global $wpdb;
		$coschool_prefix = coschool_db_prefix();
		$reports 	= [];
		$_temp 		= [];
		$final 		= [];

		$current_user_enrollment_ids = $wpdb->get_results( $wpdb->prepare( "SELECT `id` FROM `{$wpdb->prefix}{$coschool_prefix}enrollments` WHERE `student` = %d",get_current_user_id() ), ARRAY_A );

		foreach ( $current_user_enrollment_ids as $id ) {
			$_reports = $wpdb->get_results( $wpdb->prepare( "SELECT `completed_at` as time FROM `{$wpdb->prefix}{$coschool_prefix}enrollment_progress` WHERE `enrollment_id` = %d", $id['id'] ), ARRAY_A );

			foreach ( $_reports as $value ) {

				array_push( $_temp, $value['time'] );
			}
		}
		asort( $_temp );

		foreach ( $_temp as $value ) {
			$time  = wp_date( get_option( 'date_format' ), $value );
			array_push( $final, $time  );
		}

		$reports = array_count_values( $final );

		return $reports;
	}
endif;

if( ! function_exists( 'coschool_get_enrollment' ) ) :
	function coschool_get_enrollment( $enrollment_id ) {
		global $wpdb;

		$prefix = $wpdb->prefix . coschool_db_prefix();

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$prefix}enrollments` WHERE `id` = %d", $enrollment_id ) );
	}
endif;

if( ! function_exists( 'coschool_pagination' ) ) :
function coschool_pagination() {
	?>
	<div id="coschool-pagination-wrap">
	    <div class="coschool-pagination">
	        <?php 
	        $args = [
	            'prev_text' => '<i class="fas fa-arrow-left"></i>',
	            'next_text' => '<i class="fas fa-arrow-right"></i>'
	        ];
	        echo paginate_links( $args );
	        ?>
	    </div>
	</div>
	<?php
}
endif;

if( ! function_exists( 'coschool_admin_email' ) ) :
/**
 * Gets admin email
 *
 * @since 0.9
 * 
 * @return string
 */
function coschool_admin_email() {
	return get_option( 'admin_email' );
}
endif;

/**
 * Is Schema markup enabled?
 * 
 * @return bool
 */
if( ! function_exists( 'coschool_schema_enabled' ) ) {
	function coschool_schema_enabled() {
		// return Helper::get_option( 'coschool_certificate' , 'enabled' );
		return true;
	}
}

/**
 * get terms
 * 
 * @return array
 */
if( ! function_exists( 'coschool_get_terms' ) ) {
	function coschool_get_terms( $taxonomy, $slug = false ) {
		$_terms = get_terms( [
			'taxonomy' 		=> $taxonomy,
		    'hide_empty' 	=> false,
		] );

		$term_key = 'term_id';
		if ( $slug ) {
			$term_key = 'slug';
		}

		$terms = [];
		foreach( $_terms as $term ) {
			$terms[ $term->$term_key ] = $term->name;
		}

		return $terms;
	}
}

/**
 * get post terms
 * 
 * @return array
 */
if( ! function_exists( 'coschool_course_terms' ) ) {
	function coschool_course_terms( $course_id, $taxonomy ) {
		if ( empty( $course_id ) ) return [];
		
		$_terms	= get_the_terms( $course_id, $taxonomy );

		$terms 	= [];
		if( is_array( $_terms ) && count( $_terms ) > 0 ) {
			foreach ( $_terms as $term ) {
				$terms[ $term->slug ] = $term->name;
			}
		}

		return $terms;
	}
}

/**
 * get post terms
 * 
 * @return string
 */
if( ! function_exists( 'coschool_selected_terms' ) ) {
	function coschool_selected_terms( $term_slug, $terms ) {
		$selected = '';
		if ( array_key_exists( $term_slug, $terms ) ) {
			$selected = 'selected';
		}

		return $selected;
	}
}

/**
 * check endpoint
 * 
 * @return boolean
 */
if( ! function_exists( 'is_coschool_endpoint_url' ) ) {
	function is_coschool_endpoint_url( $endpoint ) {
		global $wp_query;

		return isset( $wp_query->query_vars['coschool_dtab'] ) && $wp_query->query_vars['coschool_dtab'] == $endpoint;
	}
}

/**
 * check endpoint
 * 
 * @return boolean
 */
if( ! function_exists( 'coschool_paginated' ) ) {
	function coschool_paginated( $url, $page_count, $paginat_number ) {

		$paginated 	= isset( $_GET['paginated'] ) ? coschool_sanitize( $_GET['paginated'] ) : '1';

		if ( !empty( $paginated ) ) {
			$prev_value 	= $paginated - 1;
			$next_value 	= $paginated + 1;

			if ( $paginated == 1 ) {
				$prev_value = $paginated;
			}

			if ( $page_count == 0 ) {
				$next_value = $paginated;
			}
			$prev_paginated =  add_query_arg( 'paginated', $prev_value, $url );
		}
		else {
			$prev_paginated = '';
			$next_value 	= 2;
		}

		$next_paginated =  add_query_arg( 'paginated', $next_value, $url );
		?>

		<div class="coschool-pagination">
			<ul class="page-numbers">
				<?php

				$dots = [ 1, 2, $paginat_number - 1, $paginat_number ];

				for ( $i = 1; $i < $paginat_number + 1; $i++ ) {

					$page =  add_query_arg( 'paginated', $i, $url );

					if ( $i == 1 ) {
						echo '<li><a class="prev page-numbers" href="'. esc_url( $prev_paginated ).'">←</a></li>';
					}

					if ( in_array( $i, $dots ) ) {
						if ( $paginated == $i ) {
							echo '<li><span aria-current="page" class="current page-numbers">'. esc_html( $i ) .'</span></li>';
						}
						else {
							echo '<li><a class="page-numbers" href="'. esc_url( $page ) .'">'. esc_html( $i ) .'</a></li>';
						}
					}
					else {
						echo '..';
					}

					if ( $i == $paginat_number ) {
						echo '<li><a class="next page-numbers" href="'. esc_url( $next_paginated ) .'">→</a></li>';
					}
				}
				?>
			</ul>
		</div>
		<?php
	}
}

/**
 * Time period pairs
 * 
 * @return array|string
 */
if( ! function_exists( 'coschool_periods' ) ) {
	function coschool_periods( $period = '' ) {
		$periods = [
			DAY_IN_SECONDS		=> __( 'Days', 'coschool' ),
			WEEK_IN_SECONDS		=> __( 'Weeks', 'coschool' ),
			MONTH_IN_SECONDS	=> __( 'Months', 'coschool' ),
			YEAR_IN_SECONDS		=> __( 'Years', 'coschool' ),
		];

		if( $period != '' && array_key_exists( $period, $periods ) ) {
			return $periods[ $period ];
		}

		return $periods;
	}
}
/**
 * Attempt status
 * 
 * @todo it should be on quiz class
 */
if( ! function_exists( 'quiz_attempt_status' ) ) {
	function quiz_attempt_status( $attempt_id ) {
		global $wpdb;
		$coschool_prefix 	= coschool_db_prefix();
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT `status` FROM `{$wpdb->prefix}{$coschool_prefix}quiz_attempts` WHERE `id` = %d",$attempt_id ) );

		return $result->status;
	}
}
