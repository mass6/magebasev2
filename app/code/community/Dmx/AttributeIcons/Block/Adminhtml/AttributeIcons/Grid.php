<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Dmx_AttributeIcons_Block_Adminhtml_AttributeIcons_Grid extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid{
	
	protected function _prepareCollection(){
		$collection = Mage::getResourceModel('attributeicons/catalog_attribute_collection');
		/* @var $collection Dmx_AttributeIcons_Model_Resource_Catalog_Attribute_Collection */
		$collection->addVisibleFilter()->addFrontendInputTypeFilter(array('select','multiselect','boolean'))
					->addFieldToFilter('is_user_defined',array('eq'=>1));
		$this->setCollection($collection);

		return $this;
	}
}