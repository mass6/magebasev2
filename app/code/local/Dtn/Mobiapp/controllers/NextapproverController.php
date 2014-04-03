<?php

class Dtn_Mobiapp_NextapproverController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $session = Mage::getSingleton('customer/session');
        if (Mage::helper('customer')->isLoggedIn()) {
            $approver = $session->getCustomer();
        }
        $result = array();
        if (isset($approver)) {
            $orderId = $this->getRequest()->getParam('orderid');
            $nextApproverId = (int) $this->getRequest()->getParam('appid');
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                $result['result_code'] = 0;
                $result['message'] = 'Order not found';
            } else {
                $objSapConfig = new Insync_Approve_Model_Id();
                $objSapConfig->Approver($nextApproverId, $orderId, $approver->getId());
                $result['result_code'] = 1;
                $result['message'] = 'Order approved successfully';
            }
        } else {
            $result['result_code'] = 0;
            $result['message'] = 'Please log in';
        }
        
        $this->getResponse()->setBody(json_encode($result));
    }

}
