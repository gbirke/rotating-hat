import VCardConverter from '../vard_converter';

import single from './fixtures/vcard_repeat_once';


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

test( 'it returns an event array', () => {
    const converter = new VCardConverter( single );
    expect( converter.getEvents() ).toHaveLength( 2 );
} );
