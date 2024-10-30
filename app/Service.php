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

use Bookslots\Employee;
use Bookslots\Includes\BaseCpt;
use Rakit\Validation\Validator;
use Bookslots\Includes\ServiceTable;

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
class Service extends BaseCpt {

	/**
	 * Declare properties.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public static $duration_str  = '_bookslot_duration';
	public static $interval_str  = '_bookslot_interval';
	public static $employees_str = '_bookslot_employees';
	public static $cpt           = 'bookslot_service';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $obj = null ) {
		$this->load_cpt(
			$obj,
			self::$cpt,
			array(
				'duration'  => 0,
				'interval'  => 0,
				'employees' => [],
			)
		);
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function register() {    }

	/**
	 * Fetch employees.
	 *
	 * @return Employee related employees.
	 */
	public function employees() {
		$employees = $this->employees;
		return array_map(
			function ( $employee ) {
				return new Employee( $employee );
			},
			$employees
		);
	}

	public static function admin_render() {
		 $service_table = new ServiceTable();
		$service_table->prepare_items();
		Includes\view(
			'service-page',
			array(
				'service_table' => $service_table,
			)
		);
	}

	public static function find_all( $args = [] ) {
		global $wpdb;

		$q = $wpdb->prepare(
			"
        SELECT ID
          FROM {$wpdb->posts}
         WHERE post_type=%s
           AND post_status=%s
      ",
			self::$cpt,
			'publish'
		);

		$ids = $wpdb->get_col( $q ); // WPCS: unprepared SQL OK.

		$services = array();
		foreach ( $ids as $id ) {
			$services[] = new Service( $id );
		}
		// dd($services);
		return $services;
	}

	public function handle_create_request() {
		if ( ! isset( $_POST['security'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'bookslots' )
		) {
			wp_send_json_error( array( 'message' => 'Nonce is invalid.' ) );
		}

		$form = isset( $_POST['form'] ) ? sanitize_text_field( wp_unslash( $_POST['form'] ) ) : [];
		if ( $form ) {
			$form = json_decode( $form, true );
		}

		if ( isset( $_POST['do'] ) && 'createService' === $_POST['do'] ) {
			$this->store( $form );
		}

		if ( isset( $_POST['do'] ) && 'getService' === $_POST['do'] ) {
			$search = isset( $_POST['search'] ) ? absint( $_POST['search'] ) : '';
			$this->handle_get_service( $search );
		}

		if ( isset( $_POST['do'] ) && 'getEmployees' === $_POST['do'] ) {
			$this->handle_get_employees();
		}
	}


	/**
	 * Handle get employee
	 *
	 * @return void
	 */
	public function handle_get_service( $search ) {
		if ( ! $search ) {
			return;
		}

		$service = self::find( $search );

		wp_send_json_success(
			array(
				'service' => $service->get_values(),
			)
		);
	}

	/**
	 * Find sinle employee
	 *
	 * @param integer $id
	 * @return void
	 */
	public static function find( $id ) {
		$post = get_post( $id );

		if ( is_null( $post ) ) {
			return false;
		} else {
			return new Service( $post->ID );
		}
	}


	public function store( $form ) {
		$validator  = new Validator();
		$validation = $validator->make(
			(array) $form,
			array(
				'title'       => 'required',
				'description' => 'required',
				'duration'    => 'required|numeric',
				'interval'    => 'required|numeric',
				'employee_id' => 'numeric',
				'employees'   => 'required|array|min:1',
			)
		);

		// then validate
		$validation->validate();

		if ( $validation->fails() ) {
			wp_send_json_error( array( 'errorMessages' => $validation->errors()->firstOfAll() ) );
		}

		$data = $validation->getValidatedData();

		if ( absint( $data['employee_id'] ) > 0 ) {
			$this->ID = $data['employee_id'];
		}
		$this->post_title   = $data['title'];
		$this->post_content = $data['description'];
		$this->duration     = $data['duration'];
		$this->interval     = $data['interval'];
		$this->employees    = $data['employees'];

		if ( ! is_null( $this->ID ) && (int) $this->ID > 0 ) {
			$this->store_meta();
		} else {
			$this->ID = self::create( $this );
		}

		$this->store_meta();

		wp_send_json_success( array( 'message' => 'success' ) );
	}

	/**
	 * Create new employee
	 *
	 * @param Employee $employee
	 * @return void
	 */
	public static function create( Service $service ) {
		$args    = $service->get_values();
		$post_id = wp_insert_post(
			$args,
			true
		);
		return $post_id;
	}

	/**
	 * Store meta
	 *
	 * @return void
	 */
	public function store_meta() {
		$id = $this->ID;

		update_post_meta( $id, self::$duration_str, $this->duration );
		update_post_meta( $id, self::$interval_str, $this->interval );
		update_post_meta( $id, self::$employees_str, $this->employees );
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function delete_service() {
		global $wpdb;

		if ( ! isset( $_GET['action'] ) || $_GET['action'] != 'delete_service' ) {
			return;
		}

		$service = isset( $_GET['service'] ) ? absint( $_GET['service'] ) : 0;

		// Ensure we can verify our nonce.
		if ( ! isset( $_GET['_wpnonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'delete_service' )
		) {
			wp_die( esc_html__( 'Could not verify request. Please try again.' ) );
			// Ensure we have an service ID provided.
		} elseif ( ! isset( $service ) ) {
			wp_die( esc_html__( 'Could not find provided service ID. Please try again.' ) );
			// Ensure we're able to delete the actual service.
		} elseif ( false === $wpdb->delete( $wpdb->prefix . 'posts', array( 'ID' => $service ) ) ) {
			wp_die( esc_html__( 'Could not delete the provided service. Please try again.' ) );
		}

		// Redirect back to original page with 'success' $_GET
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '404',
					'success' => 1,
				),
				admin_url( 'admin.php' )
			)
		);
		exit();
	}


	/**
	 * Set up custom post type
	 *
	 * @return void
	 */
	public function setup_post_type() {
		 $args = array(
			 'public'    => true,
			 'rewrite'   => false,
			 'supports'  => array( 'title', 'custom-fields' ),
			 'label'     => __( 'Service', 'textdomain' ),
			 'menu_icon' => 'dashicons-book',
			 'show_ui'   => false,
		 );
		 register_post_type( self::$cpt, $args );

		 register_post_meta( 'bookslot_service', self::$duration_str, array( 'type' => 'integer' ) );
		 register_post_meta( 'bookslot_service', self::$interval_str, array( 'type' => 'integer' ) );
		 register_post_meta( 'bookslot_service', self::$employees_str, array( 'type' => 'array' ) );
	}


	/**
	 * Handle get employee
	 *
	 * @return void
	 */
	public function handle_get_employees() {
		$employees = Employee::find_all();
		$data      = array_map(
			function ( $employee ) {
				return array(
					'id'   => $employee->ID,
					'name' => Includes\fullname_by_id( $employee->user_id ),
				);
			},
			$employees
		);

		wp_send_json_success( array( 'employees' => $data ) );
	}
}
