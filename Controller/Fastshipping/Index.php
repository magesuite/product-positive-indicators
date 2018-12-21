<?php

namespace MageSuite\ProductPositiveIndicators\Controller\Fastshipping;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_LAYOUT);

        return $resultPage;
    }
}