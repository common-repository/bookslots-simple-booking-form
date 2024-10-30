<?php
namespace Bookslots\Filters;

use Carbon\Carbon;
use Bookslots as App;
use Carbon\CarbonPeriod;
use Bookslots\Includes\Filter;
use Bookslots\Includes\TimeSlotGenerator;

class AppointmentFilter implements Filter {

	public $appointments;

	public function __construct( $appointments ) {
		$this->appointments = $appointments;
	}

	public function apply( TimeSlotGenerator $generator, CarbonPeriod $interval ) {
		if ( ! $this->appointments ) {
			return true;
		}

		$interval->addFilter(
			function ( $slot ) use ( $generator ) {
				foreach ( $this->appointments as $appointment ) {
					if ( $slot->between(
						Carbon::parse( $appointment->date )->setTimeFrom(
							Carbon::createFromTimestamp( $appointment->start_time )->subMinutes( $generator->service->duration )
						),
						Carbon::parse( $appointment->date )->setTimeFrom(
							Carbon::createFromTimestamp( $appointment->end_time )
						)
					) ) {
						return false;
					}
				}

				// dd('here');
				// dd(
				// Carbon::parse( $appointment->date )->setTimeFrom(
				// Carbon::createFromTimestamp( $appointment->end_time )
				// )
				// );
				// dd( Carbon::createFromTimestamp( $appointment->start_time ) );
				// dd( Carbon::parse( $appointment->start_time ) );
				return true;
			}
		);
	}

}
