<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Catalog/Product/Listform.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('29ebfa363b8267d30d1c0eab01b233e4'); ?><?php
class Aitoc_Aitproductslists_Block_Catalog_Product_Listform extends Mage_Catalog_Block_Product_Abstract
{
    private function _getList()
    {
        return Mage::getModel('aitproductslists/list');
    }
    
    public function getQty()
    {
        if ($this->getRequest()->getParam('qty'))
        {
            return (int) $this->getRequest()->getParam('qty');
        }
        return 1;
    }
    
    public function getUpdateList()
    {
        $id = false;
        if ($this->getRequest()->getParam('list_id'))
        {
            $id = $this->getRequest()->getParam('list_id');
        }
       
        return $id;
    }
    
    public function getCurrentList()
    {
        $id = false;
        $id = Mage::getSingleton('aitproductslists/session')->getCurrentListId();
        return $id;
    }
    public function getProductId()
    { 
        $product = Mage::helper('aitproductslists')->getProduct();
        return $product->getId();
    }
    
    public function getSubmitListUrl()
    {     
        if ($this->getUpdateList())
        {
            return $this->getUrl('aitproductslists/list/updateItemOptions',array('list_id'=>$this->getRequest()->getParam('list_id'),'item_id'=>$this->getRequest()->getParam('id')));      
        }
        return $this->getUrl('aitproductslists/list/addProductToList');
    }
   
    public function getCustomerLists()
    {
       if ($this->_getCustomerId())
       {
           return $this->_getList()->getCollectionByCustomer($this->_getCustomerId());
       }
       $listIds = Mage::getSingleton('aitproductslists/session')->getNonLoginListIds();

       return $this->_getList()->loadByIds($listIds);
    }
    
    protected function _getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomerId();
    }
    
    public function getProductType()
    {
       return  $this->getProduct()->getTypeId();
    }
    
    public function getButtonLabel()
    {
        return Mage::helper('aitproductslists')->getButtonLabel();
    }
    public function canDiscount()
    {
        $list = Mage::getModel('aitproductslists/list')->load($this->getRequest()->getParam('list_id'));
        if (!$list->getDiscount()->getPrice())
        {
            return 0;
        }
        if ($list->getDiscount()->getPrice()==0)
        {
            return 0;    
        }
        if ($list->getPayQty() < $list->getDiscount()->getMinQty())
        {
            return 0;    
        }
        if ($list->getDiscount()->getToDate())
        {
            $dateFrom = Mage::getModel('core/date')->timestamp($list->getDiscount()->getFromDate());
            $dateTo = Mage::getModel('core/date')->timestamp($list->getDiscount()->getToDate());
            $dateNow = Mage::getModel('core/date')->timestamp(time());
            if ($dateFrom <= $dateNow AND $dateNow <= $dateTo)
            {
                 return 1;        
            }
            else
            {
                return 0;
            }
        }
        return 1;        
    }
} } 