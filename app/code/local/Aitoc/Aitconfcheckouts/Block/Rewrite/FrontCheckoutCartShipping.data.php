<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Block/Rewrite/FrontCheckoutCartShipping.data.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ qhCiMpaMahjIDqwa('4600276bd03482711493a3a56efa167a'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutCartShipping extends Mage_Checkout_Block_Cart_Shipping
{
    /**
     * Get address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
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

    /*
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getEstimateCountryId()
    {
        return $this->getAddress()->getCountryId();
    }

    public function getEstimatePostcode()
    {
        return $this->getAddress()->getPostcode();
    }

    public function getEstimateCity()
    {
        return $this->getAddress()->getCity();
    }

    public function getEstimateRegionId()
    {
        return $this->getAddress()->getRegionId();
    }

    public function getEstimateRegion()
    {
        return $this->getAddress()->getRegion();
    }

    public function getCityActive()
    {
        return (bool)Mage::getStoreConfig('carriers/dhl/active');
    }

    public function getStateActive()
    {
        return (bool)Mage::getStoreConfig('carriers/dhl/active') || (bool)Mage::getStoreConfig('carriers/tablerate/active');
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->convertPrice($price, true);
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->formatPrice($this->helper('tax')->getShippingPrice($price, $flag, $this->getAddress()));
    }
*/
} } 