<?php
/**
 * The Service Class file.
 *
 * @link       https://github.com/davidtowoju
 * @since      0.1.0
 *
 * @package    Bookslots
 * @subpackage Bookslots/admin
 */
namespace Bookslots;

use Bookslots\Service;
use Bookslots\Employee;
use Bookslots\Includes\BaseCpt;
use Rakit\Validation\Validator;
use Bookslots\Includes\AppointmentTable;

/**
 * The Service Class.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bookslots
 * @subpackage Bookslots/admin
 * @author     David Towoju <hello@figarts.co>
 */
class Appointment extends BaseCpt {

	/**
	 * Declare properties.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public static $token_str       = '_bookslot_token';
	public static $employee_id_str = '_bookslot_employee_id';
	public static $service_id_str  = '_bookslot_service_id';
	public static $date_str        = '_bookslot_date';
	public static $start_time_str  = '_bookslot_start_time';
	public static $end_time_str    = '_bookslot_end_time';
	public static $cpt             = 'bookslot_appointment';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $obj = null ) {
		$this->load_cpt(
			$obj,
			self::$cpt,
			[
				'token'       => null,
				'employee_id' => 0,
				'service_id'  => 0,
				'date'        => null,
				'start_time'  => null,
				'end_time'    => null,
			]
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

	public static function find_all( $args = array() ) {
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

		$appointments = array();
		foreach ( $ids as $id ) {
			$appointments[] = new Appointment( $id );
		}
		return $appointments;
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

		if ( isset( $_POST['do'] ) && 'createBooking' === $_POST['do'] ) {
			$this->store( $form );
		}

		if ( isset( $_POST['do'] ) && 'updateBooking' === $_POST['do'] ) {
			$this->store( $form );
		}

		if ( isset( $_POST['do'] ) && 'getServices' === $_POST['do'] ) {
			$this->handle_get_services();
		}

		if ( isset( $_POST['do'] ) && 'getEmployees' === $_POST['do'] ) {
			$service_id = isset( $_POST['service_id'] ) ? absint( $_POST['service_id'] ) : 0;
			$this->handle_get_employees( $service_id );
		}

		if ( isset( $_POST['do'] ) && 'getAppointment' === $_POST['do'] ) {
			$search = isset( $_POST['search'] ) ? absint( $_POST['search'] ) : '';
			$this->handle_get_appointment( $search );
		}
	}


	/**
	 * Handle get employee
	 *
	 * @return void
	 */
	public function handle_get_services() {
		$services = Service::find_all( $args );
		// dd($services);
		$services = Includes\transform( $services, array( 'ID', 'post_title', 'duration' ) );
		wp_send_json_success( array( 'services' => $services ) );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function handle_get_employees( $service_id ) {
		$service   = Service::find( $service_id );
		$employees = $service->employees();
		$employees = Includes\transform( $employees, array( 'ID', 'name' ) );

		wp_send_json_success( array( 'employees' => $employees ) );
	}


	/**
	 * Handle get employee
	 *
	 * @return void
	 */
	public function handle_get_appointment( $search ) {
		if ( ! $search ) {
			return;
		}

		$appointment = self::find( $search );
		$services    = Service::find_all();
		$services    = Includes\transform( $services, array( 'ID', 'post_title', 'duration' ) );
		$service     = Service::find( $appointment->service_id );
		$employees   = $service->employees();
		$employees   = Includes\transform( $employees, array( 'ID', 'name' ) );

		wp_send_json_success(
			array(
				'appointment' => $appointment->get_values(),
				'services'    => $services,
				'employees'   => $employees,
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
			return new Appointment( $post->ID );
		}
	}
	/**
	 * Find sinle employee
	 *
	 * @param integer $id
	 * @return void
	 */
	public static function find_where( $meta_args ) {
		$args                           = array(
			'post_type' => self::$cpt,
		);
		$args['meta_query']['relation'] = 'AND';
		foreach ( $meta_args as $key => $value ) {
			$args['meta_query'][] = array(
				'key'   => $key,
				'value' => $value,
			);
		}

		$query = new \WP_Query( $args );
		if ( is_null( $query->get_posts() ) ) {
			return false;
		} else {
			$appointments = [];
			foreach ( $query->get_posts() as $post ) {
				$appointments[] = new Appointment( $post->ID );
			}
			return $appointments;
		}
	}

	/**
	 * Create new employee
	 *
	 * @param Employee $employee
	 * @return void
	 */
	public static function create( Appointment $appointment ) {
		$post_id = wp_insert_post(
			$appointment->get_values()
		);

		return $post_id;
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function store( $form ) {
		$validator  = new Validator();
		$validation = $validator->make(
			(array) $form,
			array(
				'booking_id' => 'numeric',
				'post_title' => 'required',
				'token'      => 'required_without:booking_id',
				'service'    => 'required|numeric',
				'employee'   => 'required|numeric',
				'start_time' => 'required|date:H:i',
				'end_time'   => 'required|date:H:i',
				'date'       => 'required|date:F d Y',
			)
		);

		// then validate
		$validation->validate();

		if ( $validation->fails() ) {
			wp_send_json_error( array( 'errorMessages' => $validation->errors()->firstOfAll() ) );
		}

		$data              = $validation->getValidatedData();
		$this->ID          = absint( $data['booking_id'] );
		$this->post_title  = sanitize_text_field( $data['post_title'] );
		$this->token       = sanitize_text_field( $data['token'] );
		$this->service_id  = $data['service'];
		$this->employee_id = $data['employee'];
		$this->start_time  = $data['start_time'];
		$this->end_time    = $data['end_time'];
		$this->date        = date_i18n( 'Y-m-d', strtotime( $data['date'] ) );

		if ( ! is_null( $this->ID ) && (int) $this->ID > 0 ) {
			$this->store_meta();
		} else {
			$this->ID = self::create( $this );
		}

		$this->store_meta();

		wp_send_json_success( array( 'message' => 'success' ) );
	}


	/**
	 * Store meta
	 *
	 * @return void
	 */
	public function store_meta() {
		$id = $this->ID;

		update_post_meta( $id, self::$token_str, $this->token );
		update_post_meta( $id, self::$employee_id_str, $this->employee_id );
		update_post_meta( $id, self::$service_id_str, $this->service_id );
		update_post_meta( $id, self::$date_str, $this->date );
		update_post_meta( $id, self::$start_time_str, $this->start_time );
		update_post_meta( $id, self::$end_time_str, $this->end_time );
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function delete_appointment() {
		global $wpdb;

		if ( ! isset( $_GET['action'] ) || $_GET['action'] != 'delete_appointment' ) {
			return;
		}

		$appointment = isset( $_GET['appointment'] ) ? absint( $_GET['appointment'] ) : 0;

		// Ensure we can verify our nonce.
		if ( ! isset( $_GET['_wpnonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'delete_appointment' )
		) {
			wp_die( esc_html__( 'Could not verify request. Please try again.' ) );
			// Ensure we have an appointment ID provided.
		} elseif ( ! isset( $appointment ) ) {
			wp_die( esc_html__( 'Could not find provided appointment ID. Please try again.' ) );
			// Ensure we're able to delete the actual appointment.
		} elseif ( false === $wpdb->delete( $wpdb->prefix . 'posts', array( 'ID' => $appointment ) ) ) {
			wp_die( esc_html__( 'Could not delete the provided appointment. Please try again.' ) );
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
	 * Sends a JSON response with the details of the given error.
	 *
	 * @param WP_Error $error
	 */
	public function send_response( $args = array() ) {
		$response = array(
			'monthYear'    => $this->calendar->get_start_date()->format( 'M Y' ),
			'weekInterval' => $this->calendar->get_week_interval(),
		);

		$response = array_merge( $response, $args );
		// dd($response);
		wp_send_json_success( $response );
	}

	/**
	 * Set up custom post type
	 *
	 * @return void
	 */
	public function setup_post_type() {
		$args = array(
			'public'   => true,
			'rewrite'  => false,
			'supports' => array( 'title', 'custom-fields' ),
			'label'    => __( 'Appointment', 'textdomain' ),
			'show_ui'  => false,
		);
		register_post_type( self::$cpt, $args );
	}

	/**
	 * Registers menu
	 *
	 * @return void
	 */
	public static function admin_render() {
		$table = new AppointmentTable();
		$table->prepare_items();
		Includes\view(
			'appointment-page',
			array(
				'table' => $table,
			)
		);
	}

}
