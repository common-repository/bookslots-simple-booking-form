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
use Bookslots\Includes\BaseCpt;
use Bookslots\Includes\Stringy;
use Rakit\Validation\Validator;
use Bookslots\Includes\EmployeeTable;
use Bookslots\Includes\RequiredIfWith;
use Bookslots\Filters\AppointmentFilter;
use Bookslots\Includes\TimeSlotGenerator;
use Bookslots\Filters\UnavailabilityFilter;
use Bookslots\Filters\SlotsPassedTodayFilter;

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
class Employee extends BaseCpt {



	/**
	 * Declare properties.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public static $type_str       = '_bookslot_type';
	public static $user_id_str    = '_bookslot_user_id';
	public static $first_name_str = '_bookslot_first_name';
	public static $last_name_str  = '_bookslot_last_name';
	public static $schedule_str   = '_bookslot_schedule';
	public static $cpt            = 'bookslot_employee';

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
				'user_id'    => 0,
				'first_name' => '',
				'last_name'  => '',
				'type'       => 'user',
				'schedule'   => [],
			]
		);
		$this->name = Includes\fullname_by_id( $this->user_id );
	}

	/**
	 * Handle create request
	 *
	 * @return void
	 */
	public function handle_create_request() {
		if ( ! isset( $_POST['security'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'bookslots' )
		) {
			wp_send_json_error( array( 'message' => 'Nonce is invalid.' ) );
		}

		if ( isset( $_POST['do'] ) && 'findUsersByName' === $_POST['do'] ) {
			$this->find_users_by_name( $_POST );
		}

		if ( isset( $_POST['do'] ) && 'getEmployee' === $_POST['do'] ) {
			$search = isset( $_POST['search'] ) ? absint( $_POST['search'] ) : '';
			$this->handle_get_employee( $search );
		}

		$form = isset( $_POST['form'] ) ? sanitize_text_field( wp_unslash( $_POST['form'] ) ) : [];
		if ( $form ) {
			$form = json_decode( $form, true );
		}

		if ( isset( $_POST['do'] ) && 'createEmployee' === $_POST['do'] ) {
			$this->store( $form );
		}

		if ( isset( $_POST['do'] ) && 'updateEmployee' === $_POST['do'] ) {
			$this->store( $form );
		}
	}

	/**
	 * Handle get employee
	 *
	 * @return void
	 */
	public function handle_get_employee( $search ) {
		if ( ! $search ) {
			return;
		}

		$employee = self::find( $search );
		$schedule = $employee->schedule;

		if ( ! $schedule['timezone'] ) {
			$schedule['timezone'] = wp_timezone_string();
			$employee->schedule   = $schedule;
		}

		wp_send_json_success(
			array(
				'employee' => array(
					'id'   => $employee->user_id,
					'name' => Includes\fullname_by_id( $employee->user_id ),
				),
				'schedule' => $employee->schedule,
			)
		);
	}


	/**
	 * Undocumented function
	 *
	 * @return mixed
	 */
	public function store( $form ) {
		$validator = new Validator();
		$validator->addValidator( 'required_if_with', new RequiredIfWith() );
		$validator->addValidator( 'string', new Stringy() );
		$validation = $validator->make(
			(array) $form,
			array(
				'employee_id'                              => 'integer',
				'user_id'                                  => 'required|integer',
				'schedule.timezone'                        => 'string',
				'schedule.availability.datetype'           => 'alpha',
				'schedule.availability.startdate'          => 'required_if_with:schedule.availability.datetype,[singleday/multidays]|date:F d Y',
				'schedule.availability.enddate'            => 'required_if_with:schedule.availability.datetype,multidays|date:F d Y',
				'schedule.availability.starttime'          => 'required_if_with:schedule.availability.datetype,singleday|date:H:i',
				'schedule.availability.endtime'            => 'required_if_with:schedule.availability.datetype,singleday|date:H:i',
				'schedule.availability.days.*.status'      => 'boolean',
				'schedule.availability.days.*.name'        => 'alpha',
				'schedule.availability.days.*.start'       => 'required_if_with:schedule.availability.days.*.status,true,schedule.availability.datetype,[everyday]|date:H:i',
				'schedule.availability.days.*.end'         => 'required_if_with:schedule.availability.days.*.status,true,schedule.availability.datetype,[everyday]|date:H:i',
				'schedule.unavailability.slots.*.datetype' => 'alpha_num',
				'schedule.unavailability.slots.*.startdate' => 'date:F d Y',
				'schedule.unavailability.slots.*.starttime' => 'required_if:schedule.unavailability.slots.*.datetype,singleday,monday,tuesday,wednesday,thursday,friday,saturday,sunday,everyday|date:H:i',
				'schedule.unavailability.slots.*.endtime'  => 'required_if:schedule.unavailability.slots.*.datetype,singleday,monday,tuesday,wednesday,thursday,friday,saturday,sunday,everyday|date:H:i',
			)
		);
		// then validate
		$validation->validate();

		if ( $validation->fails() ) {
			wp_send_json_error( array( 'errorMessages' => $validation->errors()->firstOfAll() ) );
		}

		$data = $validation->getValidatedData();

		$this->ID       = $data['employee_id'];
		$this->schedule = $data['schedule'];
		$this->user_id  = $data['user_id'];

		if ( ! is_null( $this->ID ) && (int) $this->ID > 0 ) {
			$this->store_meta();
		} else {
			$this->ID = self::create( $this );
		}

		$this->store_meta();

		wp_send_json_success( [ 'message' => 'success' ] );
	}

	/**
	 * Create new employee
	 *
	 * @param Employee $employee
	 * @return void
	 */
	public static function create( Employee $employee ) {
		$post_id = wp_insert_post(
			$employee->get_values()
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

		update_post_meta( $id, self::$type_str, $this->type );
		update_post_meta( $id, self::$user_id_str, $this->user_id );
		update_post_meta( $id, self::$schedule_str, $this->schedule );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function find_users_by_name( $post ) {
		$validator  = new Validator();
		$validation = $validator->make(
			(array) $post,
			array(
				'search' => 'required',
			)
		);

		// then validate
		$validation->validate();

		if ( $validation->fails() ) {
			wp_send_json_error( array( 'errorMessages' => $validation->errors()->firstOfAll() ) );
		}

		$data  = $validation->getValidatedData();
		$value = sanitize_text_field( wp_unslash( $data['search'] ) );

		$user_query = new \WP_User_Query(
			array(
				'limit'      => 10,
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'     => 'first_name',
						'value'   => esc_attr( $value ),
						'compare' => 'LIKE',
					),
					array(
						'key'     => 'last_name',
						'value'   => esc_attr( $value ),
						'compare' => 'LIKE',
					),
					array(
						'key'     => 'nickname',
						'value'   => esc_attr( $value ),
						'compare' => 'LIKE',
					),
				),
			)
		);

		$users = array_map(
			function ( $user ) {
				return array(
					'id'       => $user->ID,
					'name'     => $user->first_name . ' ' . $user->last_name,
					'lastname' => $user->last_name,
					'lastname' => $user->last_name,
				);
			},
			$user_query->get_results()
		);

		wp_send_json_success( array( 'users' => $users ) );
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
			return new Employee( $post->ID );
		}
	}


