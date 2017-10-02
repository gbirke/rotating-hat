<?php

use Gbirke\TaskHat\CalendarGenerator;
use Gbirke\TaskHat\Duration;
use Gbirke\TaskHat\TaskSpec;
use PHPUnit\Framework\TestCase;

class CalendarGeneratorTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreatingEventWithLessThanTwoPeopleThrowsException() {
        $generator = new CalendarGenerator();
        $generator->createCalendarObject( new TaskSpec(['alice'], new DateTime(), Duration::Day ) );
    }
}
