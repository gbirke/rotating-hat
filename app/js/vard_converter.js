class VCardConverter {
    constructor ( vcard ) {
        this.validateVcard( vcard );
        this.vcard = vcard;
    }

    validateVcard( vcard ) {
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
        return [{},{}];
    }
}

export default VCardConverter;