<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://pluginette.com
 * @since      1.0.0
 *
 * @package    Bookslot
 * @subpackage Bookslot/Includes
 */

namespace Bookslots;

use Bookslots\Service;
use Bookslots\Includes\BookingCalendar;

use Rakit\Validation\Validator;
use Bookslots\Appointment;
use Carbon\Carbon;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bookslot
 * @subpackage Bookslot/Includes
 * @author     David Towoju <hello@pluginette.com>
 */
class Form {



	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Action hook used by the AJAX class.
	 *
	 * @var string
	 */
	const ACTION = 'new_booking';

	/**
	 * Action argument used by the nonce validating the AJAX request.
	 *
	 * @var string
	 */
	const NONCE = 'new_booking';

	public $calendar;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Register our AJAX JavaScript.
	 */
	public function register_scripts() {
		// dd(BOOKSLOTS_ASSETS . '/resources/js/app.js');
		wp_register_style( 'bookslots-style', BOOKSLOTS_RESOURCES . 'css/app.css', array(), '1.0.0', 'all' );
		wp_register_script( 'bookslots-script', BOOKSLOTS_RESOURCES . 'js/app.js', array(), '1.0.0', true );
		wp_add_inline_script( 'bookslots-script', 'var bookslotsJS = {}; bookslotsJS.newBooking = ' . $this->get_ajax_data(), 'before' );
	}

	/**
	 * Get the AJAX data that WordPress needs to output.
	 *
	 * @return array
	 */
	private function get_ajax_data() {
		return wp_json_encode(
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => self::ACTION,
				'nonce'   => wp_create_nonce( self::NONCE ),
			)
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $attributes
	 * @return void
	 */
	public function show_booking_form( $attributes ) {
		wp_enqueue_style( 'bookslots-style' );
		wp_enqueue_script( 'bookslots-script' );
		$options = get_option( 'bookslots', Includes\default_options() );
		// dd($options);

		switch ( $options['general']['position'] ) {
			case 'middle':
				$position_css = 'tw-mx-auto';
				break;

			default:
				$position_css = '';
				break;
		}

		return Includes\view(
			'form-booking',
			array(
				'theme'        => $options['general']['theme'],
				'position_css' => $position_css,
			),
			true
		);
	}

	public function handle_new_booking() {
		if ( ! isset( $_POST['security'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['security'] ), self::NONCE )
		) {
			wp_send_json_error( array( 'message' => 'Nonce is invalid.' ) );
		}

		$form = isset( $_POST['form'] ) ? sanitize_text_field( wp_unslash( $_POST['form'] ) ) : '';
		$form = (object) json_decode( $form );
		$this->form = $form;

		$this->calendar = new BookingCalendar( $this->form );
		$start_week = 0;

		if (
		isset( $_POST['startWeek'] ) &&
		is_numeric( $_POST['startWeek'] ) &&
		$_POST['startWeek'] >= 1
		) {
			$start_week = isset( $_POST['startWeek'] ) ? absint( wp_unslash( $_POST['startWeek'] ) ) : '';
			$this->calendar->set_start_week( $start_week );
		}

		$this->calendar->set_start_date( Carbon::now() );
		$this->calendar->set_week_interval();

		// dd($_POST);
		$action = isset( $_POST['do'] ) ? sanitize_text_field( wp_unslash( $_POST['do'] ) ) : '';

		switch ( $action ) {
			case 'selectDay':
				$this->selectDay( $start_week );
				break;
			case 'selectTime':
				$this->selectTime();
				break;
			case 'getEmployees':
				$service_id = isset( $_POST['service_id'] ) ? absint( $_POST['service_id'] ) : 0;
				$this->getEmployees($service_id);
				break;
			case 'incrementCalendarWeek':
				$this->incrementCalendarWeek();
				break;
			case 'decrementCalendarWeek':
				$this->decrementCalendarWeek();
				break;
			case 'createBooking':
				$this->createBooking($form);
				break;
			default:
				$this->getServices();
				break;
		}
	}

	public function getServices( $args = [] ) {
		$services = Service::find_all( $args );
		$services = Includes\transform( $services, array( 'ID', 'post_title', 'duration' ) );
		$this->send_response( array( 'services' => $services ) );
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function getEmployees( $service_id ) {
		$service   = Service::find( $service_id );
		$employees = $service->employees();
		$employees = Includes\transform( $employees, array( 'ID', 'name' ) );
		wp_send_json_success( $employees );
	}



	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function selectDay( $start_week ) {
		$timeslots = $this->timeSlots( $start_week );
		wp_send_json_success(
			array(
				'slots'        => $timeslots['bookable'],
				// 'available' => $timeslots['available'],
				// 'interval'  => $this->calendar->get_service()->duration,
				'selectedDate' => $this->calendar->get_selected_time(),
				'selected'     => array(
					'timestamp'    => $this->calendar->get_selected_date()->timestamp,
					'day'          => $this->calendar->get_selected_date()->format( 'D jS M Y' ),
					'time'         => $this->calendar->get_selected_date()->format( 'g:i A' ),
					'serviceName'  => $this->calendar->get_service()->name,
					'duration'     => $this->calendar->get_service()->duration,
					'employeeName' => $this->calendar->get_employee()->name,
				),
			),
		);
	}


	public function timeSlots( $startWeek, $withAll = false ) {
		if ( isset( $this->form->day->timestamp ) ) {
			$selected_date = isset( $this->form->day ) ? Carbon::createFromTimestamp( $this->form->day->timestamp ) : Carbon::today();
		} else {
			$selected_date = isset( $this->form->day ) ? new Carbon( $this->form->day ) : Carbon::today();
		}

		$this->calendar->set_start_date( Carbon::now() );

		// if ( $startWeek >= 1 ) {
		// $start_week = absint( esc_attr( $_POST['startWeek'] ) );
		// $this->calendar->set_start_week( $start_week );
		// }

		// if (
		// isset( $_POST['startWeek'] ) &&
		// is_numeric( $_POST['startWeek'] ) &&
		// $_POST['startWeek'] >= 1
		// ) {
		// $start_week = absint( esc_attr( $_POST['startWeek'] ) );
		// $this->calendar->set_start_week( $start_week );
		// }

		$this->calendar->set_week_interval();
		$this->calendar->set_selected_date( $selected_date );
		$this->calendar->set_service( $this->form->service );
		$this->calendar->set_employee( $this->form->employee );
		$this->calendar->set_employee_schedule( $this->form->employee );

		if ( $withAll ) {
			$atimeslots = $this->calendar->all_time_slots();
		}

		$timeslots = $this->calendar->time_slots();
		$slots     = array();
		$aslots    = array();

		foreach ( $timeslots as $slot ) {
			$slots[] = array(
				'time'      => $slot->format( 'g:i A' ),
				'timestamp' => $slot->timestamp,
			);
		}

		if ( $withAll ) {
			foreach ( $atimeslots as $slot ) {
				$aslots[] = array(
					'time'      => $slot->format( 'g:i A' ),
					'timestamp' => $slot->timestamp,
				);
			}
		}

		return array(
			'bookable' => $slots, // bookable time slots
			'all'      => $aslots, // all time slots
		);
	}

	public function createBooking($form) {
		$validator  = new Validator();
		$validation = $validator->make(
			(array) $form,
			array(
				'service'  => 'required|numeric',
				'employee' => 'required|numeric',
				'time'     => 'required|numeric',
				// 'day.number' => 'required|numeric',
			)
		);

		// then validate
		$validation->validate();

		if ( $validation->fails() ) {
			wp_send_json_error( array( 'errorMessages' => $validation->errors()->firstOfAll() ) );
		}

		$time    = Carbon::createFromTimestamp( $form->time );
		$service = Service::find( $form->service );

		$appointment              = new Appointment();
		$appointment->post_title  = wp_generate_uuid4();
		$appointment->token       = wp_generate_uuid4();
		$appointment->service_id  = $form->service;
		$appointment->employee_id = $form->employee;
		$appointment->start_time  = $time->timestamp;
		$appointment->end_time    = $time->copy()->addMinutes(
			$service->duration
		)->timestamp;
		$appointment->date        = $time->toDateString();

		$appointment->ID = Appointment::create( $appointment );
		$appointment->store_meta();

		wp_send_json_success( array( 'message' => 'success' ) );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function incrementCalendarWeek() {
		$this->send_response();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function decrementCalendarWeek() {
		$this->send_response();
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
}
