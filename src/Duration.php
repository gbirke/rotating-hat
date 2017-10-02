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
    const Workweek = 5;
    const Weekend = 6;

    public static function getIntervalFromDuration( int $duration ): \DateInterval
    {
        $map = [
            self::Day => 'P1D',
            self::Week => 'P1W',
            self::Month => 'P1M',
            self::Year => 'P1Y',
            self::Workweek => 'P1W',
            self::Weekend => 'P1W'
        ];
        if ( empty( $map[$duration] ) ) {
            throw new InvalidArgumentException( 'Unknown duration' );
        }
        return new \DateInterval( $map[$duration] );
    }

    public static function getDurationSpec( int $duration ): string
    {
        $map = [
            self::Day => 'P1D',
            self::Week => 'P1W',
            self::Month => 'P1M',
            self::Year => 'P1Y',
            self::Workweek => 'P5D',
            self::Weekend => 'P2D'
        ];
        if ( empty( $map[$duration] ) ) {
            throw new InvalidArgumentException( 'Unknown duration' );
        }
        return $map[$duration];
    }

}