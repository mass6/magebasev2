<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Helper/List.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('ba4ae46bce4aa66467c298d1b73a3077'); ?><?php
class Aitoc_Aitproductslists_Helper_List extends Mage_Core_Helper_Url
{
    protected $_reminderPeriods = array(
                  1=>'Day',  
                  2=>'Week',  
                  3=>'Two Weeks',  
                  4=>'Month',  
                  5=>'Year',  
                );
//    
//    protected $_reminderFrequencies = array(
//                  1=>'Daily',  
//                  2=>'Weekly',  
//                  3=>'Monthly',  
//                  4=>'Quarterly',  
//                  5=>'Yearly',  
//                );
    
    protected $_shedulePeriod = array(
                  1=>'days',  
                  2=>'weeks',  
                  3=>'half-month',  
                  4=>'months',  
                  5=>'years', 
                );
//    
//    protected $_sheduleFrequency = array(
//                  1=>'1 day',  
//                  2=>'1 week', 
//                  4=>'1 month', 
//                  4=>'3 months', 
//                  5=>'1 year', 
//                );
    public function getReminderPeriods()
    {
        return $this->_reminderPeriods;
    }
    public function getShedulePeriods()
    {
        return $this->_shedulePeriod;
    }
    /**
     * Retrieve url for add product to cart
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  string
     */
    public function getAddUrl($product, $additional = array())
    {
//          return $product->getProductUrl();
        $continueUrl    = Mage::helper('core')->urlEncode($this->getCurrentUrl());
        $urlParamName   = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;

        $routeParams = array(
            $urlParamName   => $continueUrl,
            'product'       => $product->getEntityId()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'aitproductslists'
            && $this->_getRequest()->getControllerName() == 'list') {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('aitproductslists/list/add', $routeParams);
    }
    
    public function getAccountListUrl()
    {
        return $this->_getUrl('aitproductslists/list/grid');
    }
    public function getCurrentListUrl()
    {
        return $this->_getUrl('aitproductslists/list/view',array("list_id"=>Mage::getSingleton('aitproductslists/session')->getCurrentListId()));
    }
    
    public function needConfirm()
    {
        if(!Mage::getSingleton('aitproductslists/session')->getCurrentListId())
        {
            return false;
        }
        $currentId = Mage::getSingleton('aitproductslists/session')->getCurrentListId();
        $list = Mage::getModel('aitproductslists/list')->load($currentId);
        return $list->getDiscount()->canDiscount();
    }
} } 