<?php

class Dtn_Mobiapp_TestController extends Mage_Core_Controller_Front_Action {

    public function registerdeviceAction() {
        $session = Mage::getSingleton('customer/session');
        if (Mage::helper('customer')->isLoggedIn()) {
            $approver = $session->getCustomer();
        }
        $result = array();
        if (isset($approver)) {
            $devicetoken = $this->getRequest()->getParam('devicetoken');
            $deviceos = $this->getRequest()->getParam('deviceos');
            $deviceuserid = $approver->getId();
            $apn = Mage::getModel('mobiapp/apn');
            $apn->registerDevice($devicetoken, $deviceos, $deviceuserid);
            echo 'device was registered';
        } else {
            $result['result_code'] = 0;
            $result['message'] = 'Please log in';
        }
        
        $this->getResponse()->setBody(json_encode($result));
    }
    
    public function pushmessagesAction() {
//        echo strlen('a6e25794b6c79fa70e548afd5005cb716ac9d767498294e45069b4dfafbc7dc8');return;
        $apn = Mage::getModel('mobiapp/abstract');
        $apn->processQueue();
    }
    
    public function pushmessageAction() {
        $email = $this->getRequest()->getParam('email');
        $message = $this->getRequest()->getParam('message');
        $os = $this->getRequest()->getParam('os');
        switch ($os) {
            case 'ios':
                echo Mage::getModel('mobiapp/apn')->pushTestMessage($email, $message);
                break;
            case 'android':
                echo Mage::getModel('mobiapp/gcm')->pushTestMessage($email, $message);
                break;
            default:
                echo 'Missing OS parameter: os=ios|android';
                break;
        }   
    }


    public function getdevicebyemailAction() {
        $email = $this->getRequest()->getParam('email');
        $apn = Mage::getModel('mobiapp/apn');
        $devices = $apn->getApproverDevice($email);
        $this->getResponse()->setBody(json_encode($devices));
    }
    
    public function getdevicesAction() {
        $devices = Mage::getModel('mobiapp/apn')->getAllDevices();
        echo '<table border="1" width="100%"><tr><th>ID</th><th>Token</th><th>OS</th><th>User email</th><th>Development</th><th>Status</th><th>Registered date</th></tr>';
        foreach ($devices as $device) {
            echo '<tr>';
            echo "<td>{$device['entity_id']}</td>";
            echo "<td>{$device['device_token']}</td>";
            echo "<td>{$device['device_os']}</td>";
            $customer = Mage::getModel('customer/customer')->load($device['device_userid']);
            echo "<td>{$customer->getEmail()}</td>";
            echo "<td>{$device['development']}</td>";
            echo "<td>" . ($device['status'] == 1 ? 'registered' : 'unregistered') . "</td>";
            echo "<td>{$device['created']}</td>";
            echo '</tr>';
        }
        echo '</table>';
        die;
    }

}