	public function schedule() {
		$schedule = $this->schedule;

		$schedule['availability']   = isset( $schedule['availability'] ) ? $schedule['availability'] : [];
		$schedule['unavailability'] = isset( $schedule['unavailability'] ) ? $schedule['unavailability'] : [];
		$schedule['timezone']       = isset( $schedule['timezone'] ) ? $schedule['timezone'] : wp_timezone_string();

		return $schedule;
	}

	public function timeSlots( object $availability, array $unavailability, Service $service, $selected_date = null ) {
		$appointments = Appointment::find_where( [ Appointment::$date_str => $selected_date->format( 'Y-m-d' ) ] );
		$slots        = ( new TimeSlotGenerator( $availability, $service ) )
			->applyFilters(
				[
					new SlotsPassedTodayFilter(),
					new UnavailabilityFilter( $unavailability ),
					new AppointmentFilter( $appointments ),
				]
			)
			->get();
		return $slots;
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function delete_employee() {
		global $wpdb;

		if ( ! isset( $_GET['action'] ) || $_GET['action'] != 'delete_employee' ) {
			return;
		}

		$employee = isset( $_GET['employee'] ) ? absint( $_GET['employee'] ) : 0;

		// Ensure we can verify our nonce.
		if ( ! isset( $_GET['_wpnonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'delete_employee' )
		) {
			wp_die( esc_html__( 'Could not verify request. Please try again.' ) );
			// Ensure we have an employee ID provided.
		} elseif ( ! isset( $employee ) ) {
			wp_die( esc_html__( 'Could not find provided employee ID. Please try again.' ) );
			// Ensure we're able to delete the actual employee.
		} elseif ( false === $wpdb->delete( $wpdb->prefix . 'posts', array( 'ID' => $employee ) ) ) {
			wp_die( esc_html__( 'Could not delete the provided employee. Please try again.' ) );
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
	 * Find all
	 *
	 * @return void
	 */
	public static function find_all() {
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

		$employees = array();
		foreach ( $ids as $id ) {
			$employees[] = new Employee( $id );
		}

		return $employees;
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
			 'label'     => __( 'Employee', 'textdomain' ),
			 'menu_icon' => 'dashicons-book',
			 'show_ui'   => false,
		 );
		 register_post_type( self::$cpt, $args );

		 register_post_meta( 'bookslot_employee', self::$type_str, array( 'type' => 'string' ) );
		 register_post_meta( 'bookslot_employee', self::$user_id_str, array( 'type' => 'integer' ) );
		 register_post_meta( 'bookslot_employee', self::$schedule_str, array( 'type' => 'array' ) );
	}


	public static function admin_render() {
		 $employee_table = new EmployeeTable();
		$employee_table->prepare_items();
		Includes\view(
			'employee-page',
			array(
				'employee_table' => $employee_table,
			)
		);
	}

	public function user() {
		return new \WP_User( $this->user_id );
	}
}
