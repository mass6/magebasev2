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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product api V2
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Insync_Catalog_Model_Product_Api_V2 extends Mage_Catalog_Model_Product_Api {

	const ATTRIBUTE_CODE = 'tier_price';
	
	/**
	 * Retrieve list of products with basic info (id, sku, type, set, name ...)
	 *
	 * @param array $filters
	 * @param string|int $store
	 * @return array
	 */	
	public function items($filters = null, $store = null) {
		$collection = Mage::getModel('catalog/product')->getCollection()
				->addStoreFilter($this->_getStoreId($store))
				->addAttributeToSelect('name');

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

		if (!empty($preparedFilters)) {
			try {
				foreach ($preparedFilters as $field => $value) {
					if (isset($this->_filtersMap[$field])) {
						$field = $this->_filtersMap[$field];
					}

					$collection->addFieldToFilter($field, $value);
				}
			} catch (Mage_Core_Exception $e) {
				$this->_fault('filters_invalid', $e->getMessage());
			}
		}

		$result = array();

		foreach ($collection as $product) {
			$result[] = array(
				'product_id' => $product->getId(),
				'sku' => $product->getSku(),
				'name' => $product->getName(),
				'set' => $product->getAttributeSetId(),
				'type' => $product->getTypeId(),
				'category_ids' => $product->getCategoryIds(),
				'website_ids' => $product->getWebsiteIds()
			);
		}

		return $result;
	}

	/**
	 * Retrieve product info
	 *
	 * @param int|string $productId
	 * @param string|int $store
	 * @param stdClass $attributes
	 * @return array
	 */
	public function info($productId, $store = null, $attributes = null, $identifierType = null) {
		$product = $this->_getProduct($productId, $store, $identifierType);

		if (!$product->getId()) {
			$this->_fault('not_exists');
		}

		$result = array(// Basic product data
			'product_id' => $product->getId(),
			'sku' => $product->getSku(),
			'set' => $product->getAttributeSetId(),
			'type' => $product->getTypeId(),
			'categories' => $product->getCategoryIds(),
			'websites' => $product->getWebsiteIds()
		);

		$allAttributes = array();
		if (!empty($attributes->attributes)) {
			$allAttributes = array_merge($allAttributes, $attributes->attributes);
		}
		else {
			foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
				if ($this->_isAllowedAttribute($attribute, $attributes)) {
					$allAttributes[] = $attribute->getAttributeCode();
				}
			}
		}

		$_additionalAttributeCodes = array();
		if (!empty($attributes->additional_attributes)) {
			foreach ($attributes->additional_attributes as $k => $_attributeCode) {
				$allAttributes[] = $_attributeCode;
				$_additionalAttributeCodes[] = $_attributeCode;
			}
		}

		$_additionalAttribute = 0;
		foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
			if ($this->_isAllowedAttribute($attribute, $allAttributes)) {
				if (in_array($attribute->getAttributeCode(), $_additionalAttributeCodes)) {
					$result['additional_attributes'][$_additionalAttribute]['key'] = $attribute->getAttributeCode();
					$result['additional_attributes'][$_additionalAttribute]['value'] = $product->getData($attribute->getAttributeCode());
					$_additionalAttribute++;
				}
				else {
					$result[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
				}
			}
		}

		return $result;
	}

	/**
	 * Create new product.
	 *
	 * @param string $type
	 * @param int $set
	 * @param string $sku
	 * @param array $productData
	 * @param string $store
	 * @return int
	 */
	public function create($type, $set, $sku, $productData, $store = null) {
	
		if (!$type || !$set || !$sku) {
			$this->_fault('data_invalid');
		}

		/** @var $product Mage_Catalog_Model_Product */
		$product = Mage::getModel('catalog/product');
		$product->setStoreId($this->_getStoreId($store))
				->setAttributeSetId($set)
				->setTypeId($type)
				->setSku($sku);

		if (property_exists($productData, 'website_ids') && is_array($productData->website_ids)) {
			$product->setWebsiteIds($productData->website_ids);
		}

		if (property_exists($productData, 'additional_attributes')) {
			foreach ($productData->additional_attributes as $_attribute) {
				$_attrCode = $_attribute->key;
				$productData->$_attrCode = $_attribute->value;
			}
			unset($productData->additional_attributes);
		}

		foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
			$_attrCode = $attribute->getAttributeCode();

			if ($this->_isAllowedAttribute($attribute) && isset($productData->$_attrCode)) {
				if ($_attrCode == 'price') {
					$websitePriceCombination = array();
					$websiteCurrencyCombination = array();

					$websitesProductsPrices = explode('@', $productData->$_attrCode);
					$countPrice = 0;
					$baseCurrency = Mage::app()->getBaseCurrencyCode();

					foreach ($websitesProductsPrices as $websiteProductPrice) {
						$rowData = explode('^', $websiteProductPrice);

						if ($countPrice == 0) {
							$defaultPrice = $rowData[1];
							$defaultCurrency = strtoupper($rowData[2]);
						}
						$countPrice++;

						$websitePriceCombination[$rowData[0]] = $rowData[1];
						$websiteCurrencyCombination[$rowData[0]] = strtoupper($rowData[2]);
					}

					if ($defaultCurrency != $baseCurrency && $defaultPrice != null) {
						$objDirCurrency = new Mage_Directory_Model_Currency();
						$arrAllowedCurrencies = $objDirCurrency->getConfigAllowCurrencies();

						try {
							$arrCurrencyRates = $objDirCurrency->getCurrencyRates($baseCurrency, array_values($arrAllowedCurrencies));

							if (count($arrCurrencyRates)) {
								$defaultPrice = round(floatval($defaultPrice) / floatval($arrCurrencyRates[$defaultCurrency]), 2);
							}
						} catch (Mage_Core_Exception $e) {
							$this->_fault('data_invalid', $e->getMessage());
						}
					}

					$product->setData($_attrCode, $defaultPrice);
					continue;
				}else if($attribute->getAttributeCode() == 'special_price' 
						|| $attribute->getAttributeCode() == 'special_from_date'
						|| $attribute->getAttributeCode() == 'special_to_date'
						|| $attribute->getAttributeCode() == 'tier_price'){
					continue;
				}

				$product->setData($_attrCode, $productData->$_attrCode);
			}
		}

		$this->_prepareDataForSave($product, $productData);

		try {
			/**
			 * @todo implement full validation process with errors returning which are ignoring now
			 * @todo see Mage_Catalog_Model_Product::validate()
			 */
			if (is_array($errors = $product->validate())) {
				$strErrors = array();
				foreach ($errors as $code => $error) {
					$strErrors[] = ($error === true) ? Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code) : $error;
				}
				$this->_fault('data_invalid', implode("\n", $strErrors));
			}

			$product->setData('sap_sync', 1);
			$product->save();
				if (count($websitePriceCombination) && count($websiteCurrencyCombination)) {
				$this->_setWebsiteProductPrice($product->getId(), $websitePriceCombination, $websiteCurrencyCombination);
			}
			
		} catch (Mage_Core_Exception $e) {
			$this->_fault('data_invalid', $e->getMessage());
		}
		$id = $product->getId();
		unset($product);
		return $id;
	}

	/**
	 * Update product data
	 *
	 * @param int|string $productId
	 * @param array $productData
	 * @param string|int $store
	 * @return boolean
	 */
	public function update($productId, $productData, $store = null, $identifierType = null) {
		Mage::log('$productData start ------------- ');
		Mage::log($productId);
		Mage::log($productData);
		Mage::log('end data--------------');
		$mstore = $store;
		
		if (property_exists($productData, 'website_ids') && is_array($productData->website_ids)) {
			$_websiteIds = $productData->website_ids;
		}else {
			$this->_fault('data_invalid', "No Website IDs available");
		}
		
		$defaultname=''; $defaultshortdesc=''; $defaultdesc='';
		$objSapConfig = new Insync_Sap_Model_Config();
		$product = $objSapConfig->_getProduct($productId, Mage_Core_Model_App::ADMIN_STORE_ID, $identifierType);
		$defaultname = $product->getName();
		$defaultdesc = $product->getData('description');
		$defaultshortdesc = $product->getData('short_description');
		unset($objSapConfig, $product);
		
		if($store == null)$store =Mage_Core_Model_App::ADMIN_STORE_ID;
		$objSapConfig = new Insync_Sap_Model_Config();
		$product = $objSapConfig->_getProduct($productId, $store, $identifierType);
		$productwebsites = $product->getWebsiteIds();
		unset($objSapConfig);

		
		if (property_exists($productData, 'additional_attributes')) {
			foreach ($productData->additional_attributes as $_attribute) {
				$_attrCode = $_attribute->key;
				$productData->$_attrCode = $_attribute->value;
			}
			unset($productData->additional_attributes);
		}

		foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
			$_attrCode = $attribute->getAttributeCode();
			if ($this->_isAllowedAttribute($attribute) && isset($productData->$_attrCode)) {
				if ($attribute->getAttributeCode() == 'price') {
					$websitePriceCombination = array();
					$websiteCurrencyCombination = array();

					$websitesProductsPrices = explode('@', $productData->$_attrCode);
					$countPrice = 0;
					$baseCurrency = Mage::app()->getBaseCurrencyCode();

					foreach ($websitesProductsPrices as $websiteProductPrice) {
						$rowData = explode('^', $websiteProductPrice);

						if ($countPrice == 0) {
							$defaultPrice = $rowData[1];
							$defaultCurrency = strtoupper($rowData[2]);
						}
						$countPrice++;

						$websitePriceCombination[$rowData[0]] = $rowData[1];
						$websiteCurrencyCombination[$rowData[0]] = strtoupper($rowData[2]);
					}

					if ($defaultCurrency != $baseCurrency && $defaultPrice != null) {
						$objDirCurrency = new Mage_Directory_Model_Currency();
						$arrAllowedCurrencies = $objDirCurrency->getConfigAllowCurrencies();

						try {
							$arrCurrencyRates = $objDirCurrency->getCurrencyRates($baseCurrency, array_values($arrAllowedCurrencies));

							if (count($arrCurrencyRates)) {
								$defaultPrice = round(floatval($defaultPrice) / floatval($arrCurrencyRates[$defaultCurrency]), 2);
							}
						} catch (Mage_Core_Exception $e) {
							$this->_fault('data_invalid', $e->getMessage());
						}
					}

					$product->setData($attribute->getAttributeCode(), $defaultPrice);
					continue;
				} else if ($attribute->getAttributeCode() == 'visibility') {
					continue;
				}

				$product->setData(
						$attribute->getAttributeCode(),
						$productData->$_attrCode
				);
			}
		}

		$this->_prepareDataForSave($product, $productData);

		try {
			/**
			 * @todo implement full validation process with errors returning which are ignoring now
			 * @todo see Mage_Catalog_Model_Product::validate()
			 */
			if (is_array($errors = $product->validate())) {
				$strErrors = array();
				foreach ($errors as $code => $error) {
					$strErrors[] = ($error === true) ? Mage::helper('catalog')->__('Value for "%s" is invalid.', $code) : Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
				}
				$this->_fault('data_invalid', implode("\n", $strErrors));
			}

			$product->setData('sap_sync', 1);
			$product->save();

			if (count($websitePriceCombination) && count($websiteCurrencyCombination)) {
				$this->_setWebsiteProductPrice($product->getId(), $websitePriceCombination, $websiteCurrencyCombination);
			}
		} catch (Mage_Core_Exception $e) {
			$this->_fault('data_invalid', $e->getMessage());
		}
		//Mage::log(Mage_Core_Model_App::ADMIN_STORE_ID);
		
		$objSapConfig = new Insync_Sap_Model_Config();
		$product = $objSapConfig->_getProduct($productId, $this->_getStoreId($store), $identifierType);
		$product->setName($pname);
		$product->setData('name',$productData->name);
		$product->setData('description',$productData->description);
		$product->setData('short_description',$productData->short_description);
		$product->setData('tax_class_id',$productData->tax_class_id);
		// $product->setData('visibility',$productData->visibility);
		$product->setData('status',$productData->status);
		$product->save();
		
		// website merge only for multipule company
		$result = array_merge($productwebsites, $product->getWebsiteIds());
		$product->setWebsiteIds($result);
		unset($product);
		
		if($mstore==null || $mstore==''){
			$stores = Mage::getModel('core/store')->getCollection();
			foreach ($stores as $store) {
				$product = $objSapConfig->_getProduct($productId, ($store->getId()), $identifierType);
				$product->setName($pname);
				$product->setData('name',$productData->name);
				$product->setData('description',$productData->description);
				$product->setData('short_description',$productData->short_description);
				$product->setData('tax_class_id',$productData->tax_class_id);
				// $product->setData('visibility',$productData->visibility);
				$product->setData('status',$productData->status);
				$product->save();
				unset($product);
			}
			
		}else{
			// for default
			$product = $objSapConfig->_getProduct($productId, Mage_Core_Model_App::ADMIN_STORE_ID, $identifierType);
			if(trim($productData->name)=='' )$product->setData('name',$defaultname);
			if(trim($productData->description)=='' )$product->setData('description',$defaultdesc);
			if(trim($productData->short_description)=='' )$product->setData('short_description',$defaultshortdesc);

			$product->save();
			unset($product);
		}
		unset($objSapConfig);
		return true;
	}
	
	/**
	 *  Set additional data before product saved
	 *
	 *  @param    Mage_Catalog_Model_Product $product
	 *  @param    array $productData
	 *  @return	  object
	 */
	protected function _prepareDataForSave($product, $productData) {
		// Mage::log('$productData================');
		// Mage::log($productData);
		if (property_exists($productData, 'website_ids') && is_array($productData->website_ids)) {
			// Mage::log('$productData->website_ids=============');
			// Mage::log($productData->website_ids);
			// $_websiteIds = $productData->website_ids;
			$product->setWebsiteIds($productData->website_ids);
		}
		else {
			$this->_fault('data_invalid', "No Website IDs available");
		}
		if (property_exists($productData, 'category_ids')) {
			$product->setCategoryIds($productData->category_ids);
		}

		if (Mage::app()->isSingleStoreMode()) {
			$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
		}

		if (property_exists($productData, 'stock_data')) {
			$_stockData = array();
			foreach ($productData->stock_data as $key => $value) {
				$_stockData[$key] = $value;
			}
		}
		else {
			$_stockData = array('use_config_manage_stock' => 1);
		}
		$product->setStockData($_stockData);
		//$product->save();
		// if (property_exists($productData, self::ATTRIBUTE_CODE)) {
			// $tierPrices = Mage::getModel('catalog/product_attribute_tierprice_api_V2')->prepareTierPrices($product, $productData->tier_price);
			// $product->setData(Mage_Catalog_Model_Product_Attribute_Tierprice_Api_V2::ATTRIBUTE_CODE, $tierPrices);
		// }
	}

	/**
	 * Sets the Custom Options of a Product
	 * Working of Custom Options, with "Price Conversion" and "Use Default Values" features, are known to create problems,
	 * as for each feature, same set of Custom Options seems to get duplicated.
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param object|array $customOptions
	 */
	protected function _setCustomOptions(Mage_Catalog_Model_Product $product, $customOptions = array()) {
		if ($product->getId()) {
			$productOptions = $product->getOptions();
			$oldProductOptions = array();

			if (count($productOptions)) {
				Insync_Sap_Model_Config::log("product options inside the set method");
				foreach ($productOptions as $_eachOption) {
					$temp = $_eachOption->getData();
					$temp['is_delete'] = 1;

					$oldProductOptions[] = $temp;
					unset($temp);
				}
			}
			Insync_Sap_Model_Config::log($oldProductOptions);
		}

		if (count($customOptions)) {
			$data = json_decode(json_encode($customOptions), true);

			if (isset($oldProductOptions) && is_array($oldProductOptions)) {
				$data = array_merge($data, $oldProductOptions);
				Insync_Sap_Model_Config::log("inside array merge, just after merging");
			}
			Insync_Sap_Model_Config::log("data merged inside the set method");
			Insync_Sap_Model_Config::log($data);

			$product->setProductOptions($data);
			$product->setCanSaveCustomOptions(true);
		}
	}

	/**
	 * Update Product Special Price
	 * Fully Customized with Consideration of each Website instead of each Store View
	 *
	 * @param int|string $productId
	 * @param float $specialPrice
	 * @param string $fromDate
	 * @param string $toDate
	 * @param string|int $store
	 * @param string $currencyCode
	 * @return boolean
	 * Insync Development Team
	 */
	public function setSpecialPrice($productId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null, $identifierType = null, $currencyCode = null) {
		$objSapConfig = new Insync_Sap_Model_Config();
		$objProduct = $objSapConfig->_getProduct($productId, null, $identifierType);

		if (!$objProduct->getId()) {
			$this->_fault('not_exists');
		}

		if (is_null($store) || empty($store)) {
			$websiteIds = $objProduct->getWebsiteIds();
		}
		else {
			$websiteIds = array($store);
		}
		$websiteIds = array($store);

		if (!count($websiteIds)) {
			$this->_fault('data_invalid', "No Website IDs available for this Product, when Converting Special Price");
		}

		if (is_null($currencyCode)) {
			$currencyCode = Mage::app()->getBaseCurrencyCode();
		}
		$currencyCode = strtoupper($currencyCode);

		$objDirCurrency = Mage::getModel('directory/currency');
		$arrAllowedCurrencies = $objDirCurrency->getConfigAllowCurrencies();

		$flag = false;
		foreach ($websiteIds as $_eachWebsiteId) {
			$objWebsite = new Mage_Core_Model_Website();
			$objWebsite->load($_eachWebsiteId);
			$storeIds = $objWebsite->getStoreIds();

			$websiteBaseCurrencyCode = $objWebsite->getBaseCurrency()->getCode();
			if (empty($websiteBaseCurrencyCode)) {
				$websiteBaseCurrencyCode = Mage::app()->getBaseCurrencyCode();
			}

			$arrCurrencyRates = $objDirCurrency->getCurrencyRates($websiteBaseCurrencyCode, array_values($arrAllowedCurrencies));

			$currencyRate = '1';
			if (count($arrCurrencyRates)) {
				$currencyRate = $arrCurrencyRates[$currencyCode];
			}

			$newSpecialPrice = $specialPrice;

			try {
				$newSpecialPrice = round(floatval($specialPrice) / floatval($currencyRate), 2);
			} catch (Mage_Core_Exception $e) {
				$this->_fault('data_invalid', $e->getMessage());
			}

			$objWebsite = new Mage_Core_Model_Website();
			$objWebsite->load($_eachWebsiteId);
			//$storeIds = $objWebsite->getStoreIds();
			$storeIds[count($storeIds) + 1] = 0;
			foreach ($storeIds as $storeIds1) {
				$product = Mage::getModel('catalog/product')
						->setStoreId($storeIds1)
						->load($objProduct->getId());
				$product->setData('special_price', $newSpecialPrice);
				$product->setData('special_from_date', $fromDate);
				$product->setData('special_to_date', $toDate);
				$product->save();
				unset($product);
			}
			//}

			unset($objWebsite, $storeIds, $websiteBaseCurrencyCode, $arrCurrencyRates, $currencyRate, $newSpecialPrice, $product);

			$flag = true;
		}

		return $flag;
	}

	/**
	 * Retrieve product special price
	 *
	 * @param int|string $productId
	 * @param string|int $store
	 * @return array
	 */
	public function getSpecialPrice($productId, $store = null) {
		return $this->info($productId, $store, array(
			'attributes' => array(
				'special_price',
				'special_from_date',
				'special_to_date'
			)
				)
		);
	}

	/**
	 * Set product price with all website view
	 * @param type $productId
	 * @param type $websitePriceArray
	 * @param type $websiteCurrencyCombination 
	 */
	private function _setWebsiteProductPrice($productId, $websitePriceArray, $websiteCurrencyCombination) {
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));

		$objDirCurrency = Mage::getModel('directory/currency');
		$arrAllowedCurrencies = $objDirCurrency->getConfigAllowCurrencies();

		foreach ($websitePriceArray as $websiteId => $price) {
			$websiteObj = Mage::getModel('core/website')->load($websiteId);

			$websiteBaseCurrencyCode = $websiteObj->getBaseCurrency()->getCode();
			if (empty($websiteBaseCurrencyCode)) {
				$websiteBaseCurrencyCode = Mage::app()->getBaseCurrencyCode();
			}

			if ($websiteCurrencyCombination[$websiteId] != $websiteBaseCurrencyCode && $websiteCurrencyCombination[$websiteId] != NULL) {
				$arrCurrencyRates = $objDirCurrency->getCurrencyRates($websiteBaseCurrencyCode, array_values($arrAllowedCurrencies));

				$currencyRate = '1';
				if (count($arrCurrencyRates)) {
					$currencyRate = $arrCurrencyRates[$websiteCurrencyCombination[$websiteId]];
				}

				try {
					$price = round(floatval($price) / floatval($currencyRate), 2);
				} catch (Mage_Core_Exception $e) {
					$this->_fault('data_invalid', $e->getMessage());
				}

				unset($arrCurrencyRates, $currencyRate);
			}

			$storeIds = $websiteObj->getStoreIds();
			$product = Mage::getModel('catalog/product')
					->setStoreId(end($storeIds))
					->load($productId);
			$product->setData('price', $price);
			$product->save();

			unset($websiteObj, $websiteBaseCurrencyCode, $storeIds, $product);
		}
	}

	/**
	 * Update tier prices of product
	 *
	 * @param int|string $productId
	 * @param array $tierPrices
	 * @return boolean
	 */
	public function updateTierPrice($productId, $tierPrices, $identifierType = null) {
		$product = $this->_initProduct($productId, $identifierType);

		$updatedTierPrices = $this->prepareTierPrices($product, $tierPrices);

		Mage::log($updatedTierPrices);
		/**
		 * Start of Customization for Currency Conversion
		 * Insync Development Team
		 */
		$tempTierPrices = array();
		

		$objDirCurrency = new Mage_Directory_Model_Currency();
		$arrAllowedCurrencies = $objDirCurrency->getConfigAllowCurrencies();

		foreach ($updatedTierPrices as $value) {
			$currencyCode = strtoupper($value['currencycode']);
			$websiteObj = new Mage_Core_Model_Website();
			$websiteObj->load($value['website_id']);

			$websiteBaseCurrencyCode = $websiteObj->getBaseCurrency()->getCode();
			if (empty($websiteBaseCurrencyCode)) {
				$websiteBaseCurrencyCode = Mage::app()->getBaseCurrencyCode();
			}

			$arrCurrencyRates = $objDirCurrency->getCurrencyRates($websiteBaseCurrencyCode, array_values($arrAllowedCurrencies));

			$currencyRate = '1';
			if (count($arrCurrencyRates)) {
				$currencyRate = $arrCurrencyRates[$currencyCode];
			}

			try {
				$value['price'] = round(floatval($value['price']) / floatval($currencyRate), 2);
			} catch (Mage_Core_Exception $e) {
				$this->_fault('data_invalid', $e->getMessage());
			}

			$tempTierPrices[] = array(
				'website_id' => $value['website_id'],
				'cust_group' => $value['cust_group'],
				'price_qty' => $value['price_qty'],
				'price' => $value['price']
			);

			unset($websiteObj, $websiteBaseCurrencyCode, $arrCurrencyRates, $currencyRate);
		}
		Mage::log('1111');
		$updatedTierPrices = $tempTierPrices;
		/**
		 * End of Customization
		 */
		if (is_null($updatedTierPrices)) {
			$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
		}

		Mage::log('-------------------------------------------2');
		$product->setData(self::ATTRIBUTE_CODE, $updatedTierPrices);
		try {
			/**
			 * @todo implement full validation process with errors returning which are ignoring now
			 * @todo see Mage_Catalog_Model_Product::validate()
			 */
			if (is_array($errors = $product->validate())) {
				$strErrors = array();
				foreach ($errors as $code => $error) {
					$strErrors[] = ($error === true) ? Mage::helper('catalog')->__('Value for "%s" is invalid.', $code) : Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
				}
				$this->_fault('data_invalid', implode("\n", $strErrors));
			}

			$product->save();
		} catch (Mage_Core_Exception $e) {
			$this->_fault('not_updated', $e->getMessage());
		}

		return true;
	}

	/**
	 * Update tier prices of product
	 *
	 * @param int|string $productId
	 * @param array $tierPrices
	 * @return boolean
	 */
	public function insertTierPrice($productId, $tierPrices, $identifierType = null) {
		$product = $this->_initProduct($productId, $identifierType);

		$updatedTierPrices = $this->prepareTierPrices($product, $tierPrices);

		// Mage::log($updatedTierPrices);
		/**
		 * Start of Customization for Currency Conversion
		 * Insync Development Team
		 */
		$tempTierPrices = array();
		

		$objDirCurrency = new Mage_Directory_Model_Currency();
		$arrAllowedCurrencies = $objDirCurrency->getConfigAllowCurrencies();

		foreach ($updatedTierPrices as $value) {
			$currencyCode = strtoupper($value['currencycode']);
			$websiteObj = new Mage_Core_Model_Website();
			$websiteObj->load($value['website_id']);

			$websiteBaseCurrencyCode = $websiteObj->getBaseCurrency()->getCode();
			if (empty($websiteBaseCurrencyCode)) {
				$websiteBaseCurrencyCode = Mage::app()->getBaseCurrencyCode();
			}

			$arrCurrencyRates = $objDirCurrency->getCurrencyRates($websiteBaseCurrencyCode, array_values($arrAllowedCurrencies));

			$currencyRate = '1';
			if (count($arrCurrencyRates)) {
				$currencyRate = $arrCurrencyRates[$currencyCode];
			}

			try {
				$value['price'] = round(floatval($value['price']) / floatval($currencyRate), 2);
			} catch (Mage_Core_Exception $e) {
				$this->_fault('data_invalid', $e->getMessage());
			}

			$tempTierPrices[] = array(
				'website_id' => $value['website_id'],
				'cust_group' => $value['cust_group'],
				'price_qty' => $value['price_qty'],
				'price' => $value['price']
			);

			unset($websiteObj, $websiteBaseCurrencyCode, $arrCurrencyRates, $currencyRate);
		}
		foreach($product->getData('tier_price') as $data){
			$found=false;
			foreach($tempTierPrices as $senddata){
				if($senddata['website_id'].$senddata['cust_group'].$senddata['price_qty']
					== $data['website_id'].$data['cust_group'].$data['price_qty']){
					$found=true;
					break;
				}
			}
			if($found==false){
				$tempTierPrices[] = array(
					'website_id' => $data['website_id'],
					'cust_group' => $data['cust_group'],
					'price_qty' => $data['price_qty'],
					'price' => $data['price']
				);
			}
		}
		$updatedTierPrices = $tempTierPrices;
		
		
		/**
		 * End of Customization
		 */
		if (is_null($updatedTierPrices)) {
			$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
		}

		$product->setData(self::ATTRIBUTE_CODE, $updatedTierPrices);
		$product->save();
		return true;
	}

	/**
	 *  Prepare tier prices for save
	 *
	 *  @param      Mage_Catalog_Model_Product $product
	 *  @param      array $tierPrices
	 *  @return     array
	 */
	public function prepareTierPrices($product, $tierPrices = null) {
		if (!is_array($tierPrices)) {
			return null;
		}

		if (!is_array($tierPrices)) {
			$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
		}

		$updateValue = array();

		foreach ($tierPrices as $tierPrice1) {
			$tierPrice = (array)$tierPrice1;
			if (!is_array($tierPrice)
					|| !isset($tierPrice['qty'])
					|| !isset($tierPrice['price'])) {
				$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
			}

			if (!isset($tierPrice['website']) || $tierPrice['website'] == 'all') {
				$tierPrice['website'] = 0;
			}else {
				try {
					$tierPrice['website'] = Mage::app()->getWebsite($tierPrice['website'])->getId();
				} catch (Mage_Core_Exception $e) {
					$tierPrice['website'] = 0;
				}
			}

			if (intval($tierPrice['website']) > 0 && !in_array($tierPrice['website'], $product->getWebsiteIds())) {
				$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid tier prices. The product is not associated to the requested website.'));
			}

			if (!isset($tierPrice['customer_group_id'])) {
				$tierPrice['customer_group_id'] = 'all';
			}

			if ($tierPrice['customer_group_id'] == 'all') {
				$tierPrice['customer_group_id'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
			}
			
			if (!isset($tierPrice['currencycode'])) {
				$tierPrice['currencycode'] = Mage::app()->getBaseCurrencyCode();
			}

			$updateValue[] = array(
				'website_id' => $tierPrice['website'],
				'cust_group' => $tierPrice['customer_group_id'],
				'price_qty' => $tierPrice['qty'],
				'price' => $tierPrice['price'],
				'currencycode' => $tierPrice['currencycode']
			);
		}

		return $updateValue;
	}
	
	/**
	 * Return tierprice information on specific product
	 * @param type $productId
	 * @param type $identifierType
	 * @return type 
	 */
	public function tierPriceInfo($productId, $identifierType = null) {
		$product = $this->_initProduct($productId, $identifierType);
		$tierPrices = $product->getData(self::ATTRIBUTE_CODE);

		if (!is_array($tierPrices)) {
			return array();
		}

		$result = array();

		foreach ($tierPrices as $tierPrice) {
			$row = array();
			$row['customer_group_id'] = (empty($tierPrice['all_groups']) ? $tierPrice['cust_group'] : 'all' );
			$row['website'] = ($tierPrice['website_id'] ?
							Mage::app()->getWebsite($tierPrice['website_id'])->getCode() :
							'all'
					);
			$row['qty'] = $tierPrice['price_qty'];
			$row['price'] = $tierPrice['price'];
			$row['currencycode'] = Mage::app()->getBaseCurrencyCode();
			
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Retrieve product
	 *
	 * @param int $productId
	 * @param string|int $store
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _initProduct($productId, $identifierType = null) {
		try{
		
			$objSapConfig = new Insync_Sap_Model_Config();
			$product = $objSapConfig->_getProduct($productId, Mage_Core_Model_App::ADMIN_STORE_ID, $identifierType);

			if (!$product->getId()) {
				$this->_fault('product_not_exists');
			}

			return $product;
		}catch(Exception $ex){
			Mage::log($ex->getMessage());
		}
	}
	
	/**
     * Remove image from product
     *
     * @param int|string $productId
     * @param string $file
     * @return boolean
     */
    public function removeImage($productId, $file, $identifierType = null){
        $product = $this->_initProduct($productId, $identifierType);
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$sql = "delete from catalog_product_entity_media_gallery where entity_id=" . $product->getId() . 
				" and value='" . $file ."'";
		Mage::log('-----------$sql--------------------------------');
		Mage::log($sql);
		$write->query($sql);
		unset($product, $write);
        return true;
    }
	
	/**
	 * Insert image on specific product
	 * @param type $productId
	 * @param type $data
	 * @param type $store
	 * @param null $identifierType
	 * @return type 
	 */
	public function createImage($productId, $data, $store = null, $identifierType = null) {
		$obj = new Insync_Catalog_Model_Product_Attribute_Media_Api();
		return $obj->create($productId, $data, $store, $identifierType = null);
	}
	
	/**
     * Retrieve gallery attribute from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute|boolean
     */
    protected function _getGalleryAttribute($product){
        $attributes = $product->getTypeInstance(true)
            ->getSetAttributes($product);

        if (!isset($attributes['media_gallery'])) {
            $this->_fault('not_media');
        }

        return $attributes['media_gallery'];
    }
}
