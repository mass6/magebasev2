<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Model/Rewrite/FrontSalesServiceQuote.data.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ hQNIaTBaBCmAWhcV('225561f299f666c58fcb62575b5c3848'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitconfcheckout_Model_Rewrite_FrontSalesServiceQuote extends Mage_Sales_Model_Service_Quote
{
    
    /**
     * Submit nominal items
     *
     * @return array
     */
    public function submitNominalItems()
    {
        if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
        {
            return;
        }
        elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
        {
            $this->_validate();
            $this->_submitRecurringPaymentProfiles();
            $this->_deleteNominalItems();
        }
    }
    
    // overwrite parent
    
    protected function _validate()
    {
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException(
                    Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                );
            }
             if(!Mage::getStoreConfig('aitconfcheckout/shipping_method/active'))
            {
                $rate = 0.0001;
                $method = 'n_a';
            }
            else
            {
                $method= $address->getShippingMethod();
                $rate  = $address->getShippingRateByCode($method);
            }
            
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException(Mage::helper('sales')->__('Please specify a shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException(
                Mage::helper('sales')->__('Please check billing address information. %s', implode(' ', $addressValidation))
            );
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException(Mage::helper('sales')->__('Please select a valid payment method.'));
        }
        
        // process billing
        
        $billing = $this->getQuote()->getBillingAddress();
        
        $data = $billing->getData();
        
        $aLocalConfig = Mage::helper('aitconfcheckout')->getStepData('billing');        
        
        $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('billing');        
        
        foreach ($aLocalConfig as $sKey => $aValue)
        {
            foreach ($aValue as $sField)
            {
                if (!isset($aAllowedFieldHash[$sKey]) OR !$aAllowedFieldHash[$sKey])
                {
                    if ($sKey == 'country' AND !empty($data[$sField])/* AND $aAllowedFieldHash['region']*/)
                    {
                        // skip data replace
                    }
                    else 
                    {
                        $data[$sField] = '';
                    }
                }
            }
        }
        $billDataAfterClear = $data;
        $billing->addData($data);        
        
        // process shipping
        
        if (!$this->getQuote()->isVirtual()) 
        {
            $shipping = $this->getQuote()->getShippingAddress();
            
            $data = $shipping->getData();
            
            $sSubstituteCode = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode();
                
            if (Mage::getStoreConfig('aitconfcheckout/shipping/active')) // step is activated
            {
                $aLocalConfig = Mage::helper('aitconfcheckout')->getStepData('shipping');        
                
                $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');  
                      
                foreach ($aLocalConfig as $sKey => $aValue)
                {
                    foreach ($aValue as $sField)
                    {
                        if (!isset($aAllowedFieldHash[$sKey]) OR !$aAllowedFieldHash[$sKey] OR $data[$sField] == $sSubstituteCode)
                        {
                            if ($sKey == 'country' AND !empty($data[$sField])/* AND $aAllowedFieldHash['region']*/)
                            {
                                // skip data replace
                            }
                            else 
                            {
                                $data[$sField] = '';
                            }
                        }
                    }
                }
            }
            else 
            {
                
                foreach ($data as $sKey => $sValue)
                {
                    if (is_array($sValue) && (1 == count($sValue)))
                    {
                        $sValue = current($sValue);
                    }
                    
                    try {
                        $sValue = (string) $sValue;
                    }
                    catch (Exception $e)
                    {
                        continue;                		
                    }                    
                    
                    if ($sValue == $sSubstituteCode OR strpos($sValue, $sSubstituteCode) !== false)
                    {
                        $data[$sKey] = '';
                    }
                }
                if (!((!Mage::getStoreConfig('aitconfcheckout/' .'shipping'. '/active'))&&(Mage::getStoreConfig('aitconfcheckout/shipping/copytoshipping'))))
                {
                   
                    $data['country_id'] = '';
                    $data['postcode']   = '';                    
                }
                else
                {
                    $elementsToReplace = array(
                        0=>'company', 
                        1=>'street',
                        2=>'city',
                        3=>'region',
                        4=>'region_id',
                        5=>'country_id',
                        6=>'postcode',
                        7=>'telephone',
                        8=>'fax',
                        9=>'firstname',
                        10=>'middlename',
                        11=>'lastname',
                        12=>'suffix',
                        13=>'prefix'
                    );
                    foreach($elementsToReplace as $k=>$v)
                    {  
                        if(isset($billDataAfterClear[$v]))
                        {
                            $data[$v] = $billDataAfterClear[$v];
                        }
                    }    
                }
            }
            
            $shipping->addData($data);
        }        
        
        return $this;
    }    
} } 