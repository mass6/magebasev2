<?php

class Dtn_Mobiapp_UserController extends Mage_Core_Controller_Front_Action {

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    function registerAction() {
        $secret_key = $this->getRequest()->getParam('secret_key');
        $firstname = $this->getRequest()->getParam('firstname');
        $lastname = $this->getRequest()->getParam('lastname');
        $email = $this->getRequest()->getParam('email');
        $password = $this->getRequest()->getParam('password');

        $p = array();
        $p['result_code'] = '0';
        if ($secret_key == '5Yb5zk2rixDoEmltaDt1s8aBNuqDiP4o') {
            $session = $this->_getSession();
            $session->setEscapeMessages(true);
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);

            $customerData = array('firstname' => $firstname, 'lastname' => $lastname, 'email' => $email);

            $customer->getGroupId();

            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($password);
                    $customer->setConfirmation($password);
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;
                if (true === $validationResult) {
                    $customer->save();
                    $p['result_code'] = '1';
                    $p['message'] = 'You registered success';
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
                } else {
                    $post = array('firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'password' => $password, 'confirmation' => $password);
                    $p['result_code'] = '0';
                    $session->setCustomerFormData($post);
                    if (is_array($errors)) {                        
                        $p['message'] ='';
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                            $p['message'] .= $errorMessage .' ';
                        }                        
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                        $p['message'] = $this->__('Invalid customer data');
                    }
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
                }
            } catch (Mage_Core_Exception $e) {
                $post = array('firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'password' => $password, 'confirmation' => $password);
                $session->setCustomerFormData($post);
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $p['message'] = 'There is already an account with this email addres';
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
                }
            } catch (Exception $e) {
                $p['message'] = 'cannot  register ';
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
            }
        } else {
            $p['result_code'] = '-2001';
            $p['message'] = 'secret_key is incorrect';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
        }
    }

    function updateAction() {
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();

        $phone = $this->getRequest()->getParam('phone');
        $street = $this->getRequest()->getParam('street');
        $city = $this->getRequest()->getParam('city');
        $district = $this->getRequest()->getParam('district');
	$country = $this->getRequest()->getParam('country');
        try {
            if (Mage::helper('customer')->isLoggedIn()) {
                $customer = $session->getCustomer();
                $address = $customer->getDefaultShippingAddress();
                if ($address) {
                    try {
                        if (isset($city)) {
                            $address->setCity($city);
                        }
                        if (isset($district)) {
                            $address->setRegion($district);
                        }
                        if (isset($street)) {
                            $address->setStreet($street);
                        }
                        if (isset($phone)) {
                            $address->setTelephone($phone);
                        }
			if (isset($country)) {
                            $address->setCountry($country);
                        }
                        $address->save();
                        $p = array('response_code' => 1, 'message' => 'Address saved successfully');
                    } catch (Exception $e) {
                        $p = array('response_code' => 0, 'message' => 'Error saving existing address. ' . $e->getMessage());
                    }
                } else {
                    $_new_address = array(
                        'firstname' => $customer->getFirstname(),
                        'lastname' => $customer->getLastname(),
                        'street' => array(
                            '0' => $street,
                            '1' => '',
                        ),
                        'city' => $city,
                        'region' => $district,
                        'country_id' => 'VN',
                        'telephone' => $phone,
                    );

                    $newAddress = Mage::getModel('customer/address');
                    $newAddress->setData($_new_address)
                            ->setCustomerId($customer->getId())
                            ->setIsDefaultBilling('1')
                            ->setIsDefaultShipping('1')
                            ->setSaveInAddressBook('1');

                    try {
                        $newAddress->save();
                        $p = array('response_code' => 1, 'message' => 'New address created successfully and set as default shipping address');
                    } catch (Exception $e) {
                        $p = array('response_code' => 0, 'message' => 'Error adding new address. ' . $e->getMessage());
                    }
                }
            } else {
                $p = array('response_code' => 0, 'message' => 'Please login');
            }
        } catch (Exception $e) {
            $p = array('response_code' => 0, 'message' => $e->getMessage());
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

    function changepassAction() {
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();

        $newpass = $this->getRequest()->getParam('newpass');
   
        try {
            if (Mage::helper('customer')->isLoggedIn()) {
                $customer = $session->getCustomer();
                $customer->changePassword($newpass);
                $p = array('response_code' => 1, 'message' => 'Password has been changed successfully');
            }else{
                $p = array('response_code' => 0, 'message' => 'Please login');
            }
        } catch (Exception $e) {
            $p = array('response_code' => 0, 'message' => 'Error changing password. ' . $e->getMessage());
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

}
