<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Quote.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('8f47c60c55bd9e95661a097e9e9d992f'); ?><?php
class Aitoc_Aitproductslists_Model_Quote extends Mage_Sales_Model_Quote
{
    protected $_eventPrefix = 'aitproductslists_quote';
    protected $_countItems = 0;

    /**
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_items = null;

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('aitproductslists/quote');
    }
    
     /**
     * Retrieve quote items collection
     *
     * @param   bool $loaded
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getItemsCollection($useCache = true)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getModel('aitproductslists/quote_item')->getCollection();
            $this->_items->setQuote($this);
        }
        return $this->_items;
    }
    
    public function isCurrencyDifferent()
    {
       return $this->getQuoteCurrencyCode() != $this->getBaseCurrencyCode();
    }
    
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getQuoteCurrency()->formatPrecision($price, $precision, array(), true, $addBrackets);
    }
    public function getQuoteCurrency()
    {
        return Mage::getModel('directory/currency')->load($this->getQuoteCurrencyCode());
    }
    
    public function applyBaseData()
    {
        if(!Mage::app()->getStore()->isAdmin())
        {
            $customerSession = Mage::getSingleton('customer/session');
            $this->setCustomer($customerSession->getCustomer());
            if ($remoteAddr = Mage::helper('core/http')->getRemoteAddr())
            {
                $this->setRemoteIp($remoteAddr);
                $xForwardIp = Mage::app()->getRequest()->getServer('HTTP_X_FORWARDED_FOR');
                $this->setXForwardedFor($xForwardIp);
            }
            $this->setIsActive(0);
        }
        $this->getBillingAddress();
        $this->getShippingAddress()->setCollectShippingRates(true);
        return $this;
    }
    
    protected function _beforeSave()
    {
        if(!$this->hasStoreId())
        {
            if(!Mage::app()->getStore()->isAdmin())
            {
                $this->setStoreId(Mage::app()->getStore()->getId());
            } 
        }
    }
    
    /**
     * Adding new item to quote
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Sales_Model_Quote
     */
    public function addItem(Mage_Sales_Model_Quote_Item $item)
    {
        $newItem = parent::addItem($item);
       // echo 6457645; exit;
        Mage::getModel('aitproductslists/list_item')->addFromController($newItem->getId());
        return $newItem;
    }
    
    /**
     * Merge quotes
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote
     */
    public function merge(Mage_Sales_Model_Quote $quote)
    {
       return parent::merge($quote);
    }
} } 