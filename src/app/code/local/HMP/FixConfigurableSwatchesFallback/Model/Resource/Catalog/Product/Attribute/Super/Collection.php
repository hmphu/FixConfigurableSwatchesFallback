<?php

class HMP_FixConfigurableSwatchesFallback_Model_Resource_Catalog_Product_Attribute_Super_Collection extends Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Attribute_Super_Collection{
	/**
	 * Load attribute option labels for current store and default (fallback)
	 *
	 * @return $this
	 */
	protected function _loadOptionLabels()
	{
	    if ($this->count()) {
	        $labels = $this->_getOptionLabels();
	        foreach ($this->getItems() as $item) {
	            $item->setOptionLabels($labels);
	        }
	    }
	    return $this;
	}

	/**
	 * Get Option Labels
	 *
	 * @return array
	 */
	protected function _getOptionLabels()
	{
	    $attributeIds = $this->_getAttributeIds();

	    $select = $this->getConnection()->select();
	    $select->from(array('options' => $this->getTable('eav/attribute_option')))
	        ->join(
	            array('labels' => $this->getTable('eav/attribute_option_value')),
	            'labels.option_id = options.option_id',
	            array(
	                'label' => 'labels.value',
	                'store_id' => 'labels.store_id',
	            )
	        )
	        ->where('options.attribute_id IN (?)', $attributeIds)
	        ->where(
	            'labels.store_id IN (?)',
	            array(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID, $this->getStoreId())
	        );

	    $resultSet = $this->getConnection()->query($select);
	    $labels = array();
	    while ($option = $resultSet->fetch()) {
	        $labels[$option['option_id']][$option['store_id']] = $option['label'];
	    }
	    return $labels;
	}

	/**
	 * Get Attribute IDs
	 *
	 * @return array
	 */
	protected function _getAttributeIds()
	{
	    $attributeIds = array();
	    foreach ($this->getItems() as $item) {
	        $attributeIds[] = $item->getAttributeId();
	    }
	    $attributeIds = array_unique($attributeIds);

	    return $attributeIds;
	}
}