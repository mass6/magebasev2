<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Block/Rewrite/FrontCheckoutOnepageProgress.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ pqhwmoZmZqecjphZ('dfa8dec54860405db9ac8d86f1bb112a'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageProgress extends Mage_Checkout_Block_Onepage_Progress
{
    public function getBilling()
    {
// start aitoc
        $billing = parent::getBilling();

        $data = $billing->getData();
        
            foreach ($data as $sField => $sValue)
            {
                if ($sValue == Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode())
                {
                    $data[$sField] = '';
                }
            }
        
        $billing->addData($data);        
        
        return $billing;
// finish aitoc        
//        return $this->getQuote()->getBillingAddress();
    }

    public function getShipping()
    {
// start aitoc
        $shipping = parent::getShipping();

        $data = $shipping->getData();
        
            foreach ($data as $sField => $sValue)
            {
                if ($sValue == Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode())
                {
                    $data[$sField] = '';
                }
            }
        
        $shipping->addData($data);        
        
        return $shipping;
// finish aitoc        
//        return $this->getQuote()->getShippingAddress();
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
    
    public function getProcessAddressHtml($sHtml)
    {
        $sHtml = nl2br($sHtml);

        $sHtml = str_replace(array('<br/>','<br />'), array('<br>', '<br>'), $sHtml); 
        
        $aReplace = array
        (
'<br><br>',    
    
'<br>
<br>',        

', <br>', ',  <br>'        
        );       
        
        while (strpos($sHtml, $aReplace[0]) !== false OR strpos($sHtml, $aReplace[1]) !== false) 
        {
        	$sHtml = str_replace($aReplace, '<br>', $sHtml);
        }

        if (strpos($sHtml, '<br>') === 0)
        {
            $sHtml = substr($sHtml, 4);
        }
           
        return $sHtml;
    }      
    
} } 