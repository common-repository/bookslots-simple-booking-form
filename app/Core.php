<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://pluginette.com
 * @since      1.0.0
 *
 * @package    Bookslot
 * @subpackage Bookslot/Includes
 */

namespace Bookslots;

use Bookslots\Shortcode;
use Bookslots\Appointment;
use Bookslots\Includes\i18n;
use Bookslots\Includes\Loader;


/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Bookslots
 * @subpackage Bookslots/Includes
 * @author     David Towoju <hello@pluginette.com>
 */
class Core {

	protected $loader;
	protected $plugin_name;
	protected $version;
	protected $license_manager;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = 'bookslots';
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function register() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_hooks();
		$this->loader->run();
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-wc-checkoutplus-activator.php
	 */
	public function activate_bookslots() {
		$options = Includes\default_options();
		add_option( 'bookslots', $options );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-wc-checkoutplus-deactivator.php
	 */
	public function deactivate_bookslots() {
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin & public area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_hooks() {
		$plugin_admin = new Admin( $this->plugin_name, $this->version );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_menu', 100 );
		$this->loader->add_action( 'in_admin_header', $plugin_admin, 'admin_header', 100 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'load_scripts', 10 );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_alpine', 10 );

		$plugin_service = new Service();
		$this->loader->add_action( 'init', $plugin_service, 'setup_post_type', 10 );
		$this->loader->add_action( 'wp_ajax_new_service', $plugin_service, 'handle_create_request', 10 );
		$this->loader->add_action( 'admin_init', $plugin_service, 'delete_service', 10 );

		$plugin_employee = new Employee();
		$this->loader->add_action( 'init', $plugin_employee, 'setup_post_type', 10 );
		$this->loader->add_action( 'wp_ajax_new_employee', $plugin_employee, 'handle_create_request', 10 );
		$this->loader->add_action( 'admin_init', $plugin_employee, 'delete_employee', 10 );

		// $plugin_schedule = new Schedule();
		// $this->loader->add_action( 'init', $plugin_schedule, 'setup_post_type', 10 );

		$plugin_appointment = new Appointment();
		$this->loader->add_action( 'init', $plugin_appointment, 'setup_post_type', 10 );
		$this->loader->add_action( 'wp_ajax_new_appointment', $plugin_appointment, 'handle_create_request', 10 );
		$this->loader->add_action( 'admin_init', $plugin_appointment, 'delete_appointment', 10 );

		$plugin_form = new Form();
		$this->loader->add_action( 'wp_loaded', $plugin_form, 'register_scripts', 10 );
		// $this->loader->add_action( 'wp_footer', $plugin_form, 'register_inline_scripts', 10 );
		$this->loader->add_shortcode( 'bookslots-form', $plugin_form, 'show_booking_form', 10 );
		$this->loader->add_action( 'wp_ajax_ajax_create_booking_init', $plugin_form, 'handle_new_booking', 10 );
		$this->loader->add_action( 'wp_ajax_nopriv_ajax_create_booking_init', $plugin_form, 'handle_new_booking', 10 );

		$plugin_settings = new Settings( $this->plugin_name, $this->version );
		$this->loader->add_action( 'admin_post_bookslots_update_options', $plugin_settings, 'save_options', 100 );
	}

}
