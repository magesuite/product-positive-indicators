<?php
$scenarios = [
    [
        [
            'active' => 1,
            'working_days' => '1,2,3,4,5',
            'holidays' => '14.03.2018, 19.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 16,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['day' => 'other', 'time' => 1521633600, 'deliveryDay' => __('Wednesday'), 'utcOffset' => 0]
    ],
    [
        [
            'active' => 1,
            'working_days' => '1,2,3,4,5,7',
            'holidays' => '14.03.2018, 25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 16,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['day' => 'other', 'time' => 1521460800, 'deliveryDay' => __('Monday'), 'utcOffset' => 0]
    ],
    [
        [
            'active' => 1,
            'working_days' => '1,2,3,4,5,7',
            'holidays' => '14.03.2018, 25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 2,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0

        ],
        ['day' => 'today', 'time' => 1521205200, 'deliveryDay' => __('Friday'), 'utcOffset' => 0]
    ],
    [
        [
            'active' => 1,
            'working_days' => '2',
            'holidays' => '14.03.2018, 25.03.2018',
            'working_hours' => 3,
            'order_queue_length' => 8,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['day' => 'other', 'time' => 1522152000, 'deliveryDay' => __('Tuesday'), 'utcOffset' => 0]
    ],
    [
        [
            'active' => 1,
            'working_days' => '1,2,3,4,5',
            'holidays' => '19.03.2018, 20.03.2018, 21.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 4,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['day' => 'other', 'time' => 1521720000, 'deliveryDay' => __('Thursday'), 'utcOffset' => 0]
    ],
    [
        [
            'active' => 1,
            'working_days' => '1,2,3,4,5',
            'holidays' => '14.03.2018, 25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 0,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521126600,
            'utc_offset' => 0
        ],
        ['day' => 'tomorrow', 'time' => 1521212400, 'deliveryDay' => __('Friday'), 'utcOffset' => 0]
    ],
    [
        [
            'active' => 1,
            'working_days' => '1,2,3,4,5,7',
            'holidays' => '14.03.2018, 25.03.2018',
            'working_hours' => 10,
            'order_queue_length' => 0,
            'delivery_today_time' => '15:00',
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        ['day' => 'today', 'time' => 1521212400, 'deliveryDay' => __('Friday'), 'utcOffset' => 0]
    ]
];