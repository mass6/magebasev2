<?php
/**
 * Product:     Configurable Checkout
 * Package:     Aitoc_Aitconfcheckout_2.1.11_2.0.3_434995
 * Purchase ID: cNogWVhB2mHCXupuJxNbglhN7PlDCxQldTSTebqOTK
 * Generated:   2012-11-20 05:29:30
 * File path:   app/code/local/Aitoc/Aitconfcheckout/Helper/Data.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitconfcheckout')){ qhCiMpaMahjIDqwa('535af5e014284f12b8925f72e4a4eabe'); ?><?php

class Aitoc_Aitconfcheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEFAULT_COUNTRY  = 'general/country/default';
    protected $_disabledHash = null;

    public function getStepData($sStepKey = '')
    {
        $aConfigData = array
        (
            'billing' => array
            (
//                'name'      => array('firstname', 'lastname'),
                'company'   => array('company'),
                'address'   => array('street'),
                'city'      => array('city'),
                'region'    => array('region_id', 'region'),
                'country'   => array('country_id'),
                'postcode'  => array('postcode'),
                'telephone' => array('telephone'),
                'fax'       => array('fax'),
            ),
            'shipping' => array
            (
//                'name'      => array('firstname', 'lastname'),
                'company'   => array('company'),
                'address'   => array('street'),
                'city'      => array('city'),
                'region'    => array('region_id', 'region'),
                'country'   => array('country_id'),
                'postcode'  => array('postcode'),
                'telephone' => array('telephone'),
                'fax'       => array('fax'),
            ),
        );
        
        
        if ($sStepKey AND isset($aConfigData[$sStepKey]))
        {
            return $aConfigData[$sStepKey];
        }
        else 
        {
            return $aConfigData;
        }
    }
    
    public function getDefaultCountryId()
    {
        return $this->getDefaultCountry()->getId();
        /*$countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
        ->loadByStore();

        $aCountryOptions = $countryCollection->toOptionArray();        
        
        if (sizeof($aCountryOptions) == 2) // more then 1 country are active
        {
            return $aCountryOptions[1]['value'];
        }
        else 
        {
            return '';
        }*/
    }
    
    public function getAllowedFieldHash($sStepKey)
    {
        if (!$sStepKey) return false;
        
        $aAllowedFieldHash = array();
        
        $aStepData = $this->getStepData($sStepKey);
        
        $iStoreId = Mage::app()->getStore()->getId();
        $iSiteId  = Mage::app()->getWebsite()->getId();

        /* */
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitconfcheckout')->getLicense()->getPerformer();
        $ruler     = $performer->getRuler();
        if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
        {
            foreach ($aStepData as $sKey => $aData)
            {
                $aAllowedFieldHash[$sKey] = true;
            }

            return $aAllowedFieldHash;
        }
        /* */

        foreach ($aStepData as $sKey => $aData)
        {
            $aAllowedFieldHash[$sKey] = Mage::getStoreConfig('aitconfcheckout/' . $sStepKey . '/' . $sKey);
            
            if ($sKey == 'country' AND !$aAllowedFieldHash[$sKey] AND $aAllowedFieldHash['region'])
            {
                $countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
                ->loadByStore();
        
                $aCountryOptions = $countryCollection->toOptionArray();        
                
                if (sizeof($aCountryOptions) == 2) // more then 1 country are active
                {
                    $this->_contryId = $aCountryOptions[1]['value'];
                }
                else 
                {
                    //start fix of bug with displaying country field when it is disabled
                    //$aAllowedFieldHash[$sKey] = true;
					//end fix of bug with displaying country field when it is disabled
                }
            }
        }

        return $aAllowedFieldHash;
    }

    /**
     *
     * @param int $store
     * @return Mage_Directory_Model_Country
     */
    public function getDefaultCountry($store = null)
    {
        return Mage::getModel('directory/country')->loadByCode(Mage::getStoreConfig(self::XML_PATH_DEFAULT_COUNTRY, $store));
    }


    public function checkStepActive($quote, $sStepCode)
    {
        $aDisabledSectionHash = $this->getDisabledSectionHash($quote);

        if ($aDisabledSectionHash AND in_array($sStepCode, $aDisabledSectionHash))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function getDisabledSectionHash($quote)
    {
        if(is_null($this->_disabledHash)) {
            $this->_disabledHash = Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($quote);
        }
        return $this->_disabledHash;
    }

}

 } ?>