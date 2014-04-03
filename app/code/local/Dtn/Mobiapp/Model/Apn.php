<?PHP

class Dtn_Mobiapp_Model_Apn extends Dtn_Mobiapp_Model_Abstract {

    private $apnsData;
    private $certificate = '';
    private $passphrase = '';
    private $ssl = 'ssl://gateway.push.apple.com:2195';
    private $feedback = 'ssl://feedback.push.apple.com:2196';
    private $sandboxCertificate = '';
    private $sandboxPassphrase = '';
    private $sandboxSsl = 'ssl://gateway.sandbox.push.apple.com:2195';
    private $sandboxFeedback = 'ssl://feedback.sandbox.push.apple.com:2196';
    protected $sslStreams;

    function __construct() {
        parent::__construct();
        $this->certificate = Mage::getModuleDir('data', 'Dtn_Mobiapp') . DS . 'MyApprovals_Push_Prod.pem';
        $this->sandboxCertificate = Mage::getModuleDir('data', 'Dtn_Mobiapp') . DS . 'MyApprovals_Push_Dev.pem';
        $this->passphrase = '123456';
        $this->sandboxPassphrase = '123456';
        $this->checkSetup();
        $this->apnsData = array(
            'production' => array(
                'certificate' => $this->certificate,
                'ssl' => $this->ssl,
                'feedback' => $this->feedback,
                'passphrase' => $this->passphrase
            ),
            'sandbox' => array(
                'certificate' => $this->sandboxCertificate,
                'ssl' => $this->sandboxSsl,
                'feedback' => $this->sandboxFeedback,
                'passphrase' => $this->sandboxPassphrase
            )
        );
    }

    private function checkSetup() {
        if (!file_exists($this->certificate))
            $this->addError('Missing Production Certificate.', E_USER_ERROR);
        if (!file_exists($this->sandboxCertificate))
            $this->addError('Missing Sandbox Certificate.', E_USER_ERROR);

        if (!isset($this->passphrase) || !isset($this->sandboxPassphrase))
            $this->addError('You need to specify the passphrase for the production and sandbox certificate.');

        $certificateMod = substr(sprintf('%o', fileperms($this->certificate)), -3);
        $sandboxCertificateMod = substr(sprintf('%o', fileperms($this->sandboxCertificate)), -3);

        if ($certificateMod > 644)
            $this->addError('Production Certificate is insecure! Suggest chmod 644.');
        if ($sandboxCertificateMod > 644)
            $this->addError('Sandbox Certificate is insecure! Suggest chmod 644.');
    }

    private function _connectSSLSocket($development) {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->apnsData[$development]['certificate']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->apnsData[$development]['passphrase']);
        $this->sslStreams[$development] = stream_socket_client($this->apnsData[$development]['ssl'], $error, $errorString, 100, (STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT), $ctx);
        if (!$this->sslStreams[$development]) {
            $this->log("Failed to connect to APNS: {$error} {$errorString}.");
            unset($this->sslStreams[$development]);
            return false;
        }
        return $this->sslStreams[$development];
    }

    private function _closeSSLSocket($development) {
        if (isset($this->sslStreams[$development])) {
            fclose($this->sslStreams[$development]);
            unset($this->sslStreams[$development]);
        }
    }

    private function _pushMessage($pid, $message, $token, $development) {
        if (strlen($pid) == 0)
            $this->log('Missing message pid.');
        if (strlen($message) == 0)
            $this->log('Missing message.');
        if (strlen($token) == 0)
            $this->log('Missing message token.');
        if (strlen($development) == 0)
            $this->log('Missing development status.');

        $fp = false;
        if (isset($this->sslStreams[$development])) {
            $fp = $this->sslStreams[$development];
        }

        if (!$fp) {
            $this->_pushFailed($pid);
            $this->log("A connected socket to APNS wasn't available.");
        } else {
            $expiry = time() + 120; // 2 minute validity hard coded!
            $msg = chr(1) . pack("N", $pid) . pack("N", $expiry) . pack("n", 32) . pack('H*', $token) . pack("n", strlen($message)) . $message;

            $fwrite = fwrite($fp, $msg);
            if (!$fwrite) {
                $this->_pushFailed($pid);
                $this->log("Failed writing to stream.");
                $this->_closeSSLSocket($development);
            } else {
                $tv_sec = 1;
                $tv_usec = null; // Timeout. 1 million micro seconds = 1 second
                $r = array($fp);
                $we = null; // Temporaries. "Only variables can be passed as reference."
                $numChanged = stream_select($r, $we, $we, $tv_sec, $tv_usec);
                if (false === $numChanged) {
                    $this->log("Failed selecting stream to read.");
                } else if ($numChanged > 0) {
                    $command = ord(fread($fp, 1));
                    $status = ord(fread($fp, 1));
                    $identifier = implode('', unpack("N", fread($fp, 4)));
                    $statusDesc = array(
                        0 => 'No errors encountered',
                        1 => 'Processing error',
                        2 => 'Missing device token',
                        3 => 'Missing topic',
                        4 => 'Missing payload',
                        5 => 'Invalid token size',
                        6 => 'Invalid topic size',
                        7 => 'Invalid payload size',
                        8 => 'Invalid token',
                        255 => 'None (unknown)',
                    );
                    $this->log("APNS responded with command($command) status($status) pid($identifier).");

                    if ($status > 0) {
                        // $identifier == $pid
                        $this->_pushFailed($pid);
                        $desc = isset($statusDesc[$status]) ? $statusDesc[$status] : 'Unknown';
                        $this->log("APNS responded with error for pid($identifier). status($status: $desc)");
                        // The socket has also been closed. Cause reopening in the loop outside.
                        $this->_closeSSLSocket($development);
                    } else {
                        // Apple docs state that it doesn't return anything on success though
                        $this->_pushSuccess($pid);
                    }
                } else {
                    $this->_pushSuccess($pid);
                }
            }
        }
    }

    private function _checkFeedback($development) {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->apnsData[$development]['certificate']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->apnsData[$development]['passphrase']);
        stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
        $fp = stream_socket_client($this->apnsData[$development]['feedback'], $error, $errorString, 100, (STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT), $ctx);

        if (!$fp)
            $this->log("Failed to connect to device: {$error} {$errorString}.");
        while ($devcon = fread($fp, 38)) {
            $arr = unpack("H*", $devcon);
            $rawhex = trim(implode("", $arr));
            $token = substr($rawhex, 12, 64);
            if (!empty($token)) {
                $this->unregisterDevice($token);
                $this->log("Unregistering Device Token: {$token}.");
            }
        }
        fclose($fp);
    }
    
    public function beforePush($development) {
        // Connect the socket the first time it's needed.
        if (!isset($this->sslStreams[$development])) {
            $this->_connectSSLSocket($development);
        }
    }
    
    public function prepareMessage($message) {
        return $message['message'];
    }
    
    public function pushMessage($pid, $message, $token, $development) {
        $this->_pushMessage($pid, $message, $token, $development);
    }
    
    public function afterPush() {
        // Close streams and check feedback service
        foreach ($this->sslStreams as $key => $socket) {
            $this->_closeSSLSocket($key);
            $this->_checkFeedback($key);
        }
    }

    public function pushTestMessage($email, $alert) {
        $devices = $this->getApproverDevice($email, 'ios');
        if (count($devices) > 0) {
            foreach ($devices as $device) {
                $id = 0;
                $devicetoken = $device['device_token'];
                $development = 'production';
                $message = array();
                $message['aps'] = array();
                $message['aps']['alert'] = (string) $alert;
                $message['aps']['badge'] = 1;
                $message = json_encode($message);
                // Connect the socket the first time it's needed.
                if (!isset($this->sslStreams[$development])) {
                    $this->_connectSSLSocket($development);
                }
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
