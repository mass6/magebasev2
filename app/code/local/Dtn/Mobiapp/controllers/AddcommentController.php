<?php

class Dtn_Mobiapp_AddcommentController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $session = Mage::getSingleton('customer/session');
        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = $session->getCustomer()->getData();
        }
        $result = array();
        if (isset($customer)) {
            $request = $this->getRequest();
            $orderId = (int) $request->getParam('orderid');
            $comment = $request->getParam('comment');
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                $result['result_code'] = 0;
                $result['message'] = 'Order not found';
            } else {
                try {
                    $order->addStatusToHistory($order->getStatus(), $comment, false);
                    $order->save();
                    $result['result_code'] = 1;
                    $result['message'] = 'Comment successfully added';
                } catch (Exception $ex) {
                    $result['result_code'] = 0;
                    $result['message'] = 'Could not add comment. Please try again';
                }
            }
        } else {
            $result['result_code'] = '0';
            $result['message'] = 'Please log in';
        } 
        
        $this->getResponse()->setBody(json_encode($result));
    }

}
