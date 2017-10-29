/* eslint-disable comma-dangle */

const VCalendarSingleEvent = [
    'vcalendar',
    [
        ['version', {}, 'text', '2.0'],
        ['prodid', {}, 'text', 'Test Calendar'], 
        ['calscale', {}, 'text', 'GREGORIAN']
    ],
    [
        [
            'vevent',
            [
                ['uid', {}, 'text', 'sabre-vobject-460d6d51-14df-4cc0-a9e3-9225c8adf736'],
                ['dtstamp', {}, 'date-time', '2017-10-29T08:36:48Z'],
                ['summary', {}, 'text', 'Hero of the day: Alice'],
                ['dtstart', {'tzid': 'CET'}, 'date-time', '2017-10-29T00:00:00'],
                ['duration', {}, 'duration', 'P1D']
            ],
            []
        ],
        [
            'vevent',
            [
                ['uid', {}, 'text', 'sabre-vobject-621e1ff1-a335-4030-9a2e-c1f35cdc4019'],
                ['dtstamp', {}, 'date-time', '2017-10-29T08:36:48Z'],
                ['summary', {}, 'text', 'Hero of the day: Bob'],
                ['dtstart', {'tzid': 'CET'}, 'date-time', '2017-10-30T00:00:00'],
                ['duration', {}, 'duration', 'P1D']
            ],
            []
        ]
    ]
];

export default VCalendarSingleEvent;