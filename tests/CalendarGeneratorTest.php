<?php

use Gbirke\TaskHat\CalendarGenerator;
use Gbirke\TaskHat\Duration;
use Gbirke\TaskHat\Recurrence;
use Gbirke\TaskHat\TaskSpec;
use PHPUnit\Framework\TestCase;
use Sabre\VObject\Component\VCalendar;

class CalendarGeneratorTest extends TestCase
{
    const VCS_TIMESTAMP = 'Ymd\THis\Z';

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGivenLessThenTwoLabels_anExceptionIsThrown() {
        $generator = new CalendarGenerator();
        $generator->createCalendarObject( new TaskSpec(['Alice'], new DateTime(), Duration::Day, Recurrence::newOnce() ) );
    }

    public function testGivenTwoLabels_twoCalendarEventsAreCreated() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(
            ['Alice', 'Bob'],
            new DateTime(),
            Duration::Day,
            Recurrence::newOnce()
        ) );
        $this->assertEventCount( 2, $calendar );
    }

    private function assertEventCount( int $expectedCount, VCalendar $calendar )
    {
        $events =  $calendar->getBaseComponents( 'VEVENT' );
        $this->assertSame( $expectedCount, count( $events ) );
    }

    public function testGivenFiveLabels_fiveCalendarEventsAreCreated() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(
            ['Alice', 'Bob', 'Carol', 'Dave', 'Eva'],
            new DateTime(),
            Duration::Day,
            Recurrence::newOnce()
        ) );
        $this->assertEventCount( 5, $calendar );
    }

    public function testFirstEventStartsOnStartDate() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob'], $start, Duration::Day, Recurrence::newOnce() ) );
        $firstEvent = $calendar->getBaseComponent( 'VEVENT' );
        $this->assertDateTimeMatches( $start, $firstEvent->DTSTART->getValue() );
    }

    public function testGivenDayDuration_eventStartOneDayAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Day, Recurrence::newOnce() ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-10' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-11' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenWeekDuration_eventStartOneWeekAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Week, Recurrence::newOnce() ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-16' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-23' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenMonthDuration_eventStartOneMonthAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Month, Recurrence::newOnce() ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-11-09' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-12-09' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenYearDuration_eventStartOneYearAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-09' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Year, Recurrence::newOnce() ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2018-10-09' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2019-10-09' ), $events[2]->DTSTART->getValue() );
    }

    public function testGivenDailyEvent_DurationIsOneDay() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Day, Recurrence::newOnce() ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1D', $calendar );
    }

    public function testGivenWeeklyEvent_DurationIsOneWeek() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Week, Recurrence::newOnce() ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1W', $calendar );
    }

    public function testGivenMonthlyEvent_DurationIsOneMoth() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Month, Recurrence::newOnce() ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1M', $calendar );
    }

    public function testGivenYearlyEvent_DurationIsOneYear() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Year, Recurrence::newOnce() ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1Y', $calendar );
    }

    public function testGivenOneTimeRecurrence_noRecurrenceRulesAreAdded() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Year, Recurrence::newOnce() ) );
        $this->assertEventsHaveNoProperty( 'RRULE', $calendar );
    }

    /**
     * @dataProvider recurrenceProvider
     */
    public function testAllEventsAreRecuringWithTheGivenRecurrence( $duration, $expectedRecurrenceString ) {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime( '2017-10-14' ), $duration, Recurrence::newForever() ) );
        $this->assertEventsHaveProperty( 'RRULE', $expectedRecurrenceString, $calendar );
    }

    public function recurrenceProvider(): \Iterator {
        yield [Duration::Day, 'FREQ=DAILY;INTERVAL=3'];
        yield [Duration::Week, 'FREQ=WEEKLY;INTERVAL=3'];
        yield [Duration::Month, 'FREQ=MONTHLY;INTERVAL=3'];
        yield [Duration::Year, 'FREQ=YEARLY;INTERVAL=3'];
    }

    public function testWhenRecurrenceHasEndDate_allEventsEndOnThatDay() {
        $generator = new CalendarGenerator();
        $end = new DateTime( '2017-12-24' );
        $calendar = $generator->createCalendarObject( new TaskSpec(
            ['Alice', 'Bob' ],
            new DateTime( '2017-10-14' ),
            Duration::Day,
            Recurrence::newUntil( $end )
        ) );
        $expectedRecurrence = 'FREQ=DAILY;INTERVAL=2;UNTIL=20171224T000000Z';
        $this->assertEventsHaveProperty( 'RRULE', $expectedRecurrence, $calendar );
    }

    private function assertDateTimeMatches( DateTime $expectedTime, string $value ) {
        $this->assertSame( $expectedTime->format(self::VCS_TIMESTAMP), $value );
    }

    private function assertEventsHaveProperty( string $propertyName, $property, VCalendar $calendar)
    {
        foreach( $calendar->getBaseComponents( 'VEVENT' ) as $event ) {
            $this->assertSame( $event->{$propertyName}->getValue(), $property );
        }
    }

    private function assertEventsHaveNoProperty( string $propertyName, VCalendar $calendar)
    {
        foreach( $calendar->getBaseComponents( 'VEVENT' ) as $event ) {
            $this->assertNull( $event->{$propertyName} );
        }
    }
}
