<?php
$scenarios = [
    [
        [
            'working_days' => [1,2,3,4,5],
            'holidays' => ['14.03.2018', '19.03.2018'],
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        'simple_product',
        ['deliveryDayName' => 'Wednesday', 'deliveryNextDayName' => 'Thursday']
    ],
    [
        [
            'working_days' => [1,2,3,4,5],
            'holidays' => ['14.03.2018', '19.03.2018'],
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521374400,
            'utc_offset' => 0
        ],
        'simple_product',
        ['deliveryDayName' => 'Wednesday', 'deliveryNextDayName' => 'Thursday']
    ],
    [
        [
            'working_days' => [1,2,3,4,5],
            'holidays' => ['21.03.2018', '22.03.2018', '25.03.2018'],
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521374400,
            'utc_offset' => 0
        ],
        'simple_product',
        ['deliveryDayName' => 'Tuesday', 'deliveryNextDayName' => 'Friday']
    ],
    [
        [
            'working_days' => [1,2,3,4,5],
            'holidays' => ['22.03.2018', '23.03.2018', '25.03.2018'],
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521374400,
            'utc_offset' => 0
        ],
        'out_of_stock',
        null
    ],
    [
        [
            'working_days' => [1,2,3,4,5],
            'holidays' => ['14.03.2018', '19.03.2018'],
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ],
        'custom_product',
        ['deliveryDayName' => 'Friday', 'deliveryNextDayName' => 'Monday']
    ],
];