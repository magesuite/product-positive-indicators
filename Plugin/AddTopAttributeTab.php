<?php

namespace MageSuite\ProductPositiveIndicators\Plugin;

class AddTopAttributeTab
{
    private $added = false;

    /**
     * @param \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tabs $subject
     * @param $tabId
     * @param $tab
     * @return array
     * @throws \Exception
     */
    public function beforeAddTab(\Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tabs $subject, $tabId, $tab) {

        if(! $this->added) {
            $this->added = true;

            $subject->addTab(
                'add_top_attribute_tab',
                [
                    'label' => __('Manage Top Attribute'),
                    'title' => __('Manage Top Attribute'),
                    'content' => $subject->getChildHtml('add_top_attribute_tab'),
                    'after' => 'labels'
                ]
            );
        }

        return [$tabId, $tab];
    }
}