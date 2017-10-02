<?php

use Gbirke\TaskHat\CalendarGenerator;
use Gbirke\TaskHat\Duration;
use Gbirke\TaskHat\TaskSpec;
use PHPUnit\Framework\TestCase;
use Sabre\VObject\Component\VCalendar;

class CalendarGeneratorTest extends TestCase
{
    const VCS_TIMESTAMP = 'Ymd\THis\Z';

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
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Workweek ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-16' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-23' ), $events[2]->DTSTART->getValue() );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGivenWeekdayDurationStartingOnTuesday_exceptionIsThrown() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-10' );
        $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Workweek ) );
    }

    public function testGivenWeekendDuration_eventStartOneWeekAfterEachOther() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-14' );
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Weekend ) );
        $events = $calendar->getBaseComponents( 'VEVENT' );
        $this->assertDateTimeMatches( new DateTime( '2017-10-21' ), $events[1]->DTSTART->getValue() );
        $this->assertDateTimeMatches( new DateTime( '2017-10-28' ), $events[2]->DTSTART->getValue() );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGivenWeekendDurationStartingOnTuesday_exceptionIsThrown() {
        $generator = new CalendarGenerator();
        $start = new DateTime( '2017-10-10' );
        $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], $start, Duration::Weekend ) );
    }

    public function testGivenDailyEvent_DurationIsOneDay() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Day ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1D', $calendar );
    }

    public function testGivenWeeklyEvent_DurationIsOneWeek() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Week ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1W', $calendar );
    }

    public function testGivenMonthlyEvent_DurationIsOneMoth() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Month ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1M', $calendar );
    }

    public function testGivenYearlyEvent_DurationIsOneYear() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime(), Duration::Year ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P1Y', $calendar );
    }

    public function testGivenWorkweekEvent_DurationIsFiveDays() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime( '2017-10-09' ), Duration::Workweek ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P5D', $calendar );
    }

    public function testGivenWeekendEvent_DurationIsFiveDays() {
        $generator = new CalendarGenerator();
        $calendar = $generator->createCalendarObject( new TaskSpec(['Alice', 'Bob', 'Carol' ], new DateTime( '2017-10-14' ), Duration::Weekend ) );
        $this->assertEventsHaveProperty( 'DURATION', 'P2D', $calendar );
    }

    public function testWhenEndDateIsGiven_allEventsEndOnThatDay() {
        $generator = new CalendarGenerator();
        $end = new DateTime( '2017-12-24' );
        $calendar = $generator->createCalendarObject( new TaskSpec(
            ['Alice', 'Bob', 'Carol' ],
            new DateTime( '2017-10-14' ),
            Duration::Weekend,
            $end
        ) );
        $this->assertEventsHaveProperty( 'DTEND', $end->format( self::VCS_TIMESTAMP ), $calendar );
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
}
