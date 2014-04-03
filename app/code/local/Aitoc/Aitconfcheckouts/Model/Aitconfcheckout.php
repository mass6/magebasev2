<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Model/Aitconfcheckout.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ pqhwmoZmZqecjphZ('f496a9b6f9ceacb0c5902f64c0eb50e0'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitconfcheckout_Model_Aitconfcheckout extends Mage_Eav_Model_Entity_Attribute
{
    public function getDisabledSectionHash($oQuote)
    {
        if (Mage::registry('bDisabledSectionRegistered')) return Mage::registry('aDisabledSectionHash');
        
        $originalCodes = array('shipping', 'shipping_method', 'payment');
        
        if ($oQuote->isVirtual())
        {
            $originalCodes = array('payment');
        }
        
        $aDisabledSectionHash = array();
        
        foreach ($originalCodes as $sStepKey)
        {
            if (!Mage::getStoreConfig('aitconfcheckout/' . $sStepKey . '/active'))
            {
                switch ($sStepKey) 
                {
                	case 'shipping_method':
                	    
                	    $iMethodCount = 0;
                	    
                        if (Mage::getStoreConfig('carriers/flatrate/active'))
                        {
                            $iMethodCount++;
                            
                            if (!Mage::getStoreConfig('carriers/flatrate/sallowspecific'))
                            {
                                $sShippingMethod = 'flatrate';
                            }
                        }

                        if (Mage::getStoreConfig('carriers/freeshipping/active'))
                        {
                            $iMethodCount++;
                            
                            if (!Mage::getStoreConfig('carriers/freeshipping/sallowspecific'))
                            {
                                $sShippingMethod = 'freeshipping';
                            }
                        }

                        if (Mage::getStoreConfig('carriers/tablerate/active'))
                        {
                            $iMethodCount = 0; // this method is not allowed
                        }
                	    
/*
                	    $address = $oQuote->getShippingAddress();
                	    
                        $address->setCollectShippingRates(true);
                        
                        $bNeedCountryInjection = false;
                        $bNeedPostcodInjection = false;
                        
                        if (!$address->getCountryId())
                        {
                            $address->setCountryId('US'); // to emulate missing data 
                            $bNeedCountryInjection = true;
                        }
                        
                        if (!$address->getPostcode())
                        {
                            $address->setPostcode('12345'); // to emulate missing data 
                            $bNeedPostcodInjection = true;
                        }
                              	    
                    	$address->collectShippingRates()->save();
            
                        $groups = $address->getGroupedAllShippingRates();
                        
                        $address->setCollectShippingRates(false);
                        
                        $sShippingMethod = '';            
                                    
                        foreach ($groups as $code => $_rates)
                        {
                            foreach ($_rates as $_rate)
                            {
                                $sShippingMethod = $_rate->getCode();
                                $iMethodCount++;
                            }
                        }
                        
                        if ($bNeedCountryInjection)
                        {
                            $address->setCountryId(); // to clean missing data 
                        }
                        
                        if ($bNeedPostcodInjection)
                        {
                            $address->setPostcode(); // to clean missing data 
                        }
**/



                        if ($iMethodCount != 1) // only one method is allowed
                        {
                            $sStepKey = '';
                        }
                        else 
                        {
                            if (!Mage::registry('sShippingMethodStrict') and isset($sShippingMethod))
                            {
                                Mage::register('sShippingMethodStrict', $sShippingMethod);
                            }
                        }

            		break;
            		
                	case 'payment':
                	    
                	    $iMethodCount = 0;
                	    
                        $store = $oQuote ? $oQuote->getStoreId() : null;
                        $methods = Mage::helper('payment')->getStoreMethods($store, $oQuote);
                        foreach ($methods as $key => $method) 
                        {
                            if ($method->canUseCheckout())
                            {
                                $iMethodCount++;
                            }
                        }
                	    
                        if ($iMethodCount != 1 OR $method->getCode() != 'checkmo') // only one method is allowed (check money)
                        {
                            $sStepKey = '';
                        }
                        
            		break;
                }
                
                
                if ($sStepKey)
                {
                    $aDisabledSectionHash[] = $sStepKey;
                }
            }
        }

        $iStoreId = Mage::app()->getStore()->getId();
        $iSiteId  = Mage::app()->getWebsite()->getId();

        /* */
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitconfcheckout')->getLicense()->getPerformer();
        $ruler     = $performer->getRuler();
        if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
        {
            $aDisabledSectionHash = array();
        }
        /* */

        Mage::register('aDisabledSectionHash', $aDisabledSectionHash);
        Mage::register('bDisabledSectionRegistered', true);
        
        return $aDisabledSectionHash;
    }      
    
    public function getSubstituteCode()
    {
        return '100100101010100011100101010010101101';
    }    
    
    public function getSkipStepData($sCurrentStep, $oQuote)
    {
        if (!$oQuote) return false;
        
        $originalCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'review');
        
        if ($oQuote->isVirtual())
        {
            $originalCodes = array('billing', 'payment', 'review');
        }

        $aDisabledSectionHash = $this->getDisabledSectionHash($oQuote); 

        if (!$aDisabledSectionHash) return false;
       
        $stepCodes = array_diff($originalCodes, $aDisabledSectionHash);       

        foreach ($stepCodes as $sStep)
        {
            $newCodes[] = $sStep;
        }
        
        $sPostStep = 0;
        $sGotoStep = 0;
        
        foreach ($newCodes as $iKey => $sStep)
        {
            if ($sStep == $sCurrentStep)
            {
                $sGotoStep = $iKey + 1;
            }
        }
        
        if (!$sGotoStep OR !isset($newCodes[$sGotoStep])) return false;
        
        $sGotoCode = $newCodes[$sGotoStep];
        
        return $sGotoCode;
    }    
    
}
 } ?>