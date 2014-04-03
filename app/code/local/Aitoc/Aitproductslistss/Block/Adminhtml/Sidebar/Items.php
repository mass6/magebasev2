<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/Sidebar/Items.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('9d4e7cb7a9a151cae4f48b9a04559631'); ?><?php

class Aitoc_Aitproductslists_Block_Adminhtml_Sidebar_Items extends Aitoc_Aitproductslists_Block_Adminhtml_Abstract
{
    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'aitproductslists_add';

    protected function _construct()
    {
        parent::_construct();
        $this->setListId($this->getRequest()->getParam('list_id'));
        $this->setId('aitproductslists_sidebar_items');
        $this->setDataId('aitproductslists_sidebar_items');
    }

    public function getHeaderText()
    {
        return Mage::helper('aitproductslists')->__('Shopping Cart');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        if (is_null($collection)) {
            $list = Mage::getModel('aitproductslists/list')->load($this->getListId());
            $collection = $list->getQuote()->getItemsCollection()->addFieldToFilter('parent_item_id', array('null' => true));
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }
    public function getItems()
    {
        return $this->getItemCollection()->getItems();
    }
    /**
     * Retrieve identifier of block item
     *
     * @param Varien_Object $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier linked with item
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  int
     */
    public function getProductId($item)
    {
        return $item->getProduct()->getId();
    }
    
} } 