<?php

namespace MageSuite\ProductPositiveIndicators\Service;

class DeliveryDataProvider
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Helper\Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    protected function isWorkingDay(\DateTime $currentDay): bool
    {
        return in_array($currentDay->format('N'), $this->configuration->getWorkingDays());
    }

    protected function isHoliday(\DateTime $currentDay): bool
    {
        return in_array($currentDay->format('d.m.Y'), $this->configuration->getHolidays());
    }

    public function getNumberOfBusinessDays(\DateTime $from, \DateTime $to): int
    {
        $businessDays = 0;

        while ($from < $to) {
            $from->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($from) && !$this->isHoliday($from);

            if ($isBusinessDay) {
                $businessDays++;
            }
        }

        return $businessDays;
    }
}
