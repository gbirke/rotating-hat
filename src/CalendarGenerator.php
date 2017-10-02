<?php

namespace Gbirke\TaskHat;

use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Property\ICalendar\Date;
use Sabre\VObject\Property\ICalendar\Recur;

class CalendarGenerator
{
    public function createCalendarObject( TaskSpec $spec ): VCalendar {
        if ( count( $spec->getLabels() ) < 2 ) {
            throw new \InvalidArgumentException( 'There must be at least two labels' );
        }
        $cal = new VCalendar();

        $startDate = $this->getStartDate( $spec );
        $interval = Duration::getIntervalFromDuration( $spec->getDuration() );
        $recurrence = $this->getRecurrence( $spec );
        foreach($spec->getLabels() as $label ) {
            $event = [
                'SUMMARY' => $label,
                'DTSTART' => $startDate,
                'DURATION' => Duration::getDurationSpec( $spec->getDuration() ),
                'RRULE' => $recurrence
            ];

            $cal->add( 'VEVENT', $event );
            $startDate = (clone $startDate)->add( $interval );
        }
        return $cal;
    }

    private function getStartDate( TaskSpec $taskSpec )
    {
        if ( $taskSpec->getDuration() === Duration::Workweek && !$this->taskStartsOnMonday( $taskSpec ) ) {
            throw new \InvalidArgumentException( 'Weekday tasks must start on Monday' );
        }
        if ( $taskSpec->getDuration() === Duration::Weekend && !$this->taskStartsOnSaturday( $taskSpec ) ) {
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

    private function getRecurrence( TaskSpec $spec ): array
    {
        $durationMap = [
            Duration::Day => 'DAILY',
            Duration::Week => 'WEEKLY',
            Duration::Month => 'MONTHLY',
            Duration::Year => 'YEARLY',
            Duration::Workweek => 'WEEKLY',
            Duration::Weekend => 'WEEKLY'
        ];
        $recurrence = [
            'FREQ' => $durationMap[$spec->getDuration()],
            'INTERVAL' => count( $spec->getLabels() )
        ];
        if ( $spec->getEndDate() ) {
            $recurrence['UNTIL'] = $spec->getEndDate()->format( 'Ymd\THis\Z' );
        }
        return $recurrence;
    }


}