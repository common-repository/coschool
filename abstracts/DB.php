<?php
/**
 * An abstraction for the DB
 */
namespace Codexpert\CoSchool\Abstracts;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Payment_Method
 * @author Codexpert <hi@codexpert.io>
 */
class DB {

    /**
     * @var obj $wpdb
     */
    public $db;

    /**
     * Database table prefix
     * 
     * @var string
     */
    public $prefix;

    /**
     * Constructor function
     * 
     * @uses $wpdb
     */
    public function __construct() {
        global $wpdb;

        $this->db = $wpdb;

        $this->prefix = coschool_db_prefix();
    }

    
    /**
     * Install database tables
     * 
     * @since 0.9
     */
    public function create_tables() {

        $charset_collate = $this->db->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // `wp_coschool_enrollments` table
        $enrollments_sql = "CREATE TABLE `{$this->db->prefix}{$this->prefix}enrollments` (
            id mediumint(10) NOT NULL AUTO_INCREMENT,
            course_id mediumint(10) NOT NULL,
            student mediumint(10) NOT NULL,
            price float(8,2) NOT NULL,
            payment_id mediumint(10) NOT NULL,
            status varchar(32) NOT NULL,
            time int(10) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta( $enrollments_sql );

        // `wp_coschool_enrollmentmeta` table
        $enrollmentmeta_sql = "CREATE TABLE `{$this->db->prefix}{$this->prefix}enrollmentmeta` (
            meta_id mediumint(10) NOT NULL AUTO_INCREMENT,
            enrollment_id mediumint(10) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext NOT NULL,
            PRIMARY KEY (meta_id)
        ) $charset_collate;";

        dbDelta( $enrollmentmeta_sql );

        // `wp_coschool_enrollment_progress` table
        $enrollment_progress_sql = "CREATE TABLE `{$this->db->prefix}{$this->prefix}enrollment_progress` (
            id mediumint(10) NOT NULL AUTO_INCREMENT,
            enrollment_id mediumint(10) NOT NULL,
            content_id mediumint(10) NOT NULL,
            completed_at int(10) NOT NULL,
            reference longtext NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta( $enrollment_progress_sql );

        // `wp_coschool_payments` table
        $payments_sql = "CREATE TABLE `{$this->db->prefix}{$this->prefix}payments` (
            id mediumint(10) NOT NULL AUTO_INCREMENT,
            amount float(8,2) NOT NULL,
            student mediumint(10) NOT NULL,
            method varchar(64) NOT NULL,
            transaction_id varchar(64) NOT NULL,
            reference varchar(64) NOT NULL,
            time int(10) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta( $payments_sql );

