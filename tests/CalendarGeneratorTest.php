<?php

use Gbirke\TaskHat\CalendarGenerator;
use Gbirke\TaskHat\Duration;
use Gbirke\TaskHat\TaskSpec;
use PHPUnit\Framework\TestCase;
use Sabre\VObject\Component\VCalendar;

class CalendarGeneratorTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGivenLessThenTwoPeople_anExceptionIsThrown() {
        $generator = new CalendarGenerator();
        $generator->createCalendarObject( new TaskSpec(['Alice'], new DateTime(), Duration::Day ) );
    }

    public function testGivenTwoPeople_twoCalendarEventsAreCreated() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob'], new DateTime(), Duration::Day ) );
        $this->assertEventCount( 2, $calendar );
    }

    private function assertEventCount( int $expectedCount, VCalendar $calendar )
    {
        $events =  $calendar->getBaseComponents( 'VEVENT' );
        $this->assertSame( $expectedCount, count( $events ) );
    }

    public function testGivenFivePeople_fiveCalendarEventsAreCreated() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(
            ['Alice', 'Bob', 'Carol', 'Dave', 'Eva'],
            new DateTime(),
            Duration::Day
        ) );
        $this->assertEventCount( 5, $calendar );
    }

    public function testFirstEventStartsOnStartDate() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob'], $start, Duration::Day ) );
        $firstEvent = $calendar->getBaseComponent( 'VEVENT' );
        $this->assertDateTimeMatches( $start, $firstEvent->DTSTART->getValue() );
    }

    public function testGivenDayDuration_eventStartOneDayAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Day ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-10' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-11' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenWeekDuration_eventStartOneWeekAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Week ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-16' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-23' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenMonthDuration_eventStartOneMonthAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Month ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-11-09' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-12-09' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenYearDuration_eventStartOneYearAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Year ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2018-10-09' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2019-10-09' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenWeekdayDuration_eventStartOneWeekAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Weekdays ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-16' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-23' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenWeekdayDurationStartingOnTuesday_eventsStartOnMondayOneWeekAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-10' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Weekdays ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-09' ), $events[0]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-16' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-23' ), $events[2]->DTSTART->getValue() );
    }


    private function assertDateTimeMatches( DateTime $expectedTime, string $value ) {
        $this->assertSame( $expectedTime->format( 'Ymd\THis\Z' ), $value );
    }
}
