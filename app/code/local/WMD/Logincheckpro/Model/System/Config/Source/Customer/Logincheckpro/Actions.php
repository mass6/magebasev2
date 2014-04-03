<?php
/**
 * WMD_Logincheckpro_Model_System_Config_Source_Customer_Logincheckpro_Actions  
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
class WMD_Logincheckpro_Model_System_Config_Source_Customer_Logincheckpro_Actions
{
    /**
     * Actions
     * @var array
     */		
    protected $_options;
	
    /**
     * Return the avaiable customer account actions
     * 
     * @param boolean $isMultiselect if Multiselect for the order status selection is allowed 
     *
     * @return array
     */	      
	  public function toOptionArray($isMultiselect=false)
    {		
		    if (!$this->_options) { 		    
            $this->_options = array(
                array('value' => '', 'label' => Mage::helper('logincheckpro')->__('Disable All')),
                array('value' => '/customer_account_create/', 'label' => Mage::helper('logincheckpro')->__('Customer Account Create')),
                array('value' => '/customer_account_forgotpassword/', 'label' => Mage::helper('logincheckpro')->__('Customer Account Forgot Password')),
//                 array('value' => '/customer_account_confirmation/', 'label' => Mage::helper('logincheckpro')->__('Customer Account Confirmation')),
            );
            return $this->_options;
        }		
    }
}