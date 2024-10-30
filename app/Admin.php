<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/davidtowoju
 * @since      0.1.0
 *
 * @package    Bookslots
 * @subpackage Bookslots/admin
 */

namespace Bookslots;

use Bookslots\Appointment;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bookslots
 * @subpackage Bookslots/admin
 * @author     David Towoju <hello@figarts.co>
 */
class Admin {

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Registers menu
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'BookSlots', 'textdomain' ),
			__( 'BookSlots', 'textdomain' ),
			'manage_options',
			'bookslots',
			[ Appointment::class, 'admin_render' ],
			'dashicons-calendar'
		);

		add_submenu_page(
			'bookslots',
			__( 'Services', 'textdomain' ),
			__( 'Services', 'textdomain' ),
			'manage_options',
			'bookslots-services',
			[ Service::class, 'admin_render' ],
		);

		add_submenu_page(
			'bookslots',
			__( 'Employees', 'textdomain' ),
			__( 'Employees', 'textdomain' ),
			'manage_options',
			'bookslots-employees',
			[ Employee::class, 'admin_render' ],
		);

		add_submenu_page(
			'bookslots',
			__( 'Settings', 'textdomain' ),
			__( 'Settings', 'textdomain' ),
			'manage_options',
			'bookslots-settings',
			[ Settings::class, 'admin_render' ],
		);

	}

	/**
	 * Load scripts
	 *
	 * @param [type] $hook
	 * @return void
	 */
	public function load_scripts( $hook ) {
		if ( stripos( $hook, 'bookslots' ) !== false ) {
			$this->enqueue_styles();
			$this->enqueue_scripts();
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$admin_css = BOOKSLOTS_DIR_URL . 'resources/css/admin.css';
		$jqueryui_css = BOOKSLOTS_DIR_URL . 'resources/css/jquery-ui.css';
		wp_enqueue_style( $this->plugin_name, $admin_css, [], $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui', $jqueryui_css, false, '1.9.0', false );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$alpine_src = BOOKSLOTS_DIR_URL . 'assets/alpine.min.js';

		wp_enqueue_script(
			$this->plugin_name,
			BOOKSLOTS_DIR_URL . 'resources/js/admin.js',
			array( 'wp-i18n', 'jquery-ui-datepicker' ),
			'1.0.1',
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'bookslotsJS',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => 'bookslots',
				'nonce'   => wp_create_nonce( 'bookslots' ),
				// timezones
			)
		);
	}

	public function admin_header() {
		global $current_screen;
		if ( isset( $current_screen->base ) && stripos( $current_screen->base, 'bookslots' ) !== false ) {
			remove_all_actions( 'admin_notices' );
		}
	}

}
