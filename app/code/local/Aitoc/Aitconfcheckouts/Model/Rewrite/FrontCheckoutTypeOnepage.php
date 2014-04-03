<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Model/Rewrite/FrontCheckoutTypeOnepage.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ qhCiMpaMahjIDqwa('de01fd4e7ab5c00ca1ca469c8d01eaab'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitconfcheckout_Model_Rewrite_FrontCheckoutTypeOnepage extends Mage_Checkout_Model_Type_Onepage
{
    
    public function saveSkippedSata($sCurrentStep)
    {
        $aDisabledHash = Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($this->getQuote());
        
        if ($aDisabledHash AND (in_array('shipping_method', $aDisabledHash) OR in_array('payment', $aDisabledHash) OR in_array('shipping', $aDisabledHash)))
        {
            $bCompleteShiping = (bool)$this->getCheckout()->getStepData('shipping', 'complete');
            $bCompleteShipMet = (bool)$this->getCheckout()->getStepData('shipping_method', 'complete');
            $bCompletePayment = (bool)$this->getCheckout()->getStepData('payment', 'complete');
            
            if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
            {
                if ($bCompletePayment AND $bCompleteShipMet AND $bCompleteShiping) return false;
            }
            elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
            {
                if ($bCompletePayment AND $bCompleteShipMet AND $bCompleteShiping AND $sCurrentStep != 'progress') return false;
            }
        }
        else 
        {
            return false;
        }

        $bNeedSaveShiping = false;
        $bNeedSavePayment = false;
        $bNeedSaveShipMet = false;

        switch ($sCurrentStep)
        {
            case 'billing':
                
                if (in_array('shipping', $aDisabledHash)) // no shipping info step
                {
                    $bNeedSaveShiping = true;
                    
                    if (in_array('shipping_method', $aDisabledHash)) // no shipping method step
                    {
                        $bNeedSaveShipMet = true;
                        
                        if (in_array('payment', $aDisabledHash)) // no payment method step
                        {
                            $bNeedSavePayment = true;
                        }
                    }
                }
                elseif ($this->getQuote()->isVirtual())
                {
                    if (in_array('payment', $aDisabledHash)) // no payment method step
                    {
                        $bNeedSavePayment = true;
                    }
                }
                
            break;
            
            case 'shipping':
                
                if (in_array('shipping_method', $aDisabledHash)) // no shipping method step
                {
                    $bNeedSaveShipMet = true;
                    
                    if (in_array('payment', $aDisabledHash)) // no payment method step
                    {
                        $bNeedSavePayment = true;
                    }
                }
                
            break;
            
            case 'shipping_method':
                
                if (in_array('payment', $aDisabledHash)) // no payment method step
                {
                    $bNeedSavePayment = true;
                }
                
            break;
            
            case 'progress':

                if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
                {
                    //do nothing
                }
                elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
                {
                    if (in_array('shipping_method', $aDisabledHash)) // no shipping method step
                    {
                        $bNeedSaveShipMet = true;
                    }
                }
            
            break;
        }
        
        if ($bNeedSaveShiping)
        {
            $data = array
            (
                'firstname' => Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode(),
                'lastname'  => Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode(),
            );
            
            $aLocalConfig = Mage::helper('aitconfcheckout')->getStepData('shipping');        
            
            $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');  
                  
            foreach ($aLocalConfig as $sKey => $aValue)
            {
                foreach ($aValue as $sField)
                {
                    $data[$sField] = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode();
                }
            }

            if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
            {
                parent::saveShipping($data, 0);
            }
            elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
            {
                $this->saveShipping($data, 0);
            }

            // IF shipping step is disabled there is no way for shipping method to figure out which country to use.
            // So we set the default country from configuration->general->countries options instead of that.
            if (!Mage::getStoreConfig('aitconfcheckout/shipping/active'))
            {
                $address = $this->getQuote()->getShippingAddress();
                $billingCountry = null;
                $postData = Mage::app()->getRequest()->getPost('billing', array());                
                
                if (isset($postData['country_id']) && $postData['country_id'])
                {
                    $billingCountry = $postData['country_id'];
                }
                
                if (Mage::getStoreConfig('aitconfcheckout/shipping/copytoshipping') && $billingCountry)
                {
                    $defaultCountry = $billingCountry;
                }
                else
                {
                    $defaultCountry = Mage::helper('aitconfcheckout')->getDefaultCountryId();
                }
                
                $address->setCountryId($defaultCountry);
            }
            
            if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
            {
                //do nothing
            }
            elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
            {
                $this->getCheckout()->getQuote()->load($this->getCheckout()->getQuoteId());
            }
            
            $this->getCheckout()
                ->setStepData('shipping', 'complete', true);
//                ->setStepData('shipping_method', 'allow', true);
        }
        
        if ($bNeedSaveShipMet)
        {
            $address = $this->getQuote()->getShippingAddress();
            $address->collectShippingRates()->save();

            $groups = $address->getGroupedAllShippingRates();

            $shippingMethod = '';            
                        
            foreach ($groups as $code => $_rates)
            {
                foreach ($_rates as $_rate)
                {
                    $shippingMethod = $_rate->getCode();
                }
            }
            
            $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);
            $this->getQuote()->collectTotals()->save();
    
            $this->getCheckout()
                ->setStepData('shipping_method', 'complete', true);
//                ->setStepData('payment', 'allow', true);            
        }
        
        if ($bNeedSavePayment)
        {
            $data = array('method' => 'checkmo');
    
            $payment = $this->getQuote()->getPayment();
            $payment->importData($data);
            
            $this->getQuote()->getShippingAddress()->setPaymentMethod($payment->getMethod());
            $this->getQuote()->collectTotals()->save();
    
            $this->getCheckout()
                ->setStepData('payment', 'complete', true);
//                ->setStepData('review', 'allow', true);
            
        }
        
        return true;
    }
    
    /**
     * overwrite parent
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Aitoc_Aitconfcheckout_Model_Rewrite_FrontCheckoutTypeOnepage|array
     */
    public function saveBilling($data, $customerAddressId)
    {
        $helper = Mage::helper('checkout');

        if (empty($data)) {
            return array(
                'error' => -1,
                'message' => $helper->__('Invalid data.'),
            );
        }

        $address = $this->getQuote()->getBillingAddress();
        if (!empty($customerAddressId))
        {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);

            if ($customerAddress->getId())
            {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId())
                {
                    return array(
                        'error' => 1,
                        'message' => $helper->__('Customer Address is not valid.'),
                    );
                }

                // START AITOC CODE

                $saveddata = $customerAddress->getData();
                $aLocalConfig = Mage::helper('aitconfcheckout')->getStepData('billing');
                $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('billing');

                foreach ($aLocalConfig as $sKey => $aValue)
                {
                    foreach ($aValue as $sField)
                    {
                        if (!isset($aAllowedFieldHash[$sKey]) OR !$aAllowedFieldHash[$sKey])
                        {
                            $saveddata[$sField] = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode();
                        }
                    }
                }

                $customerAddress->addData($saveddata);

                // END AITOC CODE

                $address->importCustomerAddress($customerAddress);
            }
        }
        else
        {
            // START AITOC CODE

            $aLocalConfig = Mage::helper('aitconfcheckout')->getStepData('billing');
            $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('billing');

            foreach ($aLocalConfig as $sKey => $aValue)
            {
                foreach ($aValue as $sField)
                {
                    if (!isset($aAllowedFieldHash[$sKey]) OR !$aAllowedFieldHash[$sKey])
                    {
                        if ($sKey == 'country' AND !empty($data[$sField]))
                        {
                            // skip data replace
                        }
                        else
                        {
                            $data[$sField] = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode();
                        }
                    }
                }
            }

            unset($data['address_id']);
            $address->addData($data);

            // END AITOC CODE

        }

        // validate billing address
        if (($validateRes = $address->validate()) !== true)
        {
            return array(
                'error' => 1,
                'message' => $validateRes,
            );
        }

        if (version_compare(Mage::getVersion(), '1.4.2', '>='))
        {
            if (true !== ($result = $this->_validateCustomerData($data)))
            {
                return $result;
            }
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod())
        {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId()))
            {
                return array(
                    'error' => 1,
                    'message' => $helper->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.'),
                );
            }
        }

        $address->implodeStreetAddress();

        if (!$this->getQuote()->isVirtual())
        {
            /**
            * Billing address using otions
            */
            $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

            switch($usingCase)
            {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;

                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();
                    $shipping->addData($billing->getData())
                    ->setSameAsBilling(1)
                    ->setShippingMethod($shippingMethod)
                    ->setCollectShippingRates(true);
                    $this->getCheckout()->setStepData('shipping', 'complete', true);
                    break;
            }
        }



        if (true !== $result = $this->_processValidateCustomer($address))
        {
            return $result;
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        $this->getCheckout()
        ->setStepData('billing', 'allow', true)
        ->setStepData('billing', 'complete', true)
        ->setStepData('shipping', 'allow', true);

        // START AITOC CODE

        $this->saveSkippedSata('billing');

        if (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1)
        {
            $this->saveSkippedSata('shipping');
        }

        // END AITOC CODE

        return array();
    }
    
    // overwrite parent
    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid data')
            );
            return $res;
        }
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()
                    AND !Mage::getStoreConfigFlag('adjgiftreg/general/active') // for adj_giftreg compatibility
                   ) 
                {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }
                
