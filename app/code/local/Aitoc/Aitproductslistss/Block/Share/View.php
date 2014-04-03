<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Share/View.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('105c4f8d99acb1c9503a8bbe5a537125'); ?><?php
class Aitoc_Aitproductslists_Block_Share_View extends Aitoc_Aitproductslists_Block_Account_List_Items
{
    public function getList()
    {
    	$key = $this->getRequest()->getParam('key');
        return Mage::getModel('aitproductslists/list')->load($key, 'public_key');
    }
        public function getItems2()
    {
        return $this->_items;
    }
    
    public function getCustomerName()
    {
        $customerId = $this->getList()->getData('customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        return $customer->getName();
    }
    
    public function addToListUrl()
    {
        $url = $this->getUrl("aitproductslists/list/addShare",array('list_id'=>$this->getList()->getId(),'customer'=>$this->getCustomerName()));
        return $url;
    }
    
    protected function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('page/html_pager', 'aitproductslists.share.list.items.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        return $this;
    }
    
} } 