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
use Rakit\Validation\Validator;
// use Bookslots\Includes\view as view;

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
class Settings {

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
	public static function admin_render() {
		// $table = new AppointmentTable();
		// $table->prepare_items();
		$options = get_option( 'bookslots', Includes\default_options() );

		Includes\view(
			'settings-page',
			array(
				'options' => $options,
			)
		);
	}

	public function save_options() {
		if ( ! isset( $_POST['___bookslots_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['___bookslots_nonce'] ), 'bookslots_update_options' )
		) {
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		$url        = isset($_POST['_wp_http_referer']) ? esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) ) : '';
		$validator  = new Validator();
		$validation = $validator->make(
			(array) $_POST,
			array(
				'general'           => 'array',
				'general.theme'     => 'alpha_num',
				// 'general.color'    => 'alpha_num',
				'general.position'  => 'alpha_num',
				'labels'            => 'array',
				'labels.form_title' => 'required|alpha_spaces',
				'labels.employees'  => 'required|alpha_num',
				'labels.services'   => 'required|alpha_num',
				'labels.employee'   => 'required|alpha_num',
				'labels.service'    => 'required|alpha_num',
			)
		);

		$validation->validate();
		if ( $validation->fails() ) {
			// dd($validation->errors()->firstOfAll());
			$url = add_query_arg( 'status', 'error', $url );
			wp_safe_redirect( $url, 302 );
			exit();
		}

		$data = $validation->getValidatedData();
		// dd($data);
		update_option( 'bookslots', $data );
		$url = add_query_arg( 'status', 'success', $url );
		wp_safe_redirect( $url, 302 );
		exit();
	}
}
