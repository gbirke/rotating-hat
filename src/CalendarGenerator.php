<?php

namespace Gbirke\TaskHat;

use Sabre\VObject\Component\VCalendar;

class CalendarGenerator
{
    public function createCalendarObject( TaskSpec $spec ): VCalendar {
        if ( count( $spec->getNames() ) < 2 ) {
            throw new \InvalidArgumentException( 'There must be at least two people' );
        }
        return new VCalendar();

    }
}