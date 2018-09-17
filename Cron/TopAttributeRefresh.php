<?php

namespace MageSuite\ProductPositiveIndicators\Cron;


class TopAttributeRefresh
{
    const DEFAULT_STORE_INDEX = 0;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    private $attributeResource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $productAttributeCollectionFactory;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface
     */
    private $parser;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $attributeResource
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory
     * @param \MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface $parser
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $attributeResource,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface $parser,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    )
    {
        $this->attributeResource = $attributeResource;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->parser = $parser;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute()
    {
        // get all attributes with type text or text area that are enabled and has % as sign
        $attributeCollection = $this->productAttributeCollectionFactory->create()
            ->addFieldToFilter('frontend_input', ['in' => ['text', 'textarea', 'price']])
            ->addFieldToFilter('top_attribute_enabled', 1)
            ->addFieldToFilter('top_attribute_sign', '%')
            ->load();

        foreach ($attributeCollection as $attribute) {
            $this->calculateMinValueForTextAttribute($attribute);
        }
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function calculateMinValueForTextAttribute(\Magento\Eav\Model\Entity\Attribute $attribute){
        try {
            $topAttributeValues = $this->serializer->unserialize($attribute->getTopAttributeValue());
            $productAttributeValues = $this->getProductAttributeValues($attribute->getAttributeId(), $attribute->getBackendTable());
            $topAttributeMinValue = $this->parser->calculateTopAttributeMinValue($topAttributeValues, $productAttributeValues);
        } catch (\Exception $e) {
            $topAttributeMinValue = [];
        }
        $attribute->setTopAttributeMinValue($this->serializer->serialize($topAttributeMinValue));
        $this->attributeResource->save($attribute);
    }

    /**
     * @param int $attributeId
     * @param string $attributeTable
     * @return array
     */
    public function getProductAttributeValues($attributeId, $attributeTable)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['att' => $attributeTable], [
                'entity_id',
                'store_id',
                'value'
            ])
            ->where('att.attribute_id = ?', $attributeId)
            // remove duplicated values by group
            ->group(['store_id', 'value']);;

        $result = $connection->fetchAll($select);
        $formattedResult = $this->reindexResult($result);
        $completedResult = $this->fillMissingValues($formattedResult);
        return $completedResult;
    }

    /**
     * Format values to table indexed by entity and store
     *
     * @param array $result
     * @return array
     */
    public function reindexResult($result)
    {
        $formattedResult = [];

        foreach ($result as $row) {
            $formattedResult[$row['entity_id']][$row['store_id']] = $row['value'];
        }

        return $formattedResult;
    }

    /**
     * Fill missing attribute values with default ones
     * and check if value for store is set if not set as empty string
     *
     * @param array $formattedResult
     * @return array
     */
    public function fillMissingValues($formattedResult) {
        $response = [];
        $storeIds = $this->getStoreIds();

        foreach ($formattedResult as $ie => $entity) {
            foreach ($storeIds as $storeId) {
                $defaultValue = isset($entity[self::DEFAULT_STORE_INDEX]) ? $entity[self::DEFAULT_STORE_INDEX] : "";
                $response[$storeId][] = isset($entity[$storeId]) ? $entity[$storeId] : $defaultValue;
            }
        }

        return $response;
    }

    public function getStoreIds()
    {
        $storeIds = [self::DEFAULT_STORE_INDEX];
        foreach ($this->storeManager->getStores() as $store) {
            array_push($storeIds, $store->getId());
        }

        return $storeIds;
    }

}
