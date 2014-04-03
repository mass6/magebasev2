<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Adminhtml/Quote/Session.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ amejBgrekphghUcr('48c0c7b1fe65274a516f3e097672cfc8'); ?><?php
class Aitoc_Aitproductslists_Model_Adminhtml_Quote_Session extends Mage_Adminhtml_Model_Session_Quote
{
    public function __construct()
    {
        $this->init('adminhtml_quote_aitppl');
        if (Mage::app()->isSingleStoreMode()) {
            $this->setStoreId(Mage::app()->getStore(true)->getId());
        }
    }

    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('aitproductslists/quote');
            if ($this->getStoreId() && $this->getQuoteId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->load($this->getQuoteId());
            }
            elseif($this->getStoreId() && $this->hasCustomerId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->setCustomerGroupId(Mage::getStoreConfig(self::XML_PATH_DEFAULT_CREATEACCOUNT_GROUP))
                    ->assignCustomer($this->getCustomer())
                    ->setIsActive(false)
                    ->save();
                $this->setQuoteId($this->_quote->getId());
            }
            $this->_quote->setIgnoreOldQty(true);
            $this->_quote->setIsSuperMode(true);
        }
        return $this->_quote;
    }
} } 