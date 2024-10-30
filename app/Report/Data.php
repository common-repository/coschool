<?php
/**
 * Data for the reports
 */
namespace Codexpert\CoSchool\App\Report;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Report
 * @author Codexpert <hi@codexpert.io>
 */
class Data {

	public function filter( $data ) {

        if( ! isset( $_GET['page'] ) || $_GET['page'] != 'reports' ) return $data;

        $group_by = 'course';
        if( isset( $_GET['group-by'] ) && in_array( $_GET['group-by'], [ 'category', 'instructor' ] ) ) {
            $group_by = coschool_sanitize( $_GET['group-by'] );
        }

        $date_range = [
            'from'      => wp_date( 'd F Y 00:00:00', strtotime( 'first day of this month' ) ),
            'to'        => wp_date( 'd F Y H:i:s', time() ),
            'format'    => 'd F Y',
        ];

        if( isset( $_GET['date-range'] ) && array_key_exists( $_GET['date-range'], coschool_intervals() ) ) {
            if( 'custom' != $_GET['date-range'] ) {
                $date_range = coschool_time_range( coschool_sanitize( $_GET['date-range'] ) );
            }
            else {
                $date_range = [
                    'from'      => isset( $_GET['from'] ) ? wp_date( 'd F Y 00:00:00', strtotime( coschool_sanitize( $_GET['from'] ) ) ) : null,
                    'to'        => isset( $_GET['to'] ) ? wp_date( 'd F Y 23:59:59', strtotime( coschool_sanitize( $_GET['to'] ) ) ) : null,
                    'format'    => 'd F Y',
                ];
            }
        }
        
        // helper::pri($group_by);
        $item = isset( $_GET['item'] ) && '' != $_GET['item'] ? coschool_sanitize( $_GET['item'] ) : null;
        $data['reports'] = [
            'bar'   => coschool_reports_enrollments( $group_by, $item, strtotime( $date_range['from'] ), strtotime( $date_range['to'] ), $date_range['format'] ),
            'pie'   => coschool_reports_top_sales( $group_by, strtotime( $date_range['from'] ), strtotime( $date_range['to'] ) ),
        ];

        return $data;
    }

    public function front_admin_report( $data ) {

        // if( ! isset( $_GET['page'] ) || $_GET['page'] != 'reports' ) return $data;

        $group_by = 'course';
        if( isset( $_GET['group-by'] ) && in_array( $_GET['group-by'], [ 'category' ] ) ) {
            $group_by = coschool_sanitize( $_GET['group-by'] );
        }

        $date_range = [
            'from'      => wp_date( 'd F Y 00:00:00', strtotime( 'first day of this month' ) ),
            'to'        => wp_date( 'd F Y H:i:s', time() ),
            'format'    => 'd F Y',
        ];

        if( isset( $_GET['date-range'] ) && array_key_exists( $_GET['date-range'], coschool_intervals() ) ) {
            if( 'custom' != $_GET['date-range'] ) {
                $date_range = coschool_time_range( coschool_sanitize( $_GET['date-range'] ) );
            }
            else {
                $date_range = [
                    'from'      => isset( $_GET['from'] ) ? wp_date( 'd F Y 00:00:00', strtotime( coschool_sanitize( $_GET['from'] ) ) ) : null,
                    'to'        => isset( $_GET['to'] ) ? wp_date( 'd F Y 23:59:59', strtotime( coschool_sanitize( $_GET['to'] ) ) ) : null,
                    'format'    => 'd F Y',
                ];
            }
        }
        // helper::pri($group_by);
        $item = isset( $_GET['item'] ) && '' != $_GET['item'] ? coschool_sanitize( $_GET['item'] ) : null;
        $data['reports'] = [
            'enrollments'   => coschool_reports_enrollments( $group_by, $item, strtotime( $date_range['from'] ), strtotime( $date_range['to'] ), $date_range['format'] ),
            'top_sales'   => coschool_reports_top_sales( $group_by, strtotime( $date_range['from'] ), strtotime( $date_range['to'] ) ),
        ];

        return $data;
    }

    public function front_student_report( $data ) {
 
        $data['progress'] = [
            'student_progress'   => front_student_report(),
        ];

        return $data;
    }
	
}