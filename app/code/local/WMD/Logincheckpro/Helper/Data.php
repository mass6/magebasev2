<?php
/**
 * WMD_Logincheckpro_Helper_Data
 *
 * WMD Web-Manufaktur/Digiswiss 
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that you find at http://wmdextensions.com/WMD-License-Community.txt
 *
 * @category  WMD
 * @package   WMD_Logincheckpro
 * @author    Dominik Wyss <info@wmdextensions.com>
 * @copyright 2011 Dominik Wyss | Digiswiss (http://www.digiswiss.ch)
 * @link      http://www.wmdextensions.com/
 * @license   http://wmdextensions.com/WMD-License-Community.txt
*/?>
<?php
class WMD_Logincheckpro_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    const CUSTOMER_ACCOUNT_CREATE = 'customer_account_create';
    
    const CUSTOMER_ACCOUNT_FORGOTPASSWORD = 'customer_account_forgotpassword';
    
    protected $_allowedActionNames;
    
    /**
     * return allowed actions. 
     * 
     * @return string
     */	
     
    protected function _returnAllowed()
    {
        if (!$this->_allowedActionNames) {
            $this->_allowedActionNames = Mage::getStoreConfig('logincheckpro/actions/allowed');
        }
        return $this->_allowedActionNames;
    } 
    
    /**
     * Check for customer_account_forgotpassword in allowed actions. 
     * 
     * @return boolean
     */	 
    public function returnTitle()
    {
        if (!$this->canCreateAccount())
        {
            return 'Login';
        }
        return 'Login or Create an Account'; 
    }
    
    /**
     * Check for customer_account_create in allowed actions. 
     * 
     * @return boolean
     */	 
    public function canCreateAccount()
    {
        if (strstr($this->_returnAllowed(), self::CUSTOMER_ACCOUNT_CREATE))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Check for customer_account_forgotpassword in allowed actions. 
     * 
     * @return boolean
     */	 
    public function canGetNewPassword()
    {
        if (strstr($this->_returnAllowed(), self::CUSTOMER_ACCOUNT_FORGOTPASSWORD))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Check for customer login. 
     * 
     * @return boolean
     */	 
    public function isLoggedIn()
    {
        return Mage::getSingleton( 'customer/session' )->isLoggedIn();
    }
    
    /**
     * Check for ip/hostname to be bypassed from login
     * 
     * @return boolean
     */
    public function isIpToBypass()
    {
        if ($bypassip = Mage::getStoreConfig('logincheckpro/bypassip/allow'))
        {
            $ipArr = explode("\n", $bypassip);
            $remoteIp = $this->getRemoteIp();
            if (in_array($remoteIp, $ipArr) || in_array(gethostbyaddr($remoteIp), $ipArr))
            {
                return true;
            }    
        }
        if ($bypassagent = Mage::getStoreConfig('logincheckpro/bypassagent/allow'))
        {
            $agentArr = explode(';', $bypassagent);
            $userAgent = (string)$_SERVER['HTTP_USER_AGENT'];
            foreach ($agentArr as $agent)
            {
                if (is_int(strpos($userAgent, trim($agent))))
                {
                    return true;
                }
            }    
        }         
        return false;
    } 
    
    /**
     * Check for ip not to be in reserved ranges
     * 
     * @return boolean
     */
    public function validip($ip) {
    
        if (!empty($ip) && ip2long($ip)!=-1) {
        
            $reserved_ips = array (
                array('0.0.0.0','0.255.255.255'),
                array('10.0.0.0','10.255.255.255'),
                array('127.0.0.0','127.255.255.255'),
                array('169.254.0.0','169.254.255.255'),
                array('172.16.0.0','172.31.255.255'),
                array('192.0.2.0','192.0.2.255'),
                array('192.168.0.0','192.168.255.255'),
                array('255.255.255.0','255.255.255.255')
            );
            
            foreach ($reserved_ips as $r) {
                $min = ip2long($r[0]);
                $max = ip2long($r[1]);
                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) 
                {
                    return false;
                }
            }
            
            return true;
        
        } else {
        
            return false;
            
        }
    }
    
    /**
    * Retrieve most appropriate remote ip
    *       
    * excluding reserved ips
    * 
    * @return string
    */
    function getRemoteIP() 
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) 
        {
            if ($this->validip($_SERVER["HTTP_CLIENT_IP"])) 
            {
                return $_SERVER["HTTP_CLIENT_IP"];
            }
        }                                     
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) 
        {
            foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) 
            {
                if ($this->validip(trim($ip))) 
                {
                    return $ip;
                }
            }
        }
        if (isset($_SERVER["HTTP_X_FORWARDED"]) && $this->validip($_SERVER["HTTP_X_FORWARDED"])) 
        {
            return $_SERVER["HTTP_X_FORWARDED"];
        } 
        elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]) && $this->validip($_SERVER["HTTP_FORWARDED_FOR"])) 
        {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } 
        elseif (isset($_SERVER["HTTP_FORWARDED"]) && $this->validip($_SERVER["HTTP_FORWARDED"])) 
        {
            return $_SERVER["HTTP_FORWARDED"];
        } 
        return $_SERVER["REMOTE_ADDR"];
    }
        
}