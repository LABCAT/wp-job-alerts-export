<?php
/**
 * WPJAE_Job_Views_Export_CSV_Form
 *
 * @class     WPJAE_Job_Views_Export_CSV_Form
 * @version   1.0.0
 * @package   WP_Job_Reports/Admin
 * @category  Class
 * @author   My Site Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPJAE_Job_Views_Export_CSV_Form', false ) ) {

    /**
     * WPJAE_Job_Views_Export_CSV_Form Class.
     *
     */
    class WPJAE_Job_Views_Export_CSV_Form {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'init', [ $this, 'init' ], 999 );
            add_action( 'admin_menu', [ $this, 'add_settings_page_to_jobs_submenu' ], 999 );
        }

        public function init(){
            if ( isset( $_POST[ 'wpjae_job_alerts_export_csv' ] ) ) {
                $this->download_csv();
            }
        }

        public function download_csv(){
            if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpjae-export-csv' ) ) {
                die( 'Action failed. Please refresh the page and retry.' );
            }

            $domain = explode( ".", parse_url( site_url(), PHP_URL_HOST ) );
            $domain = reset( $domain );
            $filename = 'wp-job-alerts-report-'.  $domain . '.csv';

            header( 'Content-type: text/csv' );
            header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );

            $file = fopen( 'php://output', 'w' );


            $headings = [
                'Email',
                'Signed Up',
                'Keywords',
                'Location',	
                'Categories',	
                'Job Type',	
                'Company Name',	
                'Frequency',	
                'Last Sent',
                'Status'
            ];


            //populate CSV headings
            fputcsv(
                $file,
                $headings
            );

            global $wpdb;
            $alerts = $wpdb->get_results(
                 'SELECT user_id, user_email, meta_key, meta_value
                FROM wp_usermeta 
                INNER JOIN wp_users 
                ON wp_usermeta.user_id = wp_users.ID 
                WHERE meta_key = "jr_alert_status" 
                AND meta_value = "active" 
                ORDER BY user_id ASC'
            );

            $categories = [];
            $category_objects =  get_terms( array(
                'taxonomy' => 'job_cat',
                'hide_empty' => false,
            ) );

            foreach ($category_objects as $category_object) {
                $categories[$category_object->term_id] = $category_object->name;
            }

            $job_types = [];
            $job_type_objects =  get_terms( array(
                'taxonomy' => 'job_type',
                'hide_empty' => false,
            ) );

            foreach ($job_type_objects as $job_type_object) {
                $job_types[$job_type_object->term_id] = $job_type_object->name;
            }

            //populate CSV with a row for each alert
            foreach ( $alerts as $alert ) {
                $keywords = get_user_meta( $alert->user_id, 'jr_alert_meta_keyword', true );
                $cats = [];
                $types = []; 

                $cats_array = get_user_meta( $alert->user_id, 'jr_alert_meta_job_cat', true );
                if($cats_array) {
                    foreach ($cats_array as $cat) {
                        if(isset($categories[$cat])) {
                            $cats[] = $categories[$cat];
                        }
                    }
                }

                $types_array = get_user_meta( $alert->user_id, 'jr_alert_meta_job_type', true );
                if($types_array) {
                    foreach ($types_array as $type) {
                        if(isset($job_types[$type])) {
                            $types[] = $job_types[$type];
                        }
                    }
                }

                $locations_array = get_user_meta( 387, 'jr_alert_meta_location', true );

                if($locations_array) {
                    foreach ($locations_array as $location) {
                        $alert_array = [];
                        $alert_array[] = $alert->user_email;
                        $alert_array[] = '2023-01-14';
                        $alert_array[] = $keywords ? implode('', $keywords) : '';
                        $alert_array[] = ucfirst( trim( $location ) ); // locations
                        $alert_array[] = count($cats) ? implode(', ', $cats) : ''; // cats
                        $alert_array[] = count($types) ? implode(', ', $types) : ''; // types
                        $alert_array[] = ''; // company name
                        $alert_array[] = 'Daily';
                        $alert_array[] = '2023-01-14';
                        $alert_array[] = 'Active';
                        fputcsv(
                            $file,
                            $alert_array
                        ); 
                    }
                } 
                else {
                    $alert_array = [];
                    $alert_array[] = $alert->user_email;
                    $alert_array[] = '2023-01-14';
                    $alert_array[] = $keywords ? implode('', $keywords) : '';
                    $alert_array[] = ''; // locations
                    $alert_array[] = count($cats) ? implode(', ', $cats) : ''; // cats
                    $alert_array[] = count($types) ? implode(', ', $types) : ''; // types
                    $alert_array[] = ''; // company name
                    $alert_array[] = 'Daily';
                    $alert_array[] = '2023-01-14';
                    $alert_array[] = 'Active';
                    fputcsv(
                        $file,
                        $alert_array
                    );    
                }               
            }

            exit();
        }

        public function add_settings_page_to_jobs_submenu(){
            add_submenu_page(
                'edit.php?post_type=job_listing',
                'Export Job Alerts CSV',
                'Export Job Alerts CSV',
                'manage_options',
                'job_alerts_export_csv',
                [ $this, 'output' ]
            );
        }

        public function output(){
            include_once( JOB_ALERTS_EXPORT_PLUGIN_DIR . '/views/html-job-alerts-export-csv-form.php' );
        }

    }

}

$export_form = new WPJAE_Job_Views_Export_CSV_Form();
