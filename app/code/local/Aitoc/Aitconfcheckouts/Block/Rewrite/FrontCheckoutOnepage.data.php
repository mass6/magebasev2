<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Block/Rewrite/FrontCheckoutOnepage.data.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ hCcogqrgrwDUZhir('f01a89bec602ff14acc13a309018353c'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepage extends Mage_Checkout_Block_Onepage
{
    protected $_sShippingMethod         = '';
    
    public function getSteps()
    {
        $steps = array();

        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getCheckout()->getStepData('login');
        }
        
        $stepCodes = $this->getStepHash();

////         START AITOC CONFIGURABLE CHECKOUT CODE 

        $aDisabledSectionHash = $this->getDisabledSectionHash(); 

        $stepCodes = array_diff($stepCodes, $aDisabledSectionHash);       
    
////         FINISH AITOC CONFIGURABLE CHECKOUT CODE 

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }

        return $steps;
    }
    
    
    public function getStartSteps()
    {
        $originalCodes = $this->getStepHash();

        $aDisabledSectionHash = $this->getDisabledSectionHash(); 

        $aStartSteps = array();
     
        foreach ($originalCodes as $iKey => $sStep)
        {
            if (in_array($sStep, $aDisabledSectionHash))
            {
                $aStartSteps[] = $originalCodes[$iKey - 1];
            }
        }
        
        return $aStartSteps;
    }
    
    
    public function getShippingMethodStrict()
    {
        return mage::registry('sShippingMethodStrict');
    }
    
    public function getSubstituteCode()
    {
        return Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode();
    }
    
    public function getDisabledSectionHash()
    {
        return Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($this->getQuote());
    }    
    
    public function getStepHash()
    {
        $originalCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'review');

        if ($this->getQuote()->isVirtual())
        {
            $originalCodes = array('billing', 'payment', 'review');
        }
        
        return $originalCodes;
    }    
    
    public function checkIsVirtual()
    {
        if ($this->getQuote()->isVirtual())
        {
            return true;
        }
        else 
        {
            return false;
        }
    }    
    
} } 