<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Block/Rewrite/FrontCheckoutOnepageShipping.data.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ qhCiMpaMahjIDqwa('42b1c12eefd720c756e6426c418048a7'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageShipping extends Mage_Checkout_Block_Onepage_Shipping
{
    
    protected $_configs = array();
    protected $_contryId = null;
    
    protected function _construct()
    {
        $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');        
    
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
    
} } 