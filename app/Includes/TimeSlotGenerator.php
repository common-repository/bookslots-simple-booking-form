<?php
namespace Bookslots\Includes;

use Carbon\Carbon;
use Bookslots\Service;
use Bookslots\Schedule;
use Carbon\CarbonInterval;
use Bookslots\Includes\Filter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TimeSlotGenerator {

	/**
	 * The version of our database table
	 *
	 * @access  public
	 * @since   2.1
	 */
	public $version;

	protected $interval;

	public const INCREMENT = 15;
	public $schedule;
	public $service;
	public $date;

	public function __construct( object $schedule, Service $service ) {
		$this->schedule = $schedule;
		$this->service  = $service;

		$this->interval = CarbonInterval::minutes( self::INCREMENT )
		->toPeriod(
			$schedule->date->setTimeFrom(
				$schedule->start_time
			),
			$schedule->date->setTimeFrom(
				$schedule->end_time->subMinutes( $service->duration )
			)
		);
	}

	public function applyFilters( array $filters ) {
		foreach ( $filters as $filter ) {
			if ( ! $filter instanceof Filter ) {
				continue;
			}
			$filter->apply( $this, $this->interval );
		}
		return $this;
	}

	public function get() {
		return $this->interval;
	}


}
