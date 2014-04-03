<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page common functionality block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Checkout_Block_Onepage_Abstract extends Mage_Core_Block_Template
{
    protected $_customer;
    protected $_checkout;
    protected $_quote;
    protected $_countryCollection;
    protected $_regionCollection;
    protected $_addressesCollection;

    /**
     * Get logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        if (empty($this->_checkout)) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    /**
     * Retrieve sales quote model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
	
        return $this->_quote;
    }

    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
                ->loadByStore();
        }
        return $this->_countryCollection;
    }

    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }

    public function customerHasAddresses()
    {
        return count($this->getCustomer()->getAddresses());
    }

/* */
    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }
			$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
			
				
			
			
			// Mage::log('$customerId===');
			// Mage::log($customerId);
			$collection = Mage::getModel('customer/customer')->getCollection()
				->addAttributeToSelect('*')
				->addFieldToFilter('entity_id', $customerId);
			$temId = '';
			foreach ($collection as $customer) {
			  $data = $customer->toArray();
			  foreach ($data as $key => $value) {
			   
				if ($key == 'contract_id') {
				  $temId = $value;
				}
			  }
			}
			// Mage::log('$temId===');
			// Mage::log($temId);
			$contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $temId);
			$contractCode = array();
			$contractCode1 = array();
			$contractCode2 = array();
			$contractCode3 = array();
			$contractCode4 = array();
			$contractCode5 = array();
			$tempShipppingAddress = array();
			// $contractCode2 = '';
			
			
			$country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
					 
			
			foreach ($contractDetails as $each) {
			
			if($each['street_bill']!= '' || $each['street1_bill']!= ''|| $each['city_bill']!= ''|| $each['country_bill']!= ''|| $each['state_bill']!= ''|| $each['zip_bill']!= ''){ 
			  $tempBill = '' ;
					foreach($country_list as $country){ 
							// $temp[]= $country['Please Select'] ;
							if($country['value'] == $each['country_bill'])
							$tempBill = $country['label'] ;

				}
			  if($each['name_bill']!= '')$contractCode[] = $each['name_bill'];
			  if($each['street_bill']!= '')$contractCode[] = $each['street_bill'];
			  if($each['street1_bill']!= '')$contractCode[] = $each['street1_bill'];
			  if($each['city_bill']!= '')$contractCode[] = $each['city_bill'];
			  if($each['country_bill']!= '0')$contractCode[] = $tempBill;
			  if($each['state_bill']!= '')$contractCode[] = $each['state_bill'];
			  if($each['zip_bill']!= '')$contractCode[] = $each['zip_bill'];
			  
			  }
			  // else{
			  // Mage::log('eneter else block add');
			
			  if($each['name_ship1']!= '' ||$each['street_ship1']!= '' || $each['street1_ship1']!= ''|| $each['city_ship1']!= ''|| $each['country_ship1']!= ''|| $each['state_ship1']!= ''|| $each['zip_ship1']!= ''){
Mage::log('enter ship1');			  
			  $tempShip1 = '' ;
					foreach($country_list as $country){ 
							// $temp[]= $country['Please Select'] ;
							if($country['value'] == $each['country_ship1'])
							$tempShip1 = $country['label'] ;

				}
				// unset ($country_list);
			  
			  if($each['name_ship1']!= '')$contractCode1[] = $each['name_ship1'];
			  if($each['street_ship1']!= '')$contractCode1[] = $each['street_ship1'];
			  if($each['street1_ship1']!= '')$contractCode1[] = $each['street1_ship1'];
			  if($each['city_ship1']!= '')$contractCode1[] = $each['city_ship1'];
			  if($each['country_ship1']!= '0')$contractCode1[] = $tempShip1;
			  if($each['state_ship1']!= '')$contractCode1[] = $each['state_ship1'];
			  if($each['zip_ship1']!= '')$contractCode1[] = $each['zip_ship1'];
			  
			  }
			  
			  
			  
			  if($each['name_ship2']!= '' ||$each['street_ship2']!= '' || $each['street1_ship2']!= ''|| $each['city_ship2']!= ''|| $each['country_ship2']!= ''|| $each['state_ship2']!= ''|| $each['zip_ship2']!= ''){ 
			  $tempShip2 = '' ;
					foreach($country_list as $country){ 
							// $temp[]= $country['Please Select'] ;
							if($country['value'] == $each['country_ship2'])
							$tempShip2 = $country['label'] ;

				}
				// unset ($country_list);
			  if($each['name_ship2']!= '')$contractCode2[] = $each['name_ship2'];
			  if($each['street_ship2']!= '')$contractCode2[] = $each['street_ship2'];
			  if($each['street1_ship2']!= '')$contractCode2[] = $each['street1_ship2'];
			  if($each['city_ship2']!= '')$contractCode2[] = $each['city_ship2'];
			  if($each['country_ship2']!= '0')$contractCode2[] = $tempShip2;
			  if($each['state_ship2']!= '')$contractCode2[] = $each['state_ship2'];
			  if($each['zip_ship2']!= '')$contractCode2[] = $each['zip_ship2'];
			  
			  }
			  
			  
			  
			  if($each['name_ship3']!= '' ||$each['street_ship3']!= '' || $each['street1_ship3']!= ''|| $each['city_ship3']!= ''|| $each['country_ship3']!= ''|| $each['state_ship3']!= ''|| $each['zip_ship3']!= ''){ 
			  $tempShip3 = '' ;
					foreach($country_list as $country){ 
							// $temp[]= $country['Please Select'] ;
							if($country['value'] == $each['country_ship3'])
							$tempShip3 = $country['label'] ;

				}
				// unset ($country_list);
			  if($each['name_ship3']!= '')$contractCode3[] = $each['name_ship3'];
			  if($each['street_ship3']!= '')$contractCode3[] = $each['street_ship3'];
			  if($each['street1_ship3']!= '')$contractCode3[] = $each['street1_ship3'];
			  if($each['city_ship3']!= '')$contractCode3[] = $each['city_ship3'];
			  if($each['country_ship3']!= '0')$contractCode3[] = $tempShip3;
			  if($each['state_ship3']!= '')$contractCode3[] = $each['state_ship3'];
			  if($each['zip_ship3']!= '')$contractCode3[] = $each['zip_ship3'];
			  
			  }
			  
			 
			 if($each['name_ship4']!= '' ||$each['street_ship4']!= '' || $each['street1_ship4']!= ''|| $each['city_ship4']!= ''|| $each['country_ship4']!= ''|| $each['state_ship4']!= ''|| $each['zip_ship4']!= ''){ 
			  $tempShip4 = '' ;
					foreach($country_list as $country){ 
							// $temp[]= $country['Please Select'] ;
							if($country['value'] == $each['country_ship4'])
							$tempShip4 = $country['label'] ;

				}
				// unset ($country_list);
			  if($each['name_ship4']!= '')$contractCode4[] = $each['name_ship4'];
			  if($each['street_ship4']!= '')$contractCode4[] = $each['street_ship4'];
			  if($each['street1_ship4']!= '')$contractCode4[] = $each['street1_ship4'];
			  if($each['city_ship4']!= '')$contractCode4[] = $each['city_ship4'];
			  if($each['country_ship4']!= '0')$contractCode4[] = $tempShip4;
			  if($each['state_ship4']!= '')$contractCode4[] = $each['state_ship4'];
			  if($each['zip_ship4']!= '')$contractCode4[] = $each['zip_ship4'];
			  
			  }
			  
			  
			   if($each['name_ship5']!= '' ||$each['street_ship5']!= '' || $each['street1_ship5']!= ''|| $each['city_ship5']!= ''|| $each['country_ship5']!= ''|| $each['state_ship5']!= ''|| $each['zip_ship5']!= ''){ 
			  $tempShip5 = '' ;
					foreach($country_list as $country){ 
							// $temp[]= $country['Please Select'] ;
							if($country['value'] == $each['country_ship5'])
							$tempShip5 = $country['label'] ;

				}
				// unset ($country_list);
			  if($each['name_ship5']!= '')$contractCode5[] = $each['name_ship5'];
			  if($each['street_ship5']!= '')$contractCode5[] = $each['street_ship5'];
			  if($each['street1_ship5']!= '')$contractCode5[] = $each['street1_ship5'];
			  if($each['city_ship5']!= '')$contractCode5[] = $each['city_ship5'];
			  if($each['country_ship5']!= '0')$contractCode5[] = $tempShip5;
			  if($each['state_ship5']!= '')$contractCode5[] = $each['state_ship5'];
			  if($each['zip_ship5']!= '')$contractCode5[] = $each['zip_ship5'];
			  
			  }
			 
			 
			 
			  // }
			}
			
			unset ($country_list);
			$tempBillingAddress	= implode(',',$contractCode);
			// Mage::log('$contractCode1=====');
			// Mage::log($contractCode1);
			$temp1= implode(',',$contractCode1);
			$temp2= implode(',',$contractCode2);
			$temp3= implode(',',$contractCode3);
			$temp4= implode(',',$contractCode4);
			$temp5= implode(',',$contractCode5);
			// Mage::log('$temp1----');
			// Mage::log($temp1);
			if($temp1!='')$tempShipppingAddress[1] = $temp1;
			if($temp2!='')$tempShipppingAddress[2] = $temp2;
			if($temp3!='')$tempShipppingAddress[3] = $temp3;
			if($temp4!='')$tempShipppingAddress[4] = $temp4;
			if($temp5!='')$tempShipppingAddress[5] = $temp5;
			
			
			$options1 = array();
			if($type=='billing'){
			 $options1[] = array(
                    'value' => 100,
                    'label' => $tempBillingAddress
                );
				}
				else{
				Mage::log('$tempShipppingAddressweqwe');
				Mage::log($tempShipppingAddress);
				foreach($tempShipppingAddress as $key=>$value){
				Mage::log('ashjkasdjkashdjkhdfhajks');
				Mage::log($key);
				Mage::log($value);
				$options1[] = array(
					'value' => 10000+$key,
					'label' => $value
					);
				}
				// for($i=0;$i<count($tempShipppingAddress); $i++){
					// $options1[] = array(
					// 'value' => 100+$i,
					// 'label' => $tempShipppingAddress[$i]
					// );
				}
				
				// Mage::log('$tempShipppingAddress===');
				// Mage::log($tempShipppingAddress);
				
				 // $options1[] = array(
                    // 'value' => 100,
                    // 'label' => $contractCode1
                // );
		// }
			
			// Mage::log('$contractCode=====');
			// Mage::log($contractCode);
		if(empty($tempBillingAddress) && $type=='billing' ){
		// Mage::log('enter if block');
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
				->setReadonly('readonly')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
               ->setValue($addressId)
                ->setOptions($options);
                 // ->setOptions($options1);
			}
			else if(empty($tempShipppingAddress) && $type=='shipping'){
			// Mage::log('enter else if  block');
				$select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
				->setReadonly('readonly')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
               ->setValue($addressId)
                ->setOptions($options);
			}
			else{
		// Mage::log('enter else block');
			 $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
				->setReadonly('readonly')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
               // ->setValue($addressId)
                // ->setOptions($options);
                 ->setOptions($options1);
			}
			
		
            // $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        if ($type === 'shipping') {
            $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
        }

        return $select->getHtml();
    }


    public function getRegionHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[region]')
            ->setId($type.':region')
            ->setTitle(Mage::helper('checkout')->__('State/Province'))
            ->setClass('required-entry validate-state')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray());

        return $select->getHtml();
    }

    public function getCountryOptions()
    {
        $options    = false;
        $useCache   = Mage::app()->useCache('config');
        if ($useCache) {
            $cacheId    = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
            $cacheTags  = array('config');
            if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                $options = unserialize($optionsCache);
            }
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            if ($useCache) {
                Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
            }
        }
        return $options;
    }

    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
        return array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
    }


    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return true;
    }
/* */
}
