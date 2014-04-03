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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer api V2
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Insync_Customer_Model_Customer_Api_V2 extends Insync_Customer_Model_Customer_Api {

	/**
	 * Prepare data to insert/update.
	 * Creating array for stdClass Object
	 *
	 * @param stdClass $data
	 * @return array
	 */
	protected function _prepareData($data) {
		if (null !== ($_data = get_object_vars($data))) {
			return $_data;
		}
		return array();
	}

	/**
	 * Create new customer
	 *
	 * @param array $customerData
	 * @return int
	 */
	public function create($customerData) {
		$customerData = $this->_prepareData($customerData);
		try {
			$customer = Mage::getModel('customer/customer')
					->setData($customerData)
					->save();
		} catch (Mage_Core_Exception $e) {
			$this->_fault('data_invalid', $e->getMessage());
		}
		return $customer->getId();
	}

	/**
	 * Retrieve cutomers data
	 *
	 * @param  array $filters
	 * @return array
	 */
	public function items($filters) {
		$preparedFilters = array();
		if (isset($filters->filter)) {
			foreach ($filters->filter as $_filter) {
				$preparedFilters[$_filter->key] = $_filter->value;
			}
		}
		if (isset($filters->complex_filter)) {
			foreach ($filters->complex_filter as $_filter) {
				$_value = $_filter->value;
				$preparedFilters[$_filter->key] = array(
					$_value->key => $_value->value
				);
			}
		}

		$websiteid = array();
		if (!empty($preparedFilters)) {
			foreach ($preparedFilters as $field => $value) {
				if ($field == 'website_id') {
					$websiteid = $value;
					break;
				}
			}
		}

		if (is_array($websiteid)) {
			foreach ($websiteid as $key => $value) {
				if ($key == 'in') {
					$websiteid = explode(',', $value);
				} else {
					$websiteid = array($value);
				}
			}
		}
		$collection = Mage::getModel('customer/customer')->getCollection()
				->addAttributeToSelect('*')
				->addFieldToFilter('website_id',array ('in' => $websiteid));
		$collection->getSelect()
				->where("SapSync = '0'");
		Mage::log($collection->getSelectSql(true));
				
		$result = array();
		foreach ($collection as $customer) {
			$data = $customer->toArray();
			$row = array();

			foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
				$row[$attributeAlias] = (isset($data[$attributeCode]) ? $data[$attributeCode] : null);
			}

			foreach ($this->getAllowedAttributes($customer) as $attributeCode => $attribute) {
				if (isset($data[$attributeCode])) {
					$row[$attributeCode] = $data[$attributeCode];
				}
			}

			$result[] = $row;
		}
		return $result;
	}

	/**
	 * Update customer data
	 *
	 * @param int $customerId
	 * @param array $customerData
	 * @return boolean
	 */
	public function update($customerId, $customerData) {
		$customer = Mage::getModel('customer/customer')->load($customerId);
		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = sprintf("SELECT  `entity_id` FROM  `customer_address_entity` WHERE  `parent_id` ='%d'", $customerId);
		$temp = $read->fetchAll($sql);
		//$tempId = array();
		foreach ($temp as $key) {
			foreach ($key as $key1 => $value) {
				if ($key1 = 'entity_id') {
				    $this->delete($value);
					// $tempId[] = $value;
					// Mage::log('$tempId--------------');
					// Mage::log($value);
				}
			}
		}
		if (!$customer->getId()) {
			$this->_fault('not_exists');
		}

		foreach ($this->getAllowedAttributes($customer) as $attributeCode => $attribute) {
			if (isset($customerData->$attributeCode)) {
				$customer->setData($attributeCode, $customerData->$attributeCode);
			}
		}

		$customer->save();
		return true;
	}

	
	
	   public function delete($addressId)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $address->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }
	
	
	
}
