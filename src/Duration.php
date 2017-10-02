<?php

namespace Gbirke\TaskHat;

use \InvalidArgumentException;

class Duration
{
    const __default = self::Day;

    const Day = 1;
    const Week = 2;
    const Month = 3;
    const Year = 4;
    const Weekdays = 5;
    const Weekends = 6;

    public static function getIntervalFromDuration( int $duration ): \DateInterval
    {
        $map = [
            self::Day => 'P1D',
            self::Week => 'P1W',
            self::Month => 'P1M',
            self::Year => 'P1Y',
            self::Weekdays => 'P1W',
            self::Weekends => 'P1W'
        ];
        if ( empty( $map[$duration] ) ) {
            throw new InvalidArgumentException( 'Unknown duration' );
        }
        return new \DateInterval( $map[$duration] );
    }

}