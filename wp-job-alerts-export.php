<?php
/**
 * Plugin Name: WP Job Alerts Export
 * Plugin URI: https://mysite.digital
 * Description: Get Job Alerts Export from your WordPress Website! This is initially intended to work with the JobRoller theme.
 * Version: 1.0.0
 * Author: My Site Digital
 * Author URI: https://mysite.digital
 * Requires at least: 5.0.0
 * Tested up to: 5.3
 * Text Domain: wp-job-reports
 * Domain Path: /languages/
 * License: GPL2+
 *
 * @package wp-job-reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles core plugin hooks and action setup.
 *
 * @package wp-job-reports
 * @since 1.0.0
 */
class WP_Job_Alerts_Export {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26.0
	 */
	private static $_instance = null;

	/**
	 * REST API instance.
	 *
	 * @var WP_Job_Alerts_Export_REST_API
	 */
	private $rest_api = null;

	/**
	 * Main WP Job Alerts_Export Instance.
	 *
	 * Ensures only one instance of WP Job Manager is loaded or can be loaded.
	 *
	 * @since  1.26.0
	 * @static
	 * @see WPJM()
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->define_constants();
		$this->includes();



	}

	private function define_constants() {
		// Define constants.
		define( 'JOB_ALERTS_EXPORT_VERSION', '1.0.0' );
		define( 'JOB_ALERTS_EXPORT_MINIMUM_WP_VERSION', '5.0.0' );
		define( 'JOB_ALERTS_EXPORT_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'JOB_ALERTS_EXPORT_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'JOB_ALERTS_EXPORT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	}

	public function includes()
	{
		if ( is_admin() ) {
			include_once( JOB_ALERTS_EXPORT_PLUGIN_DIR . '/includes/class-wpjae-job-alerts-export-csv-form.php' );
		}
	}

}


/**
 * Main instance of WP Job Alerts_Export.
 *
 * Returns the main instance of WP Job Manager to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WP_Job_Alerts_Export
 */
function WPJAE() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
	return WP_Job_Alerts_Export::instance();
}

$GLOBALS['job_alerts_export'] = WPJAE();
