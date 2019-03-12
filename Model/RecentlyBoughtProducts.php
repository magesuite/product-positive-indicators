<?php

namespace MageSuite\ProductPositiveIndicators\Model;

class RecentlyBoughtProducts
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected $productResourceAction;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\RecentlyBought
     */
    protected $configuration;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productResourceAction,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\RecentlyBought $configuration
    )
    {
        $this->scopeConfig = $scopeConfigInterface;
        $this->productResourceAction = $productResourceAction;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->configuration = $configuration;
    }

    public function execute()
    {
        $this->removeRecentlyBoughtFlag();

        if(!$this->configuration->isEnabled() or !$this->configuration->getPeriod() or !$this->configuration->getMinimal()){
            return false;
        }

        $productsData = $this->getProductsData();

        if(empty($productsData)){
            return false;
        }

        $this->addRecentlyBoughtFlagToProducts($productsData);
    }

    public function getProductsData()
    {
        $products = $this->getProductCollection();

        $from = date('Y-m-d 00:00:00', strtotime('-' . $this->configuration->getPeriod() . ' days'));
        $to = date('Y-m-d 23:59:59', strtotime('-1 day'));
        $minimalValue = $this->configuration->getMinimal();

        $productsData = [];

        foreach($products->getItems() AS $product){

            $recentlyBoughtPeriod = $product->getRecentlyBoughtPeriod();
            $recentlyBoughtMinimal = $product->getRecentlyBoughtMinimal();

            $productFrom = $recentlyBoughtPeriod === null ? $from : date('Y-m-d 00:00:00', strtotime('-' . $recentlyBoughtPeriod . ' days'));
            $productMinimalValue = $recentlyBoughtMinimal === null ? $minimalValue : $recentlyBoughtMinimal;

            $productId = $product->getId();

            $recentlyBoughtSum = $this->getRecentlyBoughtSum($productId, $productFrom, $to);

            if(!$recentlyBoughtSum or $recentlyBoughtSum < $productMinimalValue){
                continue;
            }

            $productsData[$productId] = $recentlyBoughtSum;
        }

        return $productsData;
    }

    protected function getRecentlyBoughtSum($productId, $from, $to)
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->from(
                $this->resourceConnection->getTableName('sales_order_item'),
                ['product_id']
            )
            ->columns('COUNT(`sales_order_item`.product_id) AS count_ordered')
            ->where('product_id = ?', $productId)
            ->where('created_at >= "' . $from . '"')
            ->where('created_at <= "' . $to . '"');

        $result = $connection->fetchRow($query);

        if(!$result or !$result['count_ordered']){
            return null;
        }

        return (int)$result['count_ordered'];
    }

    protected function addRecentlyBoughtFlagToProducts($productIds)
    {
        foreach($productIds AS $productId => $sum){
            $this->productResourceAction->updateAttributes(
                [$productId],
                ['recently_bought' => 1],
                0
            );

            $this->productResourceAction->updateAttributes(
                [$productId],
                ['recently_bought_sum' => $sum],
                0
            );
        }

        return true;
    }

    protected function removeRecentlyBoughtFlag()
    {
        $products = $this->getProductsWithFlag();

        if(empty($products)){
            return true;
        }

        foreach($products AS $product){
            $this->productResourceAction->updateAttributes(
                [$product->getId()],
                ['recently_bought' => 0],
                0
            );

            $this->productResourceAction->updateAttributes(
                [$product->getId()],
                ['recently_bought_sum' => 0],
                0
            );
        }

        return true;
    }

    private function getProductsWithFlag()
    {
        $collection = $this->getProductCollection();

        $collection->addFieldToFilter('recently_bought', 1);

        return $collection->getSize() ? $collection->getItems() : [];
    }

    private function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect([
            'recently_bought',
            'recently_bought_period',
            'recently_bought_minimal'
        ]);

        return $collection;
    }
}