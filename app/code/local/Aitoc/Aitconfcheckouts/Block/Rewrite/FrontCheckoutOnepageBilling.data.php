<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Block/Rewrite/FrontCheckoutOnepageBilling.data.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ hQNIaTBaBCmAWhcV('05333a99fc3314e0bbc860999d125b17'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageBilling extends Mage_Checkout_Block_Onepage_Billing
{
    
    protected $_configs = array();
    protected $_contryId = null;
    
    protected function _construct()
    {
        $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('billing');   
        
        foreach ($aAllowedFieldHash as $sKey => $bValue)
        {
            $this->_configs[$sKey] = $bValue;
        }
        
        parent::_construct();
    }

    public function getDefaultCountryId()
    {
        return Mage::helper('aitconfcheckout')->getDefaultCountryId();
    }    
    
    public function checkFieldShow($sKey)
    {
        if (!$sKey OR !isset($this->_configs[$sKey])) return false;
        
        if ($this->_configs[$sKey])
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
    
    public function checkStepActive($sStepCode)
    {
        $aDisabledSectionHash = $this->getDisabledSectionHash();
        
        if ($aDisabledSectionHash AND in_array($sStepCode, $aDisabledSectionHash))
        {
            return false;
        }
        else 
        {
            return true;
        }
    }

    public function getDisabledSectionHash()
    {
        return Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($this->getQuote());
    }      
    
    public function checkSkipShippingAllowed()
    {
        $aAllowedBillingHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('billing');        
        $aAllowedShipingHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');        
        
        $aRequiredHash = array('address', 'city', 'region', 'country', 'postcode', 'telephone');
        
        foreach ($aAllowedShipingHash as $sKey => $bFieldActive)
        {
            if ($bFieldActive AND in_array($sKey, $aRequiredHash) AND !$aAllowedBillingHash[$sKey])
            {
                return false;
            }
        }
        
        return true;
    }      
    

    // override parent
    public function getAddressesHtmlSelect($type)
    {
        $sHtml = parent::getAddressesHtmlSelect($type);
        
        if ($sHtml)
        {
            for ($i=1;$i<=10; $i++)
            {
                $sHtml = str_replace(array(', , ', ' , 
                        </option>', ', </option>', ' , ', ',,'), array(', ', '</option>', '</option>', ', ', ','), $sHtml);   
            }        
        }
        
        return $sHtml;
    }

    // override parent
    function getAddress() {
        
        $address = parent::getAddress();

        if ($address AND $data = $address->getData())
        {
            foreach ($data as $sKey => $mVal)
            {
                if ($mVal == Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode())
                {
                    $data[$sKey] = '';
                }
            }
            
            $address->addData($data);
        }
        
        return $address;
    }
    
    // override from another module
    public function checkStepHasRequired()
    {
        return '';
    }
    
       
} } 