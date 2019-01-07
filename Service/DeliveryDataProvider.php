<?php

namespace MageSuite\ProductPositiveIndicators\Service;

class DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    private $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ){
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    public function prepareDeliveryData($config, $format = 'd.m.Y H:i:s', $specificDate = null)
    {
        $utcOffset = (int)$this->dateTime->getGmtOffset();
        $holidays = $config['holidays'] ?? null;

        $this->config = [
            'working_days' => $this->prepareDataFromConfiguration($config['working_days']),
            'holidays' => $this->prepareDataFromConfiguration($holidays),
            'working_hours' => $this->prepareDataFromConfiguration($config['working_hours'], 'hours'),
            'order_queue_length' => $this->prepareDataFromConfiguration($config['order_queue_length'], 'hours'),
            'delivery_today_time' => $config['delivery_today_time']
        ];

        $timestamp = $specificDate ? strtotime($specificDate) : $this->localeDate->scopeTimeStamp();

        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($timestamp);

        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $this->config['delivery_today_time']);

        $businessDay = $this->isBusinessDay($currentDateTime);

        if($businessDay and ($currentDateTime->getTimestamp() + $this->config['order_queue_length']) < $maxTimeToday->getTimestamp()){
            return [
                'day' => 'today',
                'time' => ($maxTimeToday->getTimestamp() - $this->config['order_queue_length']) - $utcOffset,
                'deliveryDay' => __($maxTimeToday->format('l')),
                'utcOffset' => $utcOffset
            ];
        }

        $timeLeft = $this->config['order_queue_length'] - ($maxTimeToday->getTimestamp() - $currentDateTime->getTimestamp());

        $deliveryDayAndNextDay = $this->getDeliveryDayAndNextDay($currentDateTime, $timeLeft);

        $deliveryDay = $deliveryDayAndNextDay['deliveryDay'];
        $dayType = $deliveryDay->format('d') == $deliveryDayAndNextDay['nextDay'] ? 'tomorrow' : 'other';

        $time = $deliveryDay->getTimestamp();

        if($dayType == 'tomorrow'){
            $deliveryDay = new \DateTime($deliveryDay->format('d.m.Y') . ' ' . $this->config['delivery_today_time']);
            $time  = $deliveryDay->getTimestamp() - $utcOffset;

        }

        return [
            'day' => $dayType,
            'time' => $time,
            'deliveryDay' => __($deliveryDay->format('l')),
            'utcOffset' => $utcOffset
        ];

    }

    protected function isBusinessDay($currentDay)
    {
        if(in_array($currentDay->format('N'), $this->config['working_days']) and !in_array($currentDay->format('d.m.Y'), $this->config['holidays'])){
            return true;
        }

        return false;
    }

    protected function getDeliveryDayAndNextDay($currentTime, $timeLeft)
    {
        $nextBusinessDay = false;
        $nextDay = null;

        while (!$nextBusinessDay) {
            $currentTime->modify('+1 day');

            if(!$nextDay){
                $nextDay = $currentTime->format('d');
            }

            if (!in_array($currentTime->format('N'), $this->config['working_days'])){
                continue;
            }

            if (in_array($currentTime->format('d.m.Y'), $this->config['holidays'])){
                continue;
            }

            $timeLeft = $timeLeft - $this->config['working_hours'];

            if($timeLeft > 0){
                continue;
            }

            $nextBusinessDay = true;
        }

        return ['deliveryDay' => $currentTime, 'nextDay' => $nextDay];
    }

    private function prepareDataFromConfiguration($data, $type = 'array')
    {
        if($type == 'hours'){
            return $data * 3600;
        }

        $array = explode(',', $data);

        return array_map('trim', $array);
    }
}