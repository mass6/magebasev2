<?php

class Dtn_Mobiapp_RegisterdeviceController extends Mage_Core_Controller_Front_Action {

    /**
     * register device when login
     */
    public function indexAction() {
        $session = Mage::getSingleton('customer/session');
        if (Mage::helper('customer')->isLoggedIn()) {
            $approver = $session->getCustomer();
        }
        $result = array();
        if (isset($approver)) {
            $devicetoken = $this->getRequest()->getParam('devicetoken');
            $deviceos = $this->getRequest()->getParam('deviceos');
            if (!$devicetoken || strlen($devicetoken) < 10) {
                $result['result_code'] = 0;
                $result['message'] = 'Device token must not be blank';
            } else if (!$deviceos) {
                $result['result_code'] = 0;
                $result['message'] = 'Device OS must not be blank';
            } else {
                $apn = Mage::getModel('mobiapp/abstract');
                if ($apn->registerDevice($devicetoken, $deviceos, $approver->getId())) {
                    $result['result_code'] = 1;
                    $result['message'] = 'Device registered successfully';
                } else {
                    $result['result_code'] = 0;
                    $result['message'] = 'Error registering device';
                    $result['errors'] = $apn->getErrors();
                }
            }
        } else {
            $result['result_code'] = 0;
            $result['message'] = 'Please log in';
        }
        
        $this->getResponse()->setBody(json_encode($result));
    }
    
    public function testAction() {
        $resource = Mage::getSingleton('core/resource');
		$reader = $resource->getConnection('core_read');
        // get registered of approver device
        $sql = "SELECT * FROM mobiapp_pn_device WHERE device_userid = 89 AND status = 1";
        $rows = $reader->fetchAll($sql);
        var_dump($rows);
    }

}
