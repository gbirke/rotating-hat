<?php

namespace Gbirke\TaskHat;

use Sabre\VObject\Component\VCalendar;

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
            $event = [
                'SUMMARY' => $name,
                'DTSTART' => $startDate,
                'DURATION' => Duration::getDurationSpec( $spec->getDuration() )
            ];
            $endDate = $spec->getEndDate();
            if ( !is_null( $endDate ) ) {
                $event['DTEND'] = $endDate;
            }
            $cal->add( 'VEVENT', $event );
            $startDate = (clone $startDate)->add( $interval );
        }
        return $cal;
    }

    private function getStartDate( TaskSpec $taskSpec )
    {
        if ( $taskSpec->getDuration() === Duration::Weekdays && !$this->taskStartsOnMonday( $taskSpec ) ) {
            throw new \InvalidArgumentException( 'Weekday tasks must start on Monday' );
        }
        if ( $taskSpec->getDuration() === Duration::Weekends && !$this->taskStartsOnSaturday( $taskSpec ) ) {
            throw new \InvalidArgumentException( 'Weekend tasks must start on Saturday' );
        }
        return $startDate = $taskSpec->getStartDate();
    }

    private function taskStartsOnMonday( TaskSpec $taskSpec): bool
    {
        return $taskSpec->getStartDate()->format( 'N' ) === '1';
    }

    private function taskStartsOnSaturday( TaskSpec$taskSpec ): bool
    {
        return $taskSpec->getStartDate()->format( 'N' ) === '6';
    }


}