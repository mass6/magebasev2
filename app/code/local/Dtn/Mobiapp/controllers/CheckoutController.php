<?php

class Dtn_Mobiapp_CheckoutController extends Mage_Core_Controller_Front_Action {

    public function getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    function indexAction() {
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();

        $email = $this->getRequest()->getParam('email');
        $phone = $this->getRequest()->getParam('phone');
        $fullname = $this->getRequest()->getParam('fullname');
        $guest_street = $this->getRequest()->getParam('street');
        $guest_city = $this->getRequest()->getParam('city');
        $guest_district = $this->getRequest()->getParam('district');
        $region_id = $this->getRequest()->getParam('region_id');
        $date = $this->getRequest()->getParam('date'); //YYYY-mm-dd
        $timeslot = $this->getRequest()->getParam('timeslot'); //13:00-15:00

        if ($guest_city == null || $guest_city == '') {
            $guest_city = 'Hà Nội';
        }

        if ($guest_street == null || $guest_street == '') {
            $guest_street = 'N/A';
        }

        $p = array();
        $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
        $num_item = count($allcart);

        if ($num_item < 1) {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'Can not checkout, cart is empty')));
            return;
        }
        
        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = $session->getCustomer()->getData();
            $firstname = $customer["firstname"];
            $lastname = $customer["lastname"];
            $customerAddressId = $customer["default_shipping"];
            
            if ($customerAddressId) {
                $address = Mage::getModel('customer/address')->load($customer["default_shipping"]);
                $guest_street = $address->getStreet();
                $guest_city = $address->getCity();
                $guest_district = $address->getRegion();
                if ($address->getRegionId())
                    $region_id = $address->getRegionId();
                $phone = $address->getTelephone();
            }
            $data = array("firstname" => $firstname, "lastname" => $lastname,
                "company" => "", "street" => Array(0 => $guest_street, 1 => ""),
                "telephone" => $phone, "city" => $guest_city,
                "region" => $guest_district, "region_id" => $region_id, postcode => "",
                "country_id" => "VN", "use_for_shipping" => "1");
        } else {
            $customerAddressId = '';
            $data = array("firstname" => $fullname, "lastname" => ' (Guest)', "email" => $email,
                "company" => "", "street" => Array(0 => $guest_street, 1 => ""),
                "telephone" => $phone, "city" => $guest_city,
                "region" => $guest_district, "region_id" => $region_id, postcode => "",
                "country_id" => "VN", "use_for_shipping" => "1");
            $quote = $this->getOnepage()->getQuote();
            $quote->setCustomerId(null)
                    ->setCustomerEmail($email)
                    ->setCustomerIsGuest(true)
                    ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        try {
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            $shipping_method = 'flatrate_flatrate';
            $result = $this->getOnepage()->saveShippingMethod($shipping_method);
            $payment_method = array("method" => "checkmo");
            $result = $this->getOnepage()->savePayment($payment_method);
            $this->getOnepage()->saveOrder();
            $this->getOnepage()->getQuote()->save();
        } catch (Exception $e) {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'error occured during order detail calculation. Exception detail: '.$e->getMessage())));
            return;
        }

        if (Mage::helper('customer')->isLoggedIn()) {
            $order = Mage::getModel('sales/order')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('customer_id', $customer['entity_id'])
                    ->getLastItem();
            $orderIncrementId = $order->getIncrementId();
        } else {
            $orderIncrementId = $this->getOnepage()->getLastOrderId();
        }
        $this->getResponse()->setBody(json_encode(array('result_code' => '1', 'SID' => $sid, 'order_id' => $orderIncrementId, 'message' => 'Order created successfully')));
    }

    function preAction() {
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();

        $email = $this->getRequest()->getParam('email');
        $phone = $this->getRequest()->getParam('phone');
        $fullname = $this->getRequest()->getParam('fullname');
        $guest_city = $this->getRequest()->getParam('city');
        $guest_district = $this->getRequest()->getParam('district');
        $date = $this->getRequest()->getParam('date'); //YYYY-mm-dd
        $timeslot = $this->getRequest()->getParam('timeslot'); //13:00-15:00

        $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
        //$cart = Mage::getSingleton('checkout/cart');
        $quote = $this->getOnepage()->getQuote();

        $p = array();
        $r = array();
        $h = array();

        $street = '';
        $district = '';
        $city = '';
        $telephone = '';

        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = $session->getCustomer()->getData();
            $firstname = $customer["firstname"];
            $lastname = $customer["lastname"];
            $customerAddressId = $customer["default_shipping"];

            if ($customerAddressId) {
                $address = Mage::getModel('customer/address')->load($customer["default_shipping"]);
                $street = $address->getStreet();
                $city = $address->getCity();
                $district = $address->getRegion();
                $address->getTelephone();
            }
            /*
              $address = Mage::getModel('customer/address')->load($customerAddressId);
              $telephone = $address->getTelephone();
              $postcode = $address->getPostcode();
              $street = $address->getStreet();
              $city = $address->getCity();
              $country_id =$address->getCountryId();
             */
            $data = array("firstname" => $firstname, "lastname" => $lastname,
                "company" => "", "street" => Array(0 => $street, 1 => ""),
                "telephone" => $telephone, "city" => $city,
                "region" => $district, postcode => "",
                "country_id" => "VN", "use_for_shipping" => "1");
        } else {
            $customerAddressId = '';
            $data = array("firstname" => $fullname, "lastname" => ' (Guest)',
                "company" => "", "street" => Array(0 => '', 1 => ""),
                "telephone" => $phone, "city" => $guest_city,
                "region" => $guest_district, postcode => "",
                "country_id" => "VN", "use_for_shipping" => "1");
        }

        try {
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            $shipping_method = 'flatrate_flatrate';
            $result = $this->getOnepage()->saveShippingMethod($shipping_method);
            $payment_method = array("method" => "checkmo");
            $result = $this->getOnepage()->savePayment($payment_method);

            $this->getOnepage()->getQuote()->save();
        } catch (Exception $e) {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'error occured during order detail calculation')));
            return;
        }


        foreach ($allcart as $cartcur) {
            $productId = $cartcur->getData('product_id');
            $product = Mage::getModel('catalog/product')->load($productId);
            if ($product->getStatus() == 1) {
                $id = $product->getId();
                $title = $product->getName();
                $thumb = $product->getThumbnailUrl();
                $image = $product->getImageUrl();
                $price = $product->getFinalPrice();
                $shortdesc = $product->getShortDescription();
                $shortdesc = strip_tags($shortdesc);
                $description = $product->getDescription();
                $description = strip_tags($description);
                $qrcode = $product->getSku();
                $nappUpdate = $product->getUpdatedAt();
                $qty = $cartcur->getQty();
                $pArray = array("id" => $id, "price" => $price, "qty" => $qty, "thumb" => $thumb, "image" => $image, "title" => $title);
                $r[] = $pArray;
            }
        }
        $p['result_code'] = '1';
        $p['SID'] = $sid;
        $h['total_result'] = count($r);
        $h['product_list'] = $r;
        $h['sub_total'] = $quote->getSubtotal();
        $h['base_sub_total'] = $quote->getBaseSubtotal();
        $h['shipping_fee'] = $quote->getShippingAddress()->getShippingAmount();
        $h['sub_total_with_discount'] = $quote->getSubtotalWithDiscount();
        $h['base_grand_total'] = $quote->getBaseGrandTotal();
        $h['grand_total'] = $quote->getGrandTotal();
        $p['result'] = $h;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

}