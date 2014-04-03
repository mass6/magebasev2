<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Rewrite/Adminhtml/SalesOrderCreate.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('c441a0044eaa19b4ae98cd33bfbdf461'); ?><?php

class Aitoc_Aitproductslists_Model_Rewrite_Adminhtml_SalesOrderCreate extends Mage_Adminhtml_Model_Sales_Order_Create
{
    
    private $_itemsToRemove = array();
    
    protected function _getListDiscount($list)
    {
        if ($list->checkDiscount())
        {
            return $list->getDiscount()->getPrice();
        }
        return 0;
    }

    /**
     * Handle data sent from sidebar
     *
     * @param array $data
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function applySidebarData($data)
    {
        // add to order full products lists
        if (isset($data['aitproductslists_sidebar'])) {
            foreach ($data['aitproductslists_sidebar'] as $listId => $v) {
                $list = Mage::getModel('aitproductslists/list')->load($listId);
                foreach ($this->_getListItems($listId) as $item)
                {
                    $this->moveQuoteItem($item, 'order', $item->getQty(),$list);
                    $this->_itemsToRemove[] = $item->getId();
                }
            }
        }
        // add to order custom products from products lists
        if (isset($data['aitproductslists_add'])) {
            $list = Mage::getModel('aitproductslists/list')->load($this->_getOpenListId());
            $listQuote = $list->getQuote();
            foreach ($data['aitproductslists_add'] as $itemId => $qty) {
                $item = $listQuote->getItemById($itemId);
                if (!in_array($item->getId(), $this->_itemsToRemove))
                {
                    $this->moveQuoteItem($item, 'order', $qty, $list);
                }
            }
        }
        return parent::applySidebarData($data);
    }
    
    public function moveQuoteItem($item, $moveTo, $qty, $list=null)
    {
        if($list)
        {
            $orderQuoteId = $this->getSession()->getQuote()->getId();
            Mage::getModel('aitproductslists/list_purchase')->addFromAdmin($item,$list->getId(),$orderQuoteId);
        }
        parent::moveQuoteItem($item, $moveTo, $qty);
    }
    
    private function _getListItems($listId)
    {
        $list = Mage::getModel('aitproductslists/list')->load($listId);
        $listQuote = $list->getQuote();
        return $listQuote->getAllVisibleItems();
    }
    
    private function _getOpenListId()
    {
        return Mage::app()->getRequest()->getParam('loaded_list_id');
    }
} } 