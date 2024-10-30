<?php
namespace Bookslots\Filters;

// use Carbon\Carbon;
use Bookslots as App;
use Carbon\CarbonPeriod;
use Bookslots\Includes\Filter;
use Bookslots\Includes\TimeSlotGenerator;

class UnavailabilityFilter implements Filter {

	public $unavailabilities;

	public function __construct( $unavailabilities ) {
		$this->unavailabilities = $unavailabilities;
	}

	public function apply( TimeSlotGenerator $generator, CarbonPeriod $interval ) {

		if ( ! $this->unavailabilities ) {
			return true;
		}

		$interval->addFilter(
			function ( $slot ) use ( $generator ) {
				foreach ( $this->unavailabilities as $unavailability ) {
					$unavailability = (object) $unavailability;
					if ( $slot->between(
						$unavailability->selected_date->setTimeFrom(
							$unavailability->starttime->subMinutes(
								$generator->service->duration - $generator::INCREMENT
							)
						),
						$unavailability->selected_date->setTimeFrom(
							$unavailability->endtime->subMinutes( $generator::INCREMENT )
						)
					) ) {
						return false;
					}
				}
				return true;
			}
		);

	}

}
