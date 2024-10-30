<?php
namespace Bookslots\Filters;

use Carbon\Carbon;
use Booklots as App;
use Carbon\CarbonPeriod;
use Bookslots\Includes\Filter;
use Bookslots\Includes\TimeSlotGenerator;

class SlotsPassedTodayFilter implements Filter {

	public function apply( TimeSlotGenerator $timeSlotGenerator, CarbonPeriod $interval ) {
		$interval->addFilter(
			function ( $slot ) use ( $timeSlotGenerator ) {
				if ( $timeSlotGenerator->schedule->date->isToday() ) {
					if ( $slot->lt( Carbon::now() ) ) {
						return false;
					}
				}
				return true;
			}
		);
	}

}
