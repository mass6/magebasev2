<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/controllers/OnepageController.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ CNfUrRerecMXOwId('8452660c5fdb606029a2a5fba0efb3a5'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Aitoc_Aitconfcheckout_OnepageController extends Mage_Checkout_OnepageController
{
    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        $this->getRequest()->setRouteName('checkout');

        return parent::preDispatch();
    }

    private function _getGotoStepResult($result, $sGotoStep)
    {
        if (!$sGotoStep) return false;
        
        switch ($sGotoStep) 
        {
        	case 'shipping_method':
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
    		break;
    		
        	case 'payment': // ???? event?
                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
    		break;
    		
        	case 'review': 
                $this->loadLayout('checkout_onepage_review');

                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->getLayout()->getBlock('root')->toHtml()
                );
    
    		break;
        }
        
        return $result;
    }
    
    public function progressAction()
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0', '>='))        
        {
            $this->getOnepage()->saveSkippedSata('progress');            
        }
        
        return parent::progressAction();
    }    
    
    // override parent
    public function saveBillingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                
                // start aitoc                
                          
                $sSkipData = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSkipStepData('billing', $this->getOnepage()->getQuote());
                
                if ($sSkipData AND $sSkipData != 'shipping')
                {
                    // insert shipping info data
                    
                    $result = $this->_getGotoStepResult($result, $sSkipData);
                    
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
                else 
                {
                    if (!$this->getOnepage()->getQuote()->isVirtual() AND isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) 
                    {
                        $sSkipData = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSkipStepData('shipping', $this->getOnepage()->getQuote());
                        
                        if ($sSkipData AND $sSkipData != 'shipping_method')
                        {
                            // insert shipping info data
                            
                            $result = $this->_getGotoStepResult($result, $sSkipData);
                            
                            $this->getResponse()->setBody(Zend_Json::encode($result));
                            return;
                        }
                    }
                }
                
                // finish aitoc  
                
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                }
                elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {

                   $result['goto_section'] = 'shipping_method';

                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                }
                else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    
    // override parent
    public function saveShippingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost())
        {
            // Magento behaves very strange in shipping information section. It overwrites default country (with JavaScript, i guess) with country you`ve enetered previosly. This is the solution or "hack" for that.
            $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');
            if (empty($aAllowedFieldHash['country']))
            {
                $_POST['shipping']['country_id'] = Mage::helper('aitconfcheckout')->getDefaultCountryId();               
            }
            
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                
                // start aitoc                
                          
                $sSkipData = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSkipStepData('shipping', $this->getOnepage()->getQuote());
                
                if ($sSkipData AND $sSkipData != 'shipping_method')
                {
                    // insert shipping info data
                    
                    $result = $this->_getGotoStepResult($result, $sSkipData);
                    
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
                
                // finish aitoc  
                
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }

//            $this->loadLayout('checkout_onepage_shippingMethod');
//            $result['shipping_methods_html'] = $this->getLayout()->getBlock('root')->toHtml();
//            $result['shipping_methods_html'] = $this->_getShippingMethodsHtml();

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    // override parent
    public function saveShippingMethodAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
                $this->getResponse()->setBody(Zend_Json::encode($result));

                // start aitoc                
                          
                $sSkipData = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSkipStepData('shipping_method', $this->getOnepage()->getQuote());
                
                if ($sSkipData AND $sSkipData != 'payment')
                {
                    // insert shipping info data
                    
                    $result = $this->_getGotoStepResult($result, $sSkipData);
                    
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
                
                // finish aitoc  
                
                
                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );

//                $result['payment_methods_html'] = $this->_getPaymentMethodsHtml();
            }
            if(version_compare(Mage::getVersion(),'1.6.1.0','ge'))
            {
                $this->getOnepage()->getQuote()->collectTotals()->save();
            } 
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }

    }    
    
} } 