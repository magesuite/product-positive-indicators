<?php
$scenarios = [
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '14.03.2018
            19.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 16,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Friday', 'nextShipDayName' => 'Wednesday', 'maxTodayTime' => 1521154800, 'isNextDayTomorrow' => false]
    ],
    [
        [
            'working_days' => '1,2,3,4,5,7',
            'holidays' => '14.03.2018
            25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 16,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Friday', 'nextShipDayName' => 'Monday', 'maxTodayTime' => 1521154800, 'isNextDayTomorrow' => false]
    ],
    [
        [
            'working_days' => '1,2,3,4,5,7',
            'holidays' => '14.03.2018
            25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 2,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Friday', 'nextShipDayName' => 'Sunday', 'maxTodayTime' => 1521205200, 'isNextDayTomorrow' => false]
    ],
    [
        [
            'working_days' => '2',
            'holidays' => '14.03.2018
            25.03.2018',
            'working_hours' => 3,
            'order_queue_length' => 8,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Friday', 'nextShipDayName' => 'Tuesday', 'maxTodayTime' => 1521129600, 'isNextDayTomorrow' => false]
    ],
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '19.03.2018
            20.03.2018
            21.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 4,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Friday', 'nextShipDayName' => 'Thursday', 'maxTodayTime' => 1521198000, 'isNextDayTomorrow' => false]
    ],
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '14.03.2018
            25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 0,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521126600,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Thursday', 'nextShipDayName' => 'Friday', 'maxTodayTime' => 1521126000, 'isNextDayTomorrow' => true]
    ],
    [
        [
            'working_days' => '1,2,3,4,5,7',
            'holidays' => '14.03.2018
            25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 0,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Friday', 'nextShipDayName' => 'Sunday', 'maxTodayTime' => 1521212400, 'isNextDayTomorrow' => false]
    ],
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '',
            'working_hours' => 8,
            'order_queue_length' => 0,
            'delivery_today_time' => '14:00',
            'timestamp' => 1521068400,
            'utc_offset' => 0
        ],
        ['shipDayName' => 'Wednesday', 'nextShipDayName' => 'Thursday', 'maxTodayTime' => 1521036000, 'isNextDayTomorrow' => true]
    ]
];
