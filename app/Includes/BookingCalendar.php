<?php

namespace Bookslots\Includes;

use Carbon\Carbon;
use Bookslots\Service;
use Bookslots\Employee;
use Carbon\CarbonInterval;
use Carbon\CarbonImmutable;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BookingCalendar {


	/**
	 * The version of our database table
	 *
	 * @access  public
	 * @since   2.1
	 */
	private $timezone;
	private $selected_date;
	private $selected_time;
	private $start_date;
	private $employee_id;
	private $employee;
	private $employee_schedule;
	private $service_id;
	private $service;
	private $start_week    = 0;
	private $week_interval = array();
	private $form;

	public function __construct( $payload ) {
		$this->form = $payload;
	}

	public function get_selected_date() {
		return $this->selected_date;
	}

	public function get_selected_time() {
		return $this->selected_time;
	}

	public function get_start_date() {
		return $this->start_date;
	}

	public function get_week_interval() {
		return $this->week_interval;
	}

	public function get_service() {
		return $this->service;
	}

	public function get_employee() {
		return $this->employee;
	}

	public function set_timezone($schedule = []) {
		$this->timezone = $schedule->timezone ? $schedule->timezone : wp_timezone_string();
	}

	public function set_start_date( Carbon $date ) {
		$this->start_date = $date;
	}

	public function set_start_week( $start_week ) {
		$this->start_week = $start_week;
	}

	public function set_week_interval() {
		$this->start_date->addWeeks( $this->start_week );
		foreach ( $this->calendar_week_interval() as $day ) {
			$this->week_interval[] = array(
				'number'    => $day->format( 'd' ),
				'word'      => $day->format( 'D' ),
				'timestamp' => $day->timestamp, // timestamp of today
			);
		}
	}


	public function set_selected_date( $selected_date = '' ) {
		if ( $selected_date && $selected_date instanceof Carbon ) {
			$this->selected_date = $selected_date;
		} else {
			$this->selected_date = isset( $this->form->day->timestamp ) && ! empty( $this->form->day->timestamp ) ? Carbon::createFromTimestamp( $this->form->day->timestamp ) : Carbon::today();
		}
	}

	public function set_selected_time( $time ) {
		// $this->selected_time = isset( $this->form->time ) && ! empty( $this->form->time ) ? Carbon::createFromTimestamp( $this->form->time ) : Carbon::today();
		$this->selected_time = ! empty( $time ) ? Carbon::createFromTimestamp( $time ) : Carbon::today();
	}


	public function set_employee_schedule( $employee_id ) {
		$this->employee_id = isset( $employee_id ) ? $employee_id : 0;
		$schedule          = (object) $this->employee->schedule();

		// set the employee timezone, for now use site timezone
		$this->set_timezone($schedule);

		$availability   = (object) $schedule->availability;
		$unavailability = (object) $schedule->unavailability;
		$selected_date  = $this->selected_date;

		if ( $availability->datetype == 'singleday' ) {
			$start_date = CarbonImmutable::parse( $availability->startdate, $this->timezone );

			if ( $start_date->toDateString() !== $this->selected_date->toDateString() ) {
				return false;
			}

			$availability->date       = $start_date;
			$availability->start_time = $this->selected_date->copy()->setTimeFromTimeString( $availability->starttime );
			$availability->end_time   = $this->selected_date->copy()->setTimeFromTimeString( $availability->endtime );
		}

		if ( $availability->datetype == 'multidays' ) {
			$start_date = Carbon::parse( $availability->startdate );
			$end_date   = Carbon::parse( $availability->enddate );
			$weekday    = $this->selected_date->dayOfWeek;
			$dayofweek  = $availability->days[ $weekday - 1 ];

			if ( ! $this->selected_date->between( $start_date, $end_date->endOfDay() ) ) {
				return false;
			}

			$availability->date       = CarbonImmutable::parse( $this->selected_date->toDateString(), $this->timezone );
			$availability->start_time = $this->selected_date->copy()->setTimeFromTimeString( $availability->starttime );
			$availability->end_time   = $this->selected_date->copy()->setTimeFromTimeString( $availability->endtime );
		}

		if ( $availability->datetype == 'everyday' ) {
			$weekday                  = $this->selected_date->dayOfWeek;
			$dayofweek                = $availability->days[ $weekday - 1 ];
			$availability->date       = CarbonImmutable::parse( $this->selected_date->toDateString(), $this->timezone );
			$availability->start_time = Carbon::now()->endOfDay();
			$availability->end_time   = Carbon::yesterday()->startOfDay();

			if ( $dayofweek['status'] === 'true' || $dayofweek['status'] === true ) {
				$availability->start_time = $this->selected_date->copy()->setTimeFromTimeString( $dayofweek['start'] );
				$availability->end_time   = $this->selected_date->copy()->setTimeFromTimeString( $dayofweek['end'] );
			}
		}

		// Get unavailability
		$unavailabilities = array();
		$days             = array(
			'sunday',
			'monday',
			'tuesday',
			'wednesday',
			'thursday',
			'friday',
			'saturday',
		);

		if ( isset( $unavailability->slots ) && is_array( $unavailability->slots ) ) {
			$unavailabilities = array_reduce(
				$unavailability->slots,
				function ( $bag, $unavailability ) use ( $selected_date, $days ) {

					$unavailability['selected_date'] = CarbonImmutable::parse( $selected_date->toDateString() );

					// startdate
					if ( $unavailability['datetype'] == 'everyday' ) {
						$bag[] = $unavailability;
					} elseif ( $unavailability['datetype'] == 'singleday' ) {
						$unavailability['startdate'] = isset( $unavailability['startdate'] ) ? CarbonImmutable::parse( $unavailability['startdate'] ) : '';
						$unavailability['starttime'] = isset( $unavailability['starttime'] ) ? $unavailability['startdate']->setTimeFromTimeString( CarbonImmutable::parse( $unavailability['starttime'] )->format( 'H:i:s' ) ) : '';
						$unavailability['endtime']   = isset( $unavailability['endtime'] ) ? $unavailability['startdate']->setTimeFromTimeString( CarbonImmutable::parse( $unavailability['endtime'] )->format( 'H:i:s' ) ) : '';

						if ( $selected_date->toDateString() == Carbon::parse( $unavailability['startdate'] )->toDateString() ) {
							$bag[] = $unavailability;
						}
					} elseif ( in_array( $unavailability['datetype'], $days ) ) {
						// dd($selected_date->dayOfWeek);
						if ( $days[ $selected_date->dayOfWeek ] == $unavailability['datetype'] ) {

							$unavailability['startdate'] = $unavailability['selected_date'];
							$unavailability['starttime'] = isset( $unavailability['starttime'] ) ? $unavailability['startdate']->setTimeFromTimeString( CarbonImmutable::parse( $unavailability['starttime'] )->format( 'H:i:s' ) ) : '';

							$unavailability['endtime'] = isset( $unavailability['endtime'] ) ? $unavailability['startdate']->setTimeFromTimeString( CarbonImmutable::parse( $unavailability['endtime'] )->format( 'H:i:s' ) ) : '';

							$bag[] = $unavailability;
						}
					}

					return $bag;
				},
				array()
			);
		}

		$this->employee_schedule = $availability;
		$this->availability      = $availability;
		$this->unavailability    = $unavailabilities;
	}

	public function set_service( $service_id ) {
		// $this->service_id = isset( $this->form->service ) ? $this->form->service : 0;
		$this->service_id = isset( $service_id ) ? absint( $service_id ) : 0;
		$service          = Service::find( $this->service_id );
		$this->service    = $service;
	}

	public function set_employee( $employee_id ) {
		$this->employee_id = isset( $employee_id ) ? $employee_id : 0;
		$employee          = Employee::find( $this->employee_id );
		$this->employee    = $employee;
	}

	public function calendar_week_interval() {
		return CarbonInterval::days( 1 )
		->toPeriod(
			$this->start_date,
			$this->start_date->copy()->addWeek(),
		);
	}

	public function all_time_slots() {
		if ( ! $this->employee_id || ! $this->employee_schedule ) {
			return array();
		}

		$employee  = ( new Employee() )->find( $this->employee_id );
		$timeslots = $employee->allTimeSlots( $this->employee_schedule, $this->service, $this->selected_date );

		return $timeslots;
	}

	public function time_slots() {
		if ( ! $this->employee_id || ! $this->employee_schedule ) {
			return array();
		}
		// dd($this);
		$employee  = Employee::find( $this->employee_id );
		$timeslots = $employee->timeSlots( $this->availability, (array) $this->unavailability, $this->service, $this->selected_date );

		return $timeslots;
	}
}
