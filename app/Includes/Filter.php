<?php
namespace Bookslots\Includes;

use Carbon\CarbonPeriod;
use Bookslots\Includes\TimeSlotGenerator;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Filter {

	public function apply( TimeSlotGenerator $timeSlotGenerator, CarbonPeriod $interval );

}
