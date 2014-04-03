<?php

class Dtn_Mobiapp_RejectorderController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $session = Mage::getSingleton('customer/session');
        if (Mage::helper('customer')->isLoggedIn()) {
            $approver = $session->getCustomer();
        }
        $result = array();
        if (isset($approver)) {
            $orderId = $this->getRequest()->getParam('orderid');
            $comment = $this->getRequest()->getParam('comment');
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                $result['result_code'] = 0;
                $result['message'] = 'Order not found';
            } else if(!$comment) {
                $result['result_code'] = 0;
                $result['message'] = 'Please add your comment';
            } else if ($order->getApprover() != $approver->getId()) {
                $result['result_code'] = 0;
                $result['message'] = 'You do not have permission to reject this order';
            } else if ($order->getStatusLabel() == 'Declined') {
                $result['result_code'] = 0;
                $result['message'] = 'Could not complete the action. This order was rejected';
            } else {
                $objSapConfig = new Insync_Approve_Model_Id();
                $check = $objSapConfig->apiReject($approver, $order, $comment);
                if (!$check) {
                    $result['result_code'] = 0;
                    $result['message'] = 'Errors occurred. Please try again';
                } else {
                    $result['result_code'] = 1;
                    $result['message'] = 'Order was rejected successfully';
                }
            }
        } else {
            $result['result_code'] = '0';
            $result['message'] = 'Please log in';
        }

        $this->getResponse()->setBody(json_encode($result));
    }

}
