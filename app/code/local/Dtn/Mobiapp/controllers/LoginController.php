<?php

class Dtn_Mobiapp_LoginController extends Mage_Core_Controller_Front_Action {

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function getCustomerId() {
        $session = $this->_getSession();
        $data = $session->getCustomer();
        $id = $data->getEntityId();
        return $id;
    }

    public function indexAction() {
        $u = $this->getRequest()->getParam('u');
        $p = $this->getRequest()->getParam('p');
        $before_login_quote_id = $this->getRequest()->getParam('quote');
        $login = array('username' => $u, 'password' => $p);
        $p = array();
        $r = array();
//        $session = Mage::getSingleton('customer/session');
//        if (Mage::helper('customer')->isLoggedIn()) {
//            $customer = $session->getCustomer()->getData();
//            $p['already_loggedin'] = 'Yes';
//        }
        if (!empty($login['username']) && !empty($login['password'])) {
            try {
                $session = Mage::getSingleton('customer/session');
                $session->login($login['username'], $login['password']);
                $customer = $session->getCustomer()->getData();
                
                $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
                //need to merge this quote with the quote passed from request                
                
                $p['quote'] = $quote_id;
                $p['firstname'] = $customer['firstname'];
                $p['lastname'] = $customer['lastname'];
                
                if (isset($customer["default_shipping"])) {
                    $address = Mage::getModel('customer/address')->load($customer["default_shipping"]);
                    $street = $address->getStreet();
                    $city = $address->getCity();
                    $district = $address->getRegion();
		    $country = $address->getCountry();
		    $a = array();
                    if(is_array($street)){
                        $a['street'] = $street[0];
                    }else{
                        $a['street'] = $street;
                    }
                    if($district!=null && $district!=''){
                        $a['district'] = $district;
                    }
                    if($city!=null && $city!=''){
                        $a['city'] = $city;
                    }
		    if($country!=null && $country!=''){
                        $a['country'] = $country;
                    }
		    $p['address'] = $a;
                    $p['telephone'] = $address->getTelephone();
                } else {
                    $p['address'] = '';
                }
                $r['result_code'] = 1;
                $r['SID'] = $session->getSessionId();
                $r['result'] = $p;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($r));
            } catch (Mage_Core_Exception $e) {
                $p['result_code'] = 0;
                $p['message'] = 'please check user and password';
                //$r['result'] = $p;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
            } catch (Exception $e) {
                
            }
        } else {
            $p['result_code'] = 0;
            $p['message'] = 'please check user and password';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
        }
    }

}