// start aitoc            
                
                $saveddata = $customerAddress->getData();

                $aLocalConfig = Mage::helper('aitconfcheckout')->getStepData('shipping');        
                
                $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');        
                foreach ($aLocalConfig as $sKey => $aValue)
                {
                    foreach ($aValue as $sField)
                    {
                        if (!isset($aAllowedFieldHash[$sKey]) OR !$aAllowedFieldHash[$sKey])
                        {
                            $saveddata[$sField] = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode();
                        }
                    }
                }
                
                $customerAddress->addData($saveddata);
                
// finish aitoc            
                
                
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            
// start aitoc            
            $aLocalConfig = Mage::helper('aitconfcheckout')->getStepData('shipping');        
            
            $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');        
            foreach ($aLocalConfig as $sKey => $aValue)
            {
                foreach ($aValue as $sField)
                {
                    if (!isset($aAllowedFieldHash[$sKey]) OR !$aAllowedFieldHash[$sKey])
                    {
                        if ($sKey == 'country' AND !empty($data[$sField]))
                        {
                            // skip data replace
                        }
                        else 
                        {
                            $data[$sField] = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSubstituteCode();
                        }
                    }
                }
            }
// finish aitoc            
            
            unset($data['address_id']);
            $address->addData($data);
        }
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            $res = array(
                'error' => 1,
                'message' => $validateRes
            );
            return $res;
        }

        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()
            ->setStepData('shipping', 'complete', true)
            ->setStepData('shipping_method', 'allow', true);

// start aitoc            
        $this->saveSkippedSata('shipping');
// finish aitoc            
            
        return array();
    }
    
    
    
    // overwrite parent
    public function saveShippingMethod($shippingMethod)
    {
        $oResult = parent::saveShippingMethod($shippingMethod);
        
        if (!$oResult) // no errros
        {
            $this->saveSkippedSata('shipping_method');
        }
        
        return $oResult;
    }
    
    // overwrite parent
     public function checkAitocModule()
    {
        $oConfig = Mage::getConfig();
        $sModuleFile = $oConfig->getOptions()->getEtcDir() . '/modules/Aitoc_Aitconfcheckout.xml';
        
        if (!file_exists($sModuleFile))
        {
            return false;
        }
        
        $oModuleMainConfig = simplexml_load_file($sModuleFile);
        
        $bIsActive = (bool)('true' == $oModuleMainConfig->modules->Aitoc_Aitcheckoutfields->active);
        return $bIsActive;
    }      
} } 