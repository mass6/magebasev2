<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Model/Rewrite/FrontSalesQuoteAddress.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ wcIpBhkBkiZTawok('debbcf69b8d98bb4a5230969842aef5b'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitconfcheckout_Model_Rewrite_FrontSalesQuoteAddress extends Mage_Sales_Model_Quote_Address
{
   /**
     * Validate address attribute values
     *
     * @return bool
     */
    private function _getAllowedFields()
    {
        
        $allowed = Mage::helper('aitconfcheckout')->getAllowedFieldHash($this->getAddressType());
        if(!Mage::getStoreConfig('aitconfcheckout/shipping/active') && $this->getAddressType() == 'shipping') {
            foreach($allowed as $k=>$v)
            {
                $allowed[$k] = 0;
            }
            $allowed['firstname'] = 0;
            $allowed['secondname'] = 0;
        }
        return $allowed;
    }
    
    public function validate()
    {
        $errors = array();
        //$this->_getAllowedFields();
        $allowed = $this->_getAllowedFields();
        $this->implodeStreetAddress();
      //  print_r($allowed);
        if (isset($allowed['firstname']) && $allowed['firstname'] && !Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Please enter the first name.');
        }

        if (isset($allowed['secondname']) && $allowed['secondname'] && !Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Please enter the last name.');
        }

        if ($allowed['address'] && !Zend_Validate::is($this->getStreet(1), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Please enter the street.');
        }

        if ($allowed['city'] && !Zend_Validate::is($this->getCity(), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Please enter the city.');
        }

        if ($allowed['telephone'] && !Zend_Validate::is($this->getTelephone(), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Please enter the telephone number.');
        }

        $_havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
        if ($allowed['postcode'] && !in_array($this->getCountryId(), $_havingOptionalZip)
            && !Zend_Validate::is($this->getPostcode(), 'NotEmpty')
        ) {
            $errors[] = Mage::helper('customer')->__('Please enter the zip/postal code.');
        }

        if ($allowed['country'] && !Zend_Validate::is($this->getCountryId(), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Please enter the country.');
        }

        if ($allowed['region'] && $this->getCountryModel()->getRegionCollection()->getSize()
               && !Zend_Validate::is($this->getRegionId(), 'NotEmpty')
               && Mage::helper('directory')->isRegionRequired($this->getCountryId())
        ) {
            $errors[] = Mage::helper('customer')->__('Please enter the state/province.');
        }

        if (empty($errors) || $this->getShouldIgnoreValidation()) {
            return true;
        }
        return $errors;
    }
} } 