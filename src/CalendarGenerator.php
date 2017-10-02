<?php

namespace Gbirke\TaskHat;

use DateInterval;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Component\VEvent;

class CalendarGenerator
{
    public function createCalendarObject( TaskSpec $spec ): VCalendar {
        if ( count( $spec->getNames() ) < 2 ) {
            throw new \InvalidArgumentException( 'There must be at least two people' );
        }
        $cal = new VCalendar();

        $startDate = $spec->getStartDate();
        $interval = Duration::getIntervalFromDuration( $spec->getDuration() );
        foreach( $spec->getNames() as $name ) {
            $cal->add( 'VEVENT', [
                'SUMMARY' => $name,
                'DTSTART' => $startDate
            ] );
            $startDate = (clone $startDate)->add( $interval );
        }
        return $cal;
    }


}