<?php

class Dtn_Mobiapp_Model_Abstract {

    protected $DEVELOPMENT = 'production'; // or 'sandbox'
    protected $errors = array();
    protected $log = true;
    protected $logFile = 'mobiapp.log';
    protected $readConn;
    protected $writeConn;
    private $_pushers = array();

    public function __construct() {
        return;
    }

    protected function _getDbConnections() {
        $resource = Mage::getSingleton('core/resource');
        if (!$this->readConn)
            $this->readConn = $resource->getConnection('core_read');
        if (!$this->writeConn)
            $this->writeConn = $resource->getConnection('core_write');
    }

    public function registerDevice($devicetoken, $deviceos, $deviceuserid) {
        $this->_getDbConnections();
        $sql = "SELECT COUNT(*) FROM mobiapp_pn_device WHERE device_token = '{$devicetoken}' AND device_os = '{$deviceos}' AND device_userid = '{$deviceuserid}' AND status = 1";
        $count = (int) $this->readConn->fetchOne($sql);
        if ($count == 0) {
            $sql = "INSERT INTO `mobiapp_pn_device`
				VALUES (
					NULL,
					'{$devicetoken}',
					'{$deviceos}',
					'{$deviceuserid}',
					'{$this->DEVELOPMENT}',
					'1',
					NOW(),
					NOW()
				)";
            try {
                $this->writeConn->query($sql);
                return true;
            } catch (Exception $ex) {
                $this->addError($ex->getMessage(), E_ERROR);
                return false;
            }
        } else {
            return true;
        }
    }

    public function unregisterDevice($token) {
        try {
            $sql = "UPDATE `mobiapp_pn_device`
				SET `status`='0'
				WHERE `devicetoken`='{$token}'
				LIMIT 1;";
            $this->writeConn->query($sql);
        } catch (Exception $ex) {
            $this->log('Error unregistering device: ' . $ex->getMessage());
        }
    }

    public function newApprovalMessage($approver, $order) {
        $this->_getDbConnections();
        // get registered approver devices
        $sql = "SELECT * FROM mobiapp_pn_device WHERE device_userid = '{$approver->getId()}' AND status = 1";
        $devices = $this->readConn->fetchAll($sql);
        foreach ($devices as $device) {
            // check if there is any queued message to this device
            $sql = "SELECT entity_id, badge FROM mobiapp_pn_message WHERE device_id = {$device['entity_id']} AND status = 'queued' LIMIT 1";
            $row = $this->readConn->fetchAll($sql);
            $message = array();
            $message['aps'] = array();
            if (count($row) == 0) {
                // there is no queued message, create new one
                if ($device['device_os'] == 'ios') {
                    $message['aps']['alert'] = "You have 1 new order to approve";
                    $message['aps']['badge'] = 1;
                    $message = json_encode($message);
                } else {
                    $message = "You have 1 new order to approve";
                }
                $sql = "INSERT INTO mobiapp_pn_message VALUES(NULL,'{$device['entity_id']}','{$message}',1,'queued',NOW(),NOW())";
            } else {
                // there is a queued message, update the message
                if ($device['device_os'] == 'ios') {
                    $message['aps']['alert'] = "You have " . ($row[0]['badge'] + 1) . " new orders to approve";
                    $message['aps']['badge'] = $row[0]['badge'] + 1;
                    $message = json_encode($message);
                } else {
                    $message = "You have " . ($row[0]['badge'] + 1) . " new orders to approve";
                }
                $sql = "UPDATE mobiapp_pn_message SET message = '{$message}', badge = badge + 1 WHERE entity_id = {$row[0]['entity_id']}";
            }
            try {
                $this->writeConn->query($sql);
//                $this->log("Send to device {$device['device_token']}, os: {$device['device_os']}, user id: {$device['device_userid']}, development: {$device['development']}, message: {$message}");
            } catch (Exception $ex) {
                $this->log($ex->getMessage());
            }
        }
    }

    public function beforePush($development) {
        return $development;
    }

    public function afterPush() {
        return;
    }

    private function _flushMessages() {
        $this->_getDbConnections();
        $sql = "SELECT
                m.entity_id,
                m.message,
                m.badge,
                d.device_token,
                d.device_os,
                d.development
                FROM mobiapp_pn_message m
                LEFT JOIN mobiapp_pn_device d ON d.entity_id = m.device_id
                WHERE d.status = 1 AND m.status = 'queued'
                LIMIT 100;";
        $this->_iterateMessages($sql);
    }
    
    private function _iterateMessages($sql) {
        try {
            $messages = $this->readConn->fetchAll($sql);
            if (count($messages) <= 0) {
                return;
            }
            
            $this->_pushers['ios'] = Mage::getModel('mobiapp/apn');
            $this->_pushers['android'] = Mage::getModel('mobiapp/gcm');
            
            foreach ($messages as $_message) {
                $_pusher = $this->_pushers[$_message['device_os']];
                if (!$_pusher)
                    continue;
                
                $id = $_message['entity_id'];
                $devicetoken = $_message['device_token'];
                $development = $_message['development'];
                $_pusher->beforePush($development);
                $message = $_pusher->prepareMessage($_message);
                $_pusher->pushMessage($id, $message, $devicetoken, $development);
            }
            
            foreach ($this->_pushers as $_pusher) {
                $_pusher->afterPush();
            }
        } catch (Exception $ex) {
            $this->log('Error getting messages: ' . $ex->getMessage());
        }
    }

    protected function _pushSuccess($pid) {
        $this->_getDbConnections();
        $sql = "UPDATE `mobiapp_pn_message`
				SET `status`='delivered'
				WHERE `entity_id`={$pid}
				LIMIT 1;";
        try {
            $this->writeConn->query($sql);
        } catch (Exception $ex) {
            $this->log($ex->getMessage());
        }
    }

    protected function _pushFailed($pid) {
        $this->_getDbConnections();
        $sql = "UPDATE `mobiapp_pn_message`
				SET `status`='failed'
				WHERE `entity_id`={$pid}
				LIMIT 1;";
        try {
            $this->writeConn->query($sql);
        } catch (Exception $ex) {
            $this->log($ex->getMessage());
        }
    }

    public function processQueue() {
        $this->_flushMessages();
    }

    public function getApproverDevice($email, $deviceos) {
        $this->_getDbConnections();
        $approver = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($email);
        $sql = "SELECT * FROM mobiapp_pn_device WHERE device_userid = '{$approver->getId()}' AND device_os = '{$deviceos}' AND status = 1";
        $devices = $this->readConn->fetchAll($sql);
        foreach ($devices as &$device) {
            $sql = "SELECT * FROM mobiapp_pn_message WHERE device_id = {$device['entity_id']}";
            $device['messages'] = $this->readConn->fetchAll($sql);
        }
        return $devices;
    }

    public function getAllDevices() {
        $this->_getDbConnections();
        $sql = "SELECT * FROM mobiapp_pn_device";
        return $this->readConn->fetchAll($sql);
    }

    function addError($error, $type = E_USER_NOTICE) {
        $this->errors[] = array('type' => $type, 'text' => $error);
    }

    function getErrors() {
        return $this->errors;
    }

    public function log($message) {
//        $this->addError($message);
        if ($this->log)
            Mage::log($message, null, $this->logFile);
    }

}
