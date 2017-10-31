const VCalendarForeverEvent = [
    'vcalendar',
    [
        ['version', {}, 'text', '2.0'],
        ['prodid', {}, 'text', '-//Sabre//Sabre VObject 4.1.2//EN'],
        ['calscale', {}, 'text', 'GREGORIAN']
    ],
    [
        [
            'vevent',
            [
                ['uid', {}, 'text', 'sabre-vobject-ccfd5141-9135-4bdb-92e2-f063c6136bb3'],
                ['dtstamp', {}, 'date-time', '2017-10-29T12:59:31Z'],
                ['summary', {}, 'text', 'Daily Chores: Carol'],
                ['dtstart', {'tzid': 'CET'}, 'date-time', '2017-10-29T00:00:00'],
                ['duration', {}, 'duration', 'P1D'],
                ['rrule', {}, 'recur', {'freq': 'DAILY', 'interval': 2,} ]
            ],
            []
        ],
        [
            'vevent',
            [
                ['uid', {}, 'text', 'sabre-vobject-f0c033dc-228b-4c56-b27e-c4e3fe376133'],
                ['dtstamp', {}, 'date-time', '2017-10-29T12:59:31Z'],
                ['summary', {}, 'text', 'Daily Chores: Dave'],
                ['dtstart', {'tzid': 'CET'}, 'date-time', '2017-10-30T00:00:00'],
                ['duration', {}, 'duration', 'P1D'],
                ['rrule', {}, 'recur', {'freq': 'DAILY', 'interval': 2,} ]
            ],
            []
        ]
    ]
];

export default VCalendarForeverEvent;