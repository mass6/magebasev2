<?php

class Dtn_Mobiapp_GuestController extends Mage_Core_Controller_Front_Action {

    function checkoutAction() {
        $firstname = $this->getRequest()->getParam('firstname');
        $lastname = $this->getRequest()->getParam('lastname');
        $email = $this->getRequest()->getParam('email');
        $company = $this->getRequest()->getParam('company');
        $street = $this->getRequest()->getParam('street');
        $city = $this->getRequest()->getParam('city');
        $country_id = $this->getRequest()->getParam('country_id');
        $telephone = $this->getRequest()->getParam('telephone');
        try {
            $method = 'guest';
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $data = array("firstname" => $firstname, "lastname" => $lastname, "email" => $email, "company" => $company, "street" => array(0 => $street),
                "city" => $city, "country_id" => $country_id, "telephone" => $telephone, "save_in_address_book" => "1", "use_for_shipping" => "1");
            $customerAddressId = "";
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            $data = 'flatrate_flatrate';
            $result = $this->getOnepage()->saveShippingMethod($data, $customerAddressId);
            $data = array("method" => "checkmo");
            $result = $this->getOnepage()->savePayment($data);
            $this->getOnepage()->getQuote()->getPayment()->importData($data);
            $this->getOnepage()->saveOrder();
            $this->getOnepage()->getQuote()->save();
            $this->getResponse()->setBody(json_encode(array('result_code' => '1', 'message' => 'checkout success')));
        } catch (Exception $e) {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'message' => 'checkout false')));
            return;
        }
    }

}
