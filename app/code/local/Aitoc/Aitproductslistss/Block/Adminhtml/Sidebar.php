<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/Sidebar.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ amejBgrekphghUcr('8dc3a422400f47a9c98662e28c57c46b'); ?><?php

class Aitoc_Aitproductslists_Block_Adminhtml_Sidebar extends Aitoc_Aitproductslists_Block_Adminhtml_Abstract
{
    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'aitproductslists_sidebar';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('aitproductslists_sidebar');
        $this->setDataId('aitproductslists_sidebar');
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
            $collection = Mage::getModel('aitproductslists/list')->getCollection()->getSidebarCollection($this->getCustomerId());//getCollectionByCustomer();
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