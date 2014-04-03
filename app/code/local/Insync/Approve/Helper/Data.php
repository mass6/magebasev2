<?php

class Insync_Approve_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getApproverandName($levelappr, $applevel) { // Mage::helper('insync_approve')->getApproverandName($levelappr, $appA);
        $appId = $this->getCustomerCategoryOptionId('Approver');
        $appA = $this->getCustomerActiveApproverOptionId('Yes');
        $data[0] = '---select---';
        $tempLevel = explode(',', $levelappr);
        for ($i = 0; $i < count($tempLevel); $i++) {
            $collection = Mage::getModel('customer/customer')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('entity_id', $tempLevel[$i])
                    ->addFieldToFilter('cust_category', $appId)
                    ->addFieldToFilter('active_approver', $appA)
					->addFieldToFilter('approval_level', $applevel);
            foreach ($collection as $customer) {
                $data[$customer->getId()] = $customer->getData('firstname') . ' ' . $customer->getData('lastname');
            }
            unset($collection);
        }
        return $data;
    }

    public function sendEmail($customerId, $order, $mailtemplete, $storeId, $emailTemplateVariables) { // Mage::helper('insync_approve')->sendEmail($customerId, $order, $mailtemplete)
        // $storee = Mage::getModel('core/store')->load($storeId);
		$storee = Mage::app()->getStore($storeId);
        // Mage::app()->setCurrentStore($storee);
		
		$customer = Mage::getModel('customer/customer')->load($customerId);
        $custEmail = $customer->getEmail();
        $emailTemplate = Mage::getModel('core/email_template');
        $test = $emailTemplate->loadDefault($mailtemplete); // sales_email_order_multi
        $emailTemplateVariables['username'] = $customer->getFirstname() . ' ' . $customer->getLastname();
        $emailTemplateVariables['order_id'] = $order->getIncrementId();
        $emailTemplateVariables['store_name'] = $storee->getName();
        $emailTemplateVariables['store_url'] = $storee->getHomeUrl();
        $emailTemplateVariables['store1url'] = $storee->getHomeUrl()."customer/account/";
        $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/email/logo/' . Mage::getStoreConfig('design/email/logo', $storeId);
        $emailTemplate->sendTransactional(
                $test, 'sales', $custEmail, $custEmail, $emailTemplateVariables, $storeId
        );
		
		// Mage::log('-------------------------------------------real user---');
		// Mage::log($storeId);
		// Mage::log($storee);
		// Mage::log($storee->getHomeUrl());
		// Mage::log($storee->getHomeUrl()."customer/account/");
		
		// Mage::log('-------------------------------------------real user---');
		// Mage::log($customer->getFirstname() . ' ' . $customer->getLastname());
    }

    private function sendTransactionalEmail() {
        // Transactional Email Template's ID
        $templateId = 1;
        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName, 'email' => $senderEmail);
        // Set recepient information
        $recepientEmail = 'test@est.com';
        $recepientName = 'Test Test';
        // Get Store ID
        $store = Mage::app()->getStore()->getId();
        // Set variables that can be used in email template
        $vars = array('customerName' => 'test', 'customerEmail' => 'customer@test.com');
        $translate = Mage::getSingleton('core/translate');
        // Send Transactional Email
        Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
        $translate->setTranslateInline(true);
    }

    public function getCustomerCategoryOptionId($key) { // Mage::helper('insync_approve')->getCustomerCategoryOptionId($key);
		$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cust_category');
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
				if($optionValue['label']==$key) return $optionValue['value']; // $key = 'Approver'
            }
        }
    }
	public function getCustomerActiveApproverOptionId($key) { // Mage::helper('insync_approve')->getCustomerActiveApproverOptionId($key);
		$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'active_approver');
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
				if($optionValue['label']==$key) return $optionValue['value']; // $key = 'Yes'
            }
        }
    }
	public function getCustomerOverrideBooleanOptionId($key) { // Mage::helper('insync_approve')->getCustomerOverrideBooleanOptionId($key);
		$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'lo');
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
				if($optionValue['label']==$key) return $optionValue['value']; // $key = 'Yes'
            }
        }
    }
	public function getCustomerApproverLevel() { // Mage::helper('insync_approve')->getCustomerApproverLevel();
		$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'approval_level');
        $data = array();
		if ($attribute->usesSource()) {
			$counter = 1;
            foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
				$data[$optionValue['label']] = $optionValue['value'];
            }
        }
		return $data;
    }
	public function getOrderApproverUser($currentlevel, $order, $ifaddition=0) { // Mage::helper('insync_approve')->getOrderApproverUser($currentlevel, $order, $ifaddition=0);
		switch($currentlevel+$ifaddition){
			case 1:return $order->getApproversLevel1();break;
			case 2:return $order->getApproversLevel2();break;
			case 3:return $order->getApproversLevel3();break;
			case 4:return $order->getApproversLevel4();break;
			case 5:return $order->getApproversLevel5();break;
		}
    }

}
