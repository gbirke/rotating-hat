<?php

namespace Gbirke\TaskHat;

use Sabre\VObject\Component\VCalendar;

class CalendarGenerator
{
    public function createCalendarObject( TaskSpec $spec ): VCalendar {
        if ( count( $spec->getLabels() ) < 2 ) {
            throw new \InvalidArgumentException( 'There must be at least two labels' );
        }
        $cal = new VCalendar();

        $startDate = $spec->getStartDate();
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

    private function getRecurrence( TaskSpec $spec ): array
    {
        $durationMap = [
            Duration::Day => 'DAILY',
            Duration::Week => 'WEEKLY',
            Duration::Month => 'MONTHLY',
            Duration::Year => 'YEARLY',
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