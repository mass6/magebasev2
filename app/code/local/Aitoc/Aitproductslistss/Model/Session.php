<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Session.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ yBZaDjmZMwojoQTP('1388d8c30177a8afdd335002d1015b68'); ?><?php

class Aitoc_Aitproductslists_Model_Session extends Mage_Core_Model_Session_Abstract
{
     /**
     * Class constructor. Initialize PPL session namespace
     */
    public function __construct()
    {
        $namespace = 'aitproductslists';
          if ($this->getCustomerConfigShare()->isWebsiteScope()) {
            $namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());
        }

        $this->init($namespace);
    }
    
    public function getCustomerConfigShare()
    {
        return Mage::getSingleton('customer/config_share');
    }
    
    public function getNonLoginListIds()
    {
        
        return $this->getData('nonLoginListIds');
    }
    
    public function setNonLoginListId($listId)
    {
        $data = $this->getNonLoginListIds();
        $data[] = (int) $listId;
        return $this->setData('nonLoginListIds',$data);
    }
    
    protected function _getQuoteIdKey()
    {
        return 'quote_id_' . Mage::app()->getStore()->getWebsiteId();
    }

    public function setQuoteId($quoteId)
    {
        $this->setData($this->_getQuoteIdKey(), $quoteId);
    }

    public function getQuoteId()
    {
        return $this->getData($this->_getQuoteIdKey());
    }
    public function getCurrentListId()
    {
        return $this->getData('currentListId');
    }
    public function setCurrentListId($listId)
    {
        return $this->setData('currentListId',$listId);
    }
} } 