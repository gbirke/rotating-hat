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

        $startDate = $this->getStartDate( $spec );
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

    private function getStartDate( TaskSpec $taskSpec )
    {
        if ( $taskSpec->getDuration() === Duration::Weekdays ) {
            return $this->getCurrentOrPreviousMonday( $taskSpec->getStartDate() );
        } else {
            return $startDate = $taskSpec->getStartDate();
        }
    }

    private function getCurrentOrPreviousMonday( \DateTime $startDate )
    {
        $dayOfWeek = (int) $startDate->format('N');
        if ( $dayOfWeek === 1 ) {
            return $startDate;
        } else {
            return ( clone $startDate )->modify('-' . ( $dayOfWeek - 1 ) . 'days' );
        }
    }


}