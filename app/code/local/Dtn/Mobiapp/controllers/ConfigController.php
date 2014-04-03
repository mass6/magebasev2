<?php

class Dtn_Mobiapp_ConfigController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $store = $this->getRequest()->getParam('store');
        $p['min_order_value'] = '1';
        $countries = array();
        $countries['VN'] = 'Việt Nam';
        $countries['US'] = 'United States';
        $p['supported_countries'] = $countries;
        $p['supported_cities'] = array('Hà Nội');
        $p['qr_enabled'] = '1';
        $p['favourite'] = '1';
        $p['allow_guest_checkout'] = '1';
        $p['allow_delivery_time_selection'] = '0';
        $p['allow_delivery_slot_selection'] = '0';
        $p['require_delivery_time_selection'] = '0';
        $p['require_delivery_address'] = '1';
        $p['min_delivery_days'] = '0';
        $p['main_banner_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/mobile_app_banner.jpg';
        $p['main_banner2x_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/mobile_app_banner2x.jpg';

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

    public function timeslotAction() {
        $date = $this->getRequest()->getParam('date'); //format YYYY-mm-dd
        //add logic to check here
        $p['timeslot'] = array('11:00-13:00', '13:00-15:00', '15:00-17:00', '17:00-19:00');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

    public function getRegionsAction() {
        $countryId = $this->getRequest()->getParam('country_id');

        $collection = Mage::getResourceModel('directory/region_collection')
                ->addCountryFilter($countryId)
                ->setOrder('region_id', 'ASC')
                ->setOrder('name', 'ASC')
                ->setOrder('default_name', 'ASC')
                ->load();
        
        $regions = array();
        foreach ($collection as $item) {
            $regions[] = array(
                'region_id' => $item->getRegionId(),
                'country_id' => $item->getCountryId(),
                'code' => $item->getCode(),
                'name' => $item->getName()
            );
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($regions));
    }
    
    public function getCountriesAction() {
        $store = $this->getRequest()->getParam('store');
        
        $countries = Mage::getResourceModel('directory/country_collection')->loadByStore($store)->toOptionArray(false);
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($countries));
    }

}
