import { DateTime, Duration } from 'luxon';
import { RRule } from 'rrule';

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
            event.duration = prop[3];
            break;
        }
        case 'rrule':
            event.rrule = prop[3];
            break;
        }
    } );

    return event;
}

function repeatEvents( events, currentEvent ) {
    if ( typeof currentEvent.rrule !== 'object' ) {
        events.push( currentEvent );
        return events;
    }
    const ruleParams = {
        freq: RRule.FREQUENCIES.indexOf( currentEvent.rrule.freq ),
        interval: currentEvent.rrule.interval,
        dtstart: currentEvent.start,
    };
    if ( typeof currentEvent.rrule.until !== 'undefined' ) {
        ruleParams.until = currentEvent.rrule.until;
    }

    const rule = new RRule( ruleParams );
    rule.all( ( date, i ) => i < 5 ).map( startDate => {
        const newEvent = Object.assign( {}, currentEvent, {
            start: startDate,
            end: DateTime.fromJSDate( startDate ).plus( Duration.fromISO( currentEvent.duration ) ).toJSDate(),
        } );
        events.push( newEvent );
    } );
    return events;
}

function sortEventByDate( a, b ) {
    return a.start == b.start ? 0 : a.start > b.start ? 1 : -1;
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
            .map( veventArrayToEventObject )
            .reduce( repeatEvents, [] )
            .sort( sortEventByDate );
    }
}

export default VCardConverter;