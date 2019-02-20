<?php
$scenarios = [
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '14.03.2018, 19.03.2018',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2
        ],
        '16.03.2018 12:00',
        'simple_product',
        ['deliveryDayName' => 'Wednesday', 'deliveryNextDayName' => 'Thursday']
    ],
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '14.03.2018, 19.03.2018',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2
        ],
        '18.03.2018 12:00',
        'simple_product',
        ['deliveryDayName' => 'Wednesday', 'deliveryNextDayName' => 'Thursday']
    ],
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '21.03.2018, 22.03.2018, 25.03.2018',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2
        ],
        '18.03.2018 12:00',
        'simple_product',
        ['deliveryDayName' => 'Tuesday', 'deliveryNextDayName' => 'Friday']
    ],
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '22.03.2018, 23.03.2018, 25.03.2018',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2
        ],
        '18.03.2018 12:00',
        'out_of_stock',
        null
    ],
    [
        [
            'working_days' => '1,2,3,4,5',
            'holidays' => '14.03.2018, 19.03.2018',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2
        ],
        '16.03.2018 12:00',
        'custom_product',
        ['deliveryDayName' => 'Friday', 'deliveryNextDayName' => 'Monday']
    ],
];