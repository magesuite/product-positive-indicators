<?php

namespace MageSuite\ProductPositiveIndicators\Controller\Fastshipping;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);

        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $layout = $this->layoutFactory->create();

        $block = $layout
            ->createBlock('\MageSuite\ProductPositiveIndicators\Block\FastShipping\Product');

        $clearCache = $this->_request->getParam('clear');
        if($clearCache){
            $block->setCacheLifetime(null);
            $block->setClearCache(true);
        }

        $blockHtml = $block->toHtml();

        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($blockHtml);

        $this->getResponse()->setBody($blockHtml);
    }
}