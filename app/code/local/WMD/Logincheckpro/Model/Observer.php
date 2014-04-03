<?php
/**
 * WMD_Logincheckpro_Model_Observer  
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
class WMD_Logincheckpro_Model_Observer
{

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * Retrieve system config value
     *
     * @return mixed     
     */
    protected function _getConfigValue($path = 'general/enable')
    {
        return Mage::getStoreConfig('logincheckpro/' . $path);
    }              
    
    /**
     * Set redirect after auth if needed
     *
     * @return void      
     */              
    protected function _setRedirect()
    {
        if ($redirectUrl = $this->_getConfigValue('redirect/url'))
        {
            $session = $this->_getSession();
            $session->setAfterAuthUrl($redirectUrl);
        } 
    }
    
    /**
     * Customer session check for isLoggedIn.
     * Ignore allowed paths and allowed customer account actions.
     * Redirect all other requests to customer account login page.           
     * 
     * @param object $observer 
     *
     * @return object
     */	 
     
    public function checkLogin($observer)
  	{
    		if (1 == $this->_getConfigValue())
        {
            if (Mage::helper('logincheckpro')->isIpToBypass())
            {
                return $this;
            }               
            
            $isLoggedIn = $this->_getSession()->isLoggedIn();
            
            $allowedPath = false;
            // set the customer account action names you want users to be able to access whitout being logged in
        		$allowedPathInfos = $this->_getConfigValue('pages/allowed');
            
            $allowedAction = false;
            // set the cms pages url keys you want users to be able to access whitout being logged in 
            $allowedActionNames = $this->_getConfigValue('actions/allowed');
            
            // make sure the account login page remains accessible
            $allowedActionNames .= ',\'customer_account_login\',\'customer_account_confirm\',\'customer_account_resetpassword\'';  
            
            if (0 == $this->_getConfigValue('contacts/protect'))
            {
                $allowedPathInfos .= ',contacts'; 
                $allowedActionNames .= ',\'contacts_index_post\',\'contacts_index_index\'';   
            }
            
            // call event from observer
            $event = $observer->getEvent();
            // call action from event
        		$controller = $event->getAction();
            
            // get pathInfo of controller request
            $requestPathInfo = $controller->getRequest()->getPathInfo();
            
            // get FullActionName of controller request
            $fullActionName = $controller->getFullActionName();
                      
            // set allowedPath to true if allowedPathInfos contains the current PathInfo  
            if ($path = str_replace('/','', $requestPathInfo)) 
            {
                $allowedPath = is_int(strpos($allowedPathInfos, $path));
            }
            elseif ('/' == $requestPathInfo)
            {
                $allowedPath = is_int(strpos($allowedPathInfos, Mage::getStoreConfig('web/default/cms_home_page')));
            }
            
            // set allowedAction to true if allowedActionNames contains the current FullActionName
            $allowedAction = is_int(strpos($allowedActionNames, $fullActionName));
            
            if (!$allowedAction) 
            { 
                // get store config value for additional allowed modules actions
                if ($additionalAllowedAction = $this->_getConfigValue('additionalaction/allow'))
                {
                    if (is_int(strpos($additionalAllowedAction, ' ')))
                    {
                        $additionalAllowedActionArray = explode(' ', $additionalAllowedAction);
                    }
                    else
                    {
                        $additionalAllowedActionArray = array($additionalAllowedAction);
                    }  
                    foreach ($additionalAllowedActionArray as $value)
                    {
                        if ($allowedAction = is_int(strpos($fullActionName, $value)))
                        {
                            break;
                        }
                    }                            
                }
            } 
                                                                  
            if ('/customer/account/login/' == $requestPathInfo)
            { 
                // set redirect after login 
                $this->_setRedirect();                  
            }        
         		// redirect to account login: if there is no login, 
            // not an allowed cms page nor an allowed customer account action 
            if (!$isLoggedIn && !$allowedPath && !$allowedAction)		
        		{                                            
                $controller->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
            }
        }                  
    		return $this;
  	}    
}