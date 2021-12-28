<?php

namespace MageSuite\ProductPositiveIndicators\Block\Adminhtml\Product\Attribute\Edit\Tab;

class AddTopAttribute extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesNo;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker
     */
    private $propertyLocker;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    const DEFAULT_STORE_INDEX = 0;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        $this->propertyLocker = $propertyLocker;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'top_attribute_fieldset',
            ['legend' => __('Product Positive Top Attribute Indicators'), 'collapsable' => false]
        );

        $fieldset->addField(
            'top_attribute_enabled',
            'select',
            [
                'name' => 'top_attribute_enabled',
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'values' => $this->yesNo->toOptionArray()
            ]
        );

        $fieldset->addField(
            'top_attribute_sign',
            'select',
            [
                'name' => 'top_attribute_sign',
                'label' => __('Sign'),
                'title' => __('Sign'),
                'note' => __(
                    "Sign from this field will be used as parser instruction for comparison." .
                    "<br>If the selected sign is %, the minimum value of the top attribute will be calculated according to the formula: maxValue - x% * (maxValue - minValue)." .
                    "<br>In case the selected sign is % and attribute type is text, the minimum value of the top attribute will be calculated by the cron job at night."
                ),
                'values' => [
                    ['value' => '>', 'label' => __('> greater than')],
                    ['value' => '>=', 'label' => __('>= greater than or equal')],
                    ['value' => '<', 'label' => __('< less than')],
                    ['value' => '<=', 'label' => __('<= less than or equal')],
                    ['value' => '=', 'label' => __('= equal, exact comparison')],
                    ['value' => '[]', 'label' => __('[] equal to one of many values, separated by a comma')],
                    ['value' => '%', 'label' => __('% in top best x%, use for numeric values')]
                ]
            ]
        );

        try {
            $topAttributeValue = $this->serializer->unserialize($this->getAttributeObject()->getData('top_attribute_value'));
        } catch (\Exception $e) {
            $topAttributeValue = [];
        }

        try {
            $topAttributeMinValue = $this->serializer->unserialize($this->getAttributeObject()->getData('top_attribute_min_value'));
        } catch (\Exception $e) {
            $topAttributeMinValue = [];
        }

        $stores = $this->getStores();
        foreach ($stores as $storeId => $storeName) {
            $labelValue = $storeId == self::DEFAULT_STORE_INDEX ?
                'Default value' :
                'Value for ' . $storeName;
            $noteValue = $storeId == self::DEFAULT_STORE_INDEX ?
                'The value from this field will be used by the parser. It will be used as default in case the value for story is empty.' :
                'The value from this field will be used by the parser for ' . $storeName;
            $fieldset->addField(
                'top_attribute_value_' . $storeId,
                'text',
                [
                    'name' => 'top_attribute_value[' . $storeId . ']',
                    'label' => __($labelValue),
                    'title' => __($labelValue),
                    'note' => __($noteValue),
                    'value' => isset($topAttributeValue[$storeId]) ? $topAttributeValue[$storeId] : ""
                ]
            );

            $labelMinValue = $storeId == self::DEFAULT_STORE_INDEX ?
                'Default minimum value' :
                'Minimum value for ' . $storeName;
            $fieldset->addField(
                'top_attribute_min_value_' . $storeId,
                'text',
                [
                    'name' => 'top_attribute_min_value[' . $storeId . ']',
                    'label' => __($labelMinValue),
                    'title' => __($labelMinValue),
                    'class' => 'top_attribute_min_value',
                    'note' => __(
                        'Minimum value to show a Top Indicator. Value in this field is read only and will be calculated automatically in case of % sign chosen.'
                    ),
                    'readonly' => true,
                    'disabled' => true,
                    'value' => isset($topAttributeMinValue[$storeId]) ? $topAttributeMinValue[$storeId] : ""
                ]
            );
        }

        $this->setForm($form);
        $this->propertyLocker->lock($form);

        return $this;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return mixed
     */
    private function getAttributeObject()
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }

    public function getStores()
    {
        $stores = [self::DEFAULT_STORE_INDEX => "Default"];
        foreach ($this->storeManager->getStores() as $store) {
            $stores[$store->getId()] = $store->getName();
        }
        return $stores;
    }

    /**
     * Initialize form fields values
     *
     * @return $this
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }
}
