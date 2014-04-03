<?php

class Dtn_Mobiapp_Model_Gcm extends Dtn_Mobiapp_Model_Abstract {

    private $apiKey = 'AIzaSyDxbbf5nkRSESGsbZXhy2wUza-ROcRINMw';
    private $pushUrl = 'https://android.googleapis.com/gcm/send';

    public function __construct() {
        parent::__construct();
    }

    private function _pushMessage($pid, $message, $token, $development) {
        $headers = array(
            'Authorization: key=' . $this->apiKey,
            'Content-Type: application/json'
        );
        
        $fields = array(
            'registration_ids' => array($token),
            'data' => $message
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->pushUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        $result = curl_exec($ch);
        if ($result === FALSE) {
            $this->log('Curl failed: ' . curl_error($ch));
        } else {
            $result = json_decode($result);
            if (isset($result->results)) {
                foreach ($result->results as $v) {
                    if (isset($v->error)) {
                        $this->addError($v->error);
                    }
                }
            }
            if (isset($result->success) && $result->success == 1) {
                $this->_pushSuccess($pid);
            } else {
                $this->_pushFailed($pid);
            }
        }
        
        curl_close($ch);
    }
    
    public function prepareMessage($message) {
        return array(
            'message' => $message['message'],
            'badge' => $message['badge']
        );
    }

    public function pushMessage($pid, $message, $token, $development) {
        $this->_pushMessage($pid, $message, $token, $development);
    }
    
    public function pushTestMessage($email, $alert) {
        $devices = $this->getApproverDevice($email, 'android');
        if (count($devices) > 0) {
            foreach ($devices as $device) {
                $id = 0;
                $devicetoken = $device['device_token'];
                $development = 'production';
                $message = array('message' => $alert, 'badge' => 1);
                
                $this->_pushMessage($id, $message, $devicetoken, $development);
            }
            $result = 'DONE.<br/>';
            foreach ($this->getErrors() as $error) {
                $result .= $error['text'] . '<br/>';
            }
            return $result;
        } else {
            return 'There aren\'t any devices registered with this user.';
        }
    }

}