        // `wp_coschool_paymentmeta` table
        $paymentmeta_sql = "CREATE TABLE `{$this->db->prefix}{$this->prefix}paymentmeta` (
            meta_id mediumint(10) NOT NULL AUTO_INCREMENT,
            payment_id mediumint(10) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext NOT NULL,
            PRIMARY KEY (meta_id)
        ) $charset_collate;";

        dbDelta( $paymentmeta_sql );

        // `wp_coschool_quiz_attempts` table
        $quiz_attempts_sql = "CREATE TABLE `{$this->db->prefix}{$this->prefix}quiz_attempts` (
            id mediumint(10) NOT NULL AUTO_INCREMENT,
            quiz_id mediumint(10) NOT NULL,
            student mediumint(10) NOT NULL,
            time_taken int(6) NOT NULL,
            status varchar(32) NOT NULL,
            time int(10) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta( $quiz_attempts_sql );

        // `wp_coschool_quiz_attempt_answers` table
        $quiz_attempt_answers_sql = "CREATE TABLE `{$this->db->prefix}{$this->prefix}quiz_attempt_answers` (
            id mediumint(10) NOT NULL AUTO_INCREMENT,
            attempt_id mediumint(10) NOT NULL,
            question text NOT NULL,
            answer text NOT NULL,
            points float(8,2) NOT NULL,
            feedback longtext NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta( $quiz_attempt_answers_sql );
    }

    /**
     * Inserts a payment entry into the database
     * 
     * @since 0.9
     * 
     * @return int insert_id The id of the newly inserted row
     */
    public function insert( $table, $data ) {
        $this->db->insert( "{$this->db->prefix}{$this->prefix}{$table}", $data );

        return $this->db->insert_id;
    }

    public function update( $table, $data, $where ) {
        $this->db->update( "{$this->db->prefix}{$this->prefix}{$table}", $data, $where );
    }

    /**
     * Insert an enrollment
     * 
     * @since 0.9
     */
    public function insert_enrollment( $course_id, $student, $price, $payment_id = 0, $status = null ) {

        // @todo validate
        if( is_null( $status ) ) {
            $status = $payment_id == 0 ? 'pending' : 'active';
        }

        return $this->insert( 'enrollments', [
            'course_id'     => $course_id,
            'student'       => $student,
            'price'         => $price,
            'payment_id'    => $payment_id,
            'status'        => $status,
            'time'          => time(),
        ] );
    }

    /**
     * Adds an enrollment meta
     * 
     * @since 0.9
     */
    public function add_enrollment_meta( $enrollment_id, $meta_key, $meta_value = '' ) {
        return $this->insert( 'enrollmentmeta', [
            'enrollment_id'		=> $enrollment_id,
            'meta_key'			=> $meta_key,
            'meta_value'		=> $meta_value,
        ] );
    }

    /**
     * Adds an enrollment progress data
     * 
     * @since 0.9
     */
    public function add_enrollment_progress( $enrollment_id, $content_id, $reference = '' ) {
        return $this->insert( 'enrollment_progress', [
            'enrollment_id' => $enrollment_id,
            'content_id'    => $content_id,
            'completed_at'  => time(),
            'reference'     => $reference,
        ] );
    }

    /**
     * Insert payment
     * 
     * @since 0.9
     */
    public function insert_payment( $amount, $payer, $method, $txn_id, $reference = '' ) {
        return $this->insert( 'payments', [
            'amount'            => $amount,
            'student'           => $payer,
            'method'            => $method,
            'transaction_id'    => $txn_id,
            'reference'         => $reference,
            'time'              => time(),
        ] );
    }

    /**
     * Adds a payment meta
     * 
     * @since 0.9
     */
    public function add_payment_meta( $payment_id, $meta_key, $meta_value = '' ) {
        return $this->insert( 'paymentmeta', [
            'payment_id'        => $payment_id,
            'meta_key'          => $meta_key,
            'meta_value'        => $meta_value,
        ] );
    }

    /**
     * Insert quiz attempt
     * 
     * @since 0.9
     */
    public function insert_quiz_attempt( $quiz_id, $student, $status, $time_taken = 0 ) {
        return $this->insert( 'quiz_attempts', [
            'quiz_id'       => $quiz_id,
            'student'       => $student,
            'time_taken'    => $time_taken,
            'status'        => $status,
            'time'          => time(),
            
        ] );
    }

    /**
     * Insert quiz attempt answer
     * 
     * @since 0.9
     */
    public function add_quiz_attempt_answer( $attempt_id, $question, $answer, $points = 0, $feedback = '' ) {
        return $this->insert( 'quiz_attempt_answers', [
            'attempt_id'    => $attempt_id,
            'question'      => $question,
            'answer'        => $answer,
            'points'        => $points,
            'feedback'      => $feedback,
        ] );
    }

    /**
     * General MySQL `SELECT` query
     */
    public function select( $table, $select = '*', $where = '' ) {
        $sql = "SELECT {$select} FROM `{$this->db->prefix}{$this->prefix}{$table}`";

        if( $where != '' ) {
            $sql .= " WHERE {$where}";
        }

        return $this->db->get_results( $sql );
    }

    /**
     * Is an item found in the given table?
     */
    public function is_found( $table, $key, $value ) {
        return $this->db->query( "SELECT * FROM `{$this->db->prefix}{$this->prefix}{$table}` WHERE `{$key}` = '{$value}'" ) > 0;
    }
}