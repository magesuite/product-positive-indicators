<?php

namespace MageSuite\ProductPositiveIndicators\ViewModel\PopularIndicator;

class TileBadgeShower implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected \Magento\Framework\Registry $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }
    public function isCurrentPageCategory() {
        return $this->getCurrentCategory() != null;
    }

    public function getCurrentCategoryPageId() {
        $category = $this->getCurrentCategory();

        if(!$category) {
            return null;
        }

        return $category->getId();
    }

    /**
     * @return mixed|null
     */
    protected function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
}
