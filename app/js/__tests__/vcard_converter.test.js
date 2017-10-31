import VCardConverter from '../vard_converter';

import { DateTime } from 'luxon';

import single from './fixtures/vcard_repeat_once';
import forever from './fixtures/vcard_repeat_forever';


describe('VCardConverter checks the data format and throws exceptions', () => {
    test( 'Constructor only accepts arrays', () => {
        expect(() => {
            new VCardConverter('foo');
        }).toThrow();
    } );

    test( 'Constructor array must not be empty', () => {
        expect( () => {
            new VCardConverter( [] );
        }).toThrow();
    } );

    test( 'Constructor array must be vcalendar', () => {
        expect( () => {
            new VCardConverter( [ 'vcard', [], [] ] );
        }).toThrow();
    } );

    test( 'Constructor array must have 3 elements', () => {
        expect( () => {
            new VCardConverter( [ 'vcalendar', [] ] );
        }).toThrow();
    });

} );

describe( 'Conversion to event object', () => {
    test( 'it returns an event array', () => {
        const converter = new VCardConverter( single );
        expect( converter.getEvents() ).toHaveLength( 2 );
    } );

    test( 'it returns event summaries', () => {
        const converter = new VCardConverter( single );
        const events = converter.getEvents();
        expect( events[0].summary ).toBe( 'Hero of the day: Alice' );
        expect( events[1].summary ).toBe( 'Hero of the day: Bob' );
    } );

    test( 'it returns start dates', () => {
        const converter = new VCardConverter( single );
        const events = converter.getEvents();
        expect( events[0].start ).toEqual( DateTime.fromObject( {
            year: 2017,
            month: 10,
            day: 29,
            zone: 'CET',
        } ).toJSDate() );

        expect( events[1].start ).toEqual( DateTime.fromObject( {
            year: 2017,
            month: 10,
            day: 30,
            zone: 'CET',
        } ).toJSDate() );
    } );

    test( 'it calculates end dates', () => {
        const converter = new VCardConverter( single );
        const events = converter.getEvents();
        expect( events[0].end ).toEqual( DateTime.fromObject( {
            year: 2017,
            month: 10,
            day: 30,
            zone: 'CET',
        } ).toJSDate() );

        expect( events[1].end ).toEqual( DateTime.fromObject( {
            year: 2017,
            month: 10,
            day: 31,
            zone: 'CET',
        } ).toJSDate() );
    } );

    describe( 'recurring vcalendar entries', () => {

        test( 'it generates five events for each summary', () => {
            const converter = new VCardConverter( forever );
            const events = converter.getEvents();
            expect( events ).toHaveLength(10);
        } );

        test( 'it generates the correct dates, interleaves summaries and sorts by date', () => {
            const converter = new VCardConverter( forever );
            const events = converter.getEvents();
            expect( events[0].start ).toEqual( DateTime.fromObject( {
                year: 2017,
                month: 10,
                day: 29,
                zone: 'CET',
            } ).toJSDate() );
            expect( events[0].summary ).toBe( 'Daily Chores: Carol' );

            expect( events[1].start ).toEqual( DateTime.fromObject( {
                year: 2017,
                month: 10,
                day: 30,
                zone: 'CET',
            } ).toJSDate() );
            expect( events[1].summary ).toBe( 'Daily Chores: Dave' );

            expect( events[2].start ).toEqual( DateTime.fromObject( {
                year: 2017,
                month: 10,
                day: 31,
                zone: 'CET',
            } ).toJSDate() );
            expect( events[2].summary ).toBe( 'Daily Chores: Carol' );


            expect( events[9].start ).toEqual( DateTime.fromObject( {
                year: 2017,
                month: 11,
                day: 7,
                zone: 'CET',
            } ).toJSDate() );
            expect( events[9].summary ).toBe( 'Daily Chores: Dave' );
        } );

    });

} );


