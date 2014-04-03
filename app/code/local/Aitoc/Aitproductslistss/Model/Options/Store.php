<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Options/Store.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('15660c35e02b04377795e43d285e91cd'); ?><?php
class Aitoc_Aitproductslists_Model_Options_Store
{
    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    public function toOptionArray()
    {
        $stores = array();
        $websites = Mage::getModel('core/website')->getCollection()->addIdFilter($this->getCustomer()->getSharedWebsiteIds());
        $allgroups = Mage::getModel('core/store_group')->getCollection();
        
        foreach ($websites as $website) {
            $values = array();
            $storesCollection = $website->getGroupCollection();
            foreach($storesCollection->getItems() as $store)
            {
                $viewValues = array();
                $storeViewCollection = $store->getStoreCollection();
                foreach($storeViewCollection as $storeView)
                {
                    $viewValues[] = array('label'=>$storeView->getName(), 'value'=>$storeView->getId(), 'style'=>'padding-left:20px;');
                }
                $values[] = array('label'=>'&nbsp;&nbsp;&nbsp;'.$store->getName(),'value'=>$viewValues);
            }
            $stores[] = array('label'=>$website->getName(),'value'=>$values);
        }
        return $stores;
    }
    
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }
    
    public function getCustomer()
    {
        if(!$this->_customer)
        {
            $this->_customer = Mage::getModel('customer/customer');
        }
        return $this->_customer;
    }
} } 