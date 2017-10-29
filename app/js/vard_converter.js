import { DateTime, Duration } from 'luxon';

function veventArrayToEventObject( vevent ) {
    const event = {};
    const getProp = function ( vevent, propname ) {
        return vevent[1].filter( prop => prop[0] === propname ).shift();
    };
    vevent[1].map( prop => {
        switch( prop[0] ) {
        case 'summary':
            event.summary = prop[3];
            break;
        case 'dtstart':
            event.start = DateTime.fromISO( prop[3], { zone: prop[1].tzid } ).toJSDate();
            break;
        case 'duration': {
            const startProp = getProp( vevent, 'dtstart' );
            const start = DateTime.fromISO( startProp[3], { zone: startProp[1].tzid } );
            event.end = start.plus( Duration.fromISO( prop[3] ) ).toJSDate();
            break;
        }
        }
    } );

    return event;
}


class VCardConverter {
    constructor ( vcard ) {
        VCardConverter.validateVcard( vcard );
        this.vcard = vcard;
    }

    static validateVcard( vcard ) {
        if ( !Array.isArray( vcard ) ) {
            throw new Error( 'VCalendar must be an array' );
        }

        if ( vcard[0] !== 'vcalendar' ) {
            throw new Error( 'Array must start with vcalendar string' );
        }

        if ( vcard.length < 3 ) {
            throw new Error( 'Vcalendar must have 3 elements' );
        }
    }

    getEvents() {
        return this.vcard[2]
            .filter( element => element[0] === 'vevent' )
            .map( veventArrayToEventObject );
    }
}

export default VCardConverter;