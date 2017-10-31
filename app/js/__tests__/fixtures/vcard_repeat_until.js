/* eslint-disable comma-dangle */

const VCalendarUntilEvent = [
    'vcalendar',
    [
        [ 'version',{},'text','2.0' ],
        [ 'prodid',{},'text','Test Calendar' ],
        [ 'calscale',{},'text','GREGORIAN' ]
    ],
    [
        [
            'vevent',
            [
                ['uid',{},'text','sabre-vobject-d395c656-8478-4f44-a66c-6b74c26e964b'],
                ['dtstamp',{},'date-time','2017-10-31T12:21:04Z'],
                ['summary',{},'text','DAILY Host: Alice'],
                ['dtstart',{'tzid':'CET'},'date-time','2017-10-29T00:00:00'],
                ['duration',{},'duration','P1D'],
                ['rrule',{},'recur',{'freq':'DAILY','interval':3,'until':'2017-11-04T00:00:00Z'}]
            ],
            []
        ],
        [
            'vevent',
            [
                ['uid',{},'text','sabre-vobject-c91f50cb-a2d4-4766-bbed-60171251bd10'],
                ['dtstamp',{},'date-time','2017-10-31T12:21:04Z'],
                ['summary',{},'text','DAILY Host: Bob'],
                ['dtstart',{'tzid':'CET'},'date-time','2017-10-30T00:00:00'],
                ['duration',{},'duration','P1D'],
                ['rrule',{},'recur',{'freq':'DAILY','interval':3,'until':'2017-11-04T00:00:00Z'}]
            ],
            []
        ],
        [
            'vevent',
            [
                ['uid',{},'text','sabre-vobject-cfd6b80d-a878-4dac-a71b-d943fd1e4f74'],
                ['dtstamp',{},'date-time','2017-10-31T12:21:04Z'],
                ['summary',{},'text','DAILY Host: Carol'],
                ['dtstart',{'tzid':'CET'},'date-time','2017-10-31T00:00:00'],
                ['duration',{},'duration','P1D'],
                ['rrule',{},'recur',{'freq':'DAILY','interval':3,'until':'2017-11-04T00:00:00Z'}]
            ],
            []
        ]
    ]
];

export default VCalendarUntilEvent;