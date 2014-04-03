<?php

class Insync_Sap_Model_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product {

	var $data0;
	var $attributedata0;
	public $options = array();
	public $values = array();
	public $dataCustomOptions;
	/**
	 * Required for the Fix of Character Limitations
	 */
	private $_canBeEncryptedProductSkuRelatedAttributes = array(
		'sku', // For all Products
		'associated', // For Configurable Child Products
		'grouped', // For Grouped Child Products
		'bundle_selections_data', // For Bundle Child Products
	);

	public function setdat($dat) {
		$this->data0 = $dat;
	}

	public function setattdat($att) {
		$this->attributedata0 = $att;
	}

	/**
	 * Get the proper SKUs in an Array Format
	 *
	 * @param string $data
	 * @return array
	 * @author Anutosh Ghosh
	 */
	protected function userCSVDataAsArray($data) {
		$arrData = explode(',', $data);

		if (count($arrData)) {
			foreach ($arrData as &$_each) {
				$_each = trim($_each);
			}
		}

		return $arrData;
	}

	protected function skusToIds($userData, $product) {
		$productIds = array();
		foreach ($this->userCSVDataAsArray($userData) as $oneSku) {
			if (($a_sku = (int) $product->getIdBySku($oneSku)) > 0) {
				parse_str("position=", $productIds[$a_sku]);
			}
		}
		return $productIds;
	}

	/**
	 * Decodes the given $encodedValue string which is
	 * encoded in the JSON format
	 *
	 * @param string $encodedValue
	 * @return mixed
	 */
	public function jsonDecode($encodedValue, $objectDecodeType = Zend_Json::TYPE_ARRAY) {
		return Zend_Json::decode($encodedValue, $objectDecodeType);
	}

	private function _getActualDate($date, $strDateFormat) {
		try {
			$arrApplicableSeparators = array('.', '/', '-');
			$separator = '';

			foreach ($arrApplicableSeparators as $_each) {
				if (strpos($date, $_each) !== false) {
					$separator = $_each;
					break;
				}
			}

			$arrDateFormat = explode($separator, $strDateFormat);
			$arrDate = explode($separator, $date);

			$currentTimestamp = mktime(0, 0, 0, $arrDate[0], $arrDate[1], $arrDate[2]);
			$reqDate = date('Y-m-d', $currentTimestamp);

			return $reqDate;
		} catch (Exception $e) {
			echo $e;
		}
	}

	/**
	 * Fix for Character Limitations
	 */
	private function _decryptProductAttributes(array $allAttributeValueList) {
		$arrResultant = $allAttributeValueList;

		if (count($allAttributeValueList)) {
			foreach ($allAttributeValueList as $_eachAttribute => $_eachValue) {
				if (in_array($_eachAttribute, $this->_canBeEncryptedProductSkuRelatedAttributes)) {
					switch ($_eachAttribute) {
						case 'associated':
						case 'grouped':
							$arrAssociatedSkus = explode(',', $_eachValue);

							if (count($arrAssociatedSkus)) {
								foreach ($arrAssociatedSkus as &$_eachAssociatedSku) {
									$_eachAssociatedSku = base64_decode($_eachAssociatedSku);
								}

								unset($_eachAssociatedSku); // This is necessary to ensure that the last Array element does not get modified unintentionally.
							}

							$arrResultant[$_eachAttribute] = implode(',', $arrAssociatedSkus);
							break;

						case 'bundle_selections_data':
							$arrBundleSelections = explode('|', $_eachValue);
							$numBundleSelections = count($arrBundleSelections);

							if ($numBundleSelections) {
								foreach ($arrBundleSelections as &$_eachBundleSelection) {
									if (empty($_eachBundleSelection)) {
										continue;
									}
									else {
										$allSelections = explode('~', $_eachBundleSelection);

										if (count($allSelections)) {
											$flag = 0; // "0" value indicates that Default Selection needs to be checked

											foreach ($allSelections as &$_eachSelection) {
												$_tempVar = explode(':', $_eachSelection);

												if ($flag) {
													$_eachSelection = substr_replace($_eachSelection, base64_decode($_tempVar[0]), 0, strpos($_eachSelection, ":"));
												}
												else {
													if (!empty($_tempVar[1])) {
														$_eachSelection = $_tempVar[0] . ":" . base64_decode($_tempVar[1]);
													}

													$flag++;
												}

												unset($_tempVar);
											}

											$_eachBundleSelection = implode('~', $allSelections);
											unset($_eachSelection, $flag, $allSelections);
										}
									}
								}

								unset($_eachBundleSelection);
							}

							$arrResultant[$_eachAttribute] = implode('|', $arrBundleSelections);
							break;

						default:
							$temp = base64_decode($_eachValue, true);
							$arrResultant[$_eachAttribute] = ($temp === false ) ? $_eachValue : $temp;
							unset($temp);
					}
				}
			}
		}

		return $arrResultant;
	}

	public function products(array $importData) { 
		try {
			/**
			 * Fix for Character Limitations
			 */
			
			$importData = $this->_decryptProductAttributes($importData);
			Mage::log('-------------------------------------------');
			Mage::log($importData);
			Mage::log('-------------------------------------------');
			
			$product_type = $importData['type'];
			$store =$importData['store'];
			if($store == null){
				$store =Mage_Core_Model_App::ADMIN_STORE_ID;
			}
			
			// default data
			$objSapConfig = new Insync_Sap_Model_Config();
			$product1 = $objSapConfig->_getProduct($importData['sku'], Mage_Core_Model_App::ADMIN_STORE_ID, 'sku');
			$adminname=$product1->getData('name');
			$admindesc=$product1->getData('description');
			$adminshortdesc=$product1->getData('short_description');
			$$adminstatus=$product1->getData('status');
			unset($product1, $objSapConfig);
			// default data
			
			Mage::app()->setCurrentStore($store);
			$qty = 0;

			/**
			 * This variable determines whether the Price of the Main Product will get converted Website-wise or not.
			 * By default, the Price of the Main Product will get converted.
			 */
			$flagConvertWebsiteWisePrices = true;

			/**
			 * This variable must be used from now on, to access the Product Type attribute.
			 */
			$product_type = strtolower($product_type);

			if (isset($importData['sku']) && !empty($importData['sku'])) {
				$this->deleteOptions($importData['sku']);
				if ($product_type == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
					$this->deleteBundleItems($importData['sku']);

					/**
					 * Fix for Character Limitations
					 */
					$_tempBundleSelectionData = array(
						'bundle_selections_data' => $this->attributedata0
					);
					$this->attributedata0 = $this->_decryptProductAttributes($_tempBundleSelectionData);

					/**
					 * If the Price Type of Bundle Product is Dynamic (having value '0'), then its Price will be 0.
					 * If the Price Type is Fixed (having value '1'), then no change in its Price will be made.
					 */
					//Mage::log($importData['price_type']);
					if ($importData['price_type'] != '1') {
						$importData['price'] = 0;
						$importData['tax_class_id'] = Insync_Sap_Model_Config::PRODUCT_TAX_CLASS_LABEL_NONE;
						$flagConvertWebsiteWisePrices = false;
					}
				}
				else if ($product_type == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
					$flagConvertWebsiteWisePrices = false;
				}
			}

			if (array_key_exists('qty', $importData)) {
				if (isset($importData['qty']) && !empty($importData['qty'])) {
					$qty = $importData['qty'];
				}
			}

			$this->addOptionArray($this->dataCustomOptions);
			if ($product_type == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
				if (array_key_exists('associated', $importData)) {
					if (isset($importData['associated']) && !empty($importData['associated'])) {
						$app_sku = explode(',', $importData['associated']);
						foreach ($app_sku as $sku) {
							$pr = new Mage_Catalog_Model_Product();
							if ($pr->getIdBySku($sku)) {
								$apid[] = $pr->getIdBySku($sku);
							}
							else {
								return sprintf('Associated product %s does not exist', $sku);
							}
						}
					}
				}
			}

			$product = $this->getProductModel()
					->reset();

			/**
			 * Start of Store ID Extraction & Setting Store for Product
			 */
			if (Insync_Sap_Model_Config::IS_STORE_CONCEPT_USABLE) {
				if (empty($importData['store'])) {
					if (!is_null($this->getBatchParams('store'))) {
						$store = $this->getStoreById($this->getBatchParams('store'));
					}
					else {
						$message = Mage::helper('catalog')->__('Required field "%s" not defined', 'store');
						return $message;
					}
				}
				else {
					$store = $this->getStoreByCode($importData['store']);
				}
				if ($store === false) {
					$message = Mage::helper('catalog')->__('Store "%s" field not exist', $importData['store']);
					return $message;
				}
				$product->setStoreId($store->getId());
			}
			/**
			 * End of Code
			 */
			if (empty($importData['sku'])) {
				$message = Mage::helper('catalog')->__('Required field "%s" not defined', 'sku');
				return $message;
			}

			$product->setStoreId($store);
			$productId = $product->getIdBySku($importData['sku']);

			Mage::log('----------$newp---------------------------------');
			
			$newp = true;
			if ($productId) {
				$product->load($productId);
				$newp = false;
				Mage::log('----------$newp---------------------------------'.$newp);
				// foreach ($importData as $key => $value) {
					// if ($key == 'description') {
						// unset($importData['description']);
						// break;
					// }
				// }
			}
			else {
				$productTypes = $this->getProductTypes();
				$productAttributeSets = $this->getProductAttributeSets();

				/**
				 * Check product define type
				 */
				if (empty($product_type) || !isset($productTypes[$product_type])) {
					$value = isset($product_type) ? $product_type : '';
					$message = Mage::helper('catalog')->__('Not valid value "%s" for field "%s"', $value, 'type');
					return $message;
				}
				$product->setTypeId($productTypes[$product_type]);

				/**
				 * Check product define attribute set
				 */
				if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
					$value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
					$message = Mage::helper('catalog')->__('Not valid value "%s" for field "%s"', $value, 'attribute_set');
					return $message;
				}
				$product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

				foreach ($this->_requiredFields as $field) {
					$attribute = $this->getAttribute($field);
					if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
						$message = Mage::helper('catalog')->__('Required field "%s" for new products not defined', $field);
						return $message;
					}
				}
			}

			$this->setProductTypeInstance($product);

			if (isset($importData['category_ids'])) {
				$allCategoryIds = $importData['category_ids'];

				if (Insync_Sap_Model_Config::IS_CATEGORY_SAPGROUP_IMPLEMENTABLE) {
					if (isset($importData['ItemGrp']) && !empty($importData['ItemGrp'])) {
						$reqChildSapGroupName = Insync_Sap_Model_Config::STR_CATEGORY_SAPGROUP_CHILD_PREFIX . strtoupper($importData['ItemGrp']);

						$categoryCollection = Mage::getResourceModel('catalog/category_collection')
								->addAttributeToSelect('*');
						foreach ($categoryCollection as $category) {
							if ($reqChildSapGroupName === $category->getName()) {
								if (!empty($allCategoryIds))
									$allCategoryIds .= ',';

								$allCategoryIds .= $category->getEntityId();
								break;
							}
						}

						unset($categoryCollection);
					}
				}

				$product->setCategoryIds($allCategoryIds);
				unset($allCategoryIds);
			}

			foreach ($this->_ignoreFields as $field) {
				if (isset($importData[$field])) {
					unset($importData[$field]);
				}
			}

			if (Insync_Sap_Model_Config::IS_STORE_CONCEPT_USABLE) {
				if ($store->getId() != 0) {
					$websiteIds = $product->getWebsiteIds();
					if (!is_array($websiteIds)) {
						$websiteIds = array();
					}
					if (!in_array($store->getWebsiteId(), $websiteIds)) {
						$websiteIds[] = $store->getWebsiteId();
					}
					$product->setWebsiteIds($websiteIds);
				}
			}

			if (isset($importData['websites'])) {
				$websiteIds = $product->getWebsiteIds();
				if (!is_array($websiteIds)) {
					$websiteIds = array();
				}
				$newWebsiteIds = array();
				$websiteCodes = explode(',', $importData['websites']);
				foreach ($websiteCodes as $websiteCode) {
					try {
						$website = Mage::app()->getWebsite(trim($websiteCode));
						/* if (!in_array($website->getId(), $websiteIds)) {
						  $websiteIds[] = $website->getId();
						  } */
						$newWebsiteIds[] = $website->getId();
					} catch (Exception $e) {

					}
				}
				if($newp==false){
					$result = array_merge($newWebsiteIds, $product->getWebsiteIds());
					$product->setWebsiteIds($result);
				}else{
					$product->setWebsiteIds($newWebsiteIds);
				}
				
				unset($websiteIds, $newWebsiteIds);
			}

			foreach ($importData as $field => $value) {
				if (in_array($field, $this->_inventoryFields)) {
					continue;
				}
				if (in_array($field, $this->_imageFields)) {
					continue;
				}

				$attribute = $this->getAttribute($field);
				if (!$attribute) {
					continue;
				}

				$isArray = false;
				$setValue = $value;

				if ($attribute->getFrontendInput() == 'multiselect') {
					$value = explode(self::MULTI_DELIMITER, $value);
					$isArray = true;
					$setValue = array();
				}

				if ($value && $attribute->getBackendType() == 'decimal') {
					$setValue = $this->getNumber($value);
				}

				if ($attribute->usesSource()) {
					$options = $attribute->getSource()->getAllOptions(false);
					if ($isArray) {
						foreach ($options as $item) {
							if (in_array($item['label'], $value)) {
								$setValue[] = $item['value'];
							}
						}
					}
					else {
						$setValue = null;
						foreach ($options as $item) {
							if ($item['label'] == $value) {
								$setValue = $item['value'];
							}
						}
					}
				}

				$product->setData($field, $setValue);
			}

			if ($importData['visibility'] == 'Catalog, Search') {
				$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
			}
			if ($importData['visibility'] == 'Nowhere') {
				$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
			}
			if (!$product->getVisibility()) {
				$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
			}

			// Start of Setting Product Stock
			$status = 0;
			$backorderStatus = 0;
			if (Insync_Sap_Model_Config::IS_BACKORDER_REQUIRED) {
				$backorderStatus = 1;
			}

			if ($qty) {
				$status = 1;
			}
			if ($qty <= 0 && Insync_Sap_Model_Config::IS_PRODUCT_ALWAYS_IN_STOCK) {
				$status = 1;
			}

			$product->setStockData(array(
				'is_in_stock' => $status,
				'qty' => $qty,
				'backorders' => $backorderStatus
			));
			// End of Setting Product Stock

			$imageData = array();
			foreach ($this->_imageFields as $field) {
				if (!empty($importData[$field]) && $importData[$field] != 'no_selection') {
					if (!isset($imageData[$importData[$field]])) {
						$imageData[$importData[$field]] = array();
					}
					$imageData[$importData[$field]][] = $field;
				}
			}
			foreach ($imageData as $file => $fields) {
				try {
					$product->addImageToMediaGallery(Mage::getBaseDir('media') . DS . 'import' . DS . $file, $fields, false, false);
				} catch (Exception $e) {
					//echo $e;
				}
			}
			try {
				$galleryData = explode(';', $importData["gallery"]);
				foreach ($galleryData as $gallery_img) {
					$img = $product->addImageToMediaGallery(Mage::getBaseDir('media') . DS . 'import' . DS . $gallery_img, null, false, false);
				}
			} catch (Exception $e) {
				//echo $e;
			}

			if ($product_type == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
				$productdata = explode(',', $this->data0);

				foreach ($productdata as $data) {
					$attdata = explode('|', $data);
					foreach ($attdata as $att) {
						$attvalues = explode('~', $att);
						$configChildPrice[] = $attvalues[4];

						/**
						 * In the Magento way, the Index of "is_percent" is used when saving the Associated Products of the Configurable Product,
						 * so this "is_percent" Index is added with value "0".
						 */
						$str = '{"attribute_id": "#2", "label": "#3", "value_index": "#4", "is_percent": "0"}';
						$str = str_replace('#2', $attvalues[2], $str);
						$str = str_replace('#3', $attvalues[1], $str);
						$str = str_replace('#4', $attvalues[3], $str);

						foreach ($apid as $id) {
							if ($attvalues[0] == $id) {
								if (empty($group[$id])) {
									$group[$id] = $str;
								}
								else {
									$group[$id] = $group[$id] . ',' . $str;
								}
							}
						}
					}
				}

				$config_att_str = '';
				$i = 0;
				foreach ($apid as $id) {
					$config_att_str .= '"' . $id . '"' . ':[' . $group[$id] . ']';
					if (count($apid) > 1 && $i < count($apid) - 1) {
						$config_att_str .= ',';
					}
					$i++;
				}
				$config_att_str = '{' . $config_att_str . '}';

				$configAttributeCodes = $this->userCSVDataAsArray($importData['config_attributes']);
				$attid = array();

				foreach ($configAttributeCodes as $attributeCode) {
					$attribute = Mage::getModel('catalog/product')
							->getResource()
							->getAttribute($attributeCode);
					if ($attribute->getData('is_configurable') == 1) {
						$attid[] = $attribute->getId();
					}
					unset($attribute);
				}
				if (!empty($attid)) {
					$product->getTypeInstance()->setUsedProductAttributeIds($attid);
				}

				$product->setConfigurableProductsData($this->jsonDecode($config_att_str));

				$cofigurable_attribute_data = explode(',', $this->attributedata0);

				$configChildPriceCount = 0;
				$priceArray = array();
				foreach ($cofigurable_attribute_data as $attribute_data) {
					$attribute = explode('|', $attribute_data);
					foreach ($attribute as $data) {
						$att = explode('~', $data);
						if (!array_key_exists($att[1] . '-' . $att[2], $priceArray)) {
							$priceArray[$att[1] . '-' . $att[2]] = $att[3];
						}
						else {
							if ($att[3] == 0) {
								$priceArray[$att[1] . '-' . $att[2]] = $att[3];
							}
						}
					}
				}

				foreach ($cofigurable_attribute_data as $attribute_data) {
					$attribute = explode('|', $attribute_data);
					$counterEachAttributeData = 0;

					foreach ($attribute as $data) {
						$att = explode('~', $data);

						//if(count($att)<3)continue;

						/**
						 * In the Magento way, no Index of "value_id" is used when saving the Associated Products of the Configurable Product,
						 * so this "value_id" Index is removed, whose string value was:-
						 * "value_id": "#4"
						 * Insync Development Team
						 */
						$str = '{"value_index": "#1", "label": "#2", "is_percent": "0", "pricing_value": "#5", "use_default_value": true, "attribute_id": "#3"}';
						$str = str_replace('#1', $att[2], $str);
						$str = str_replace('#2', $att[0], $str);
						// $str = str_replace('#5', $att[3], $str);
						$str = str_replace('#5', $priceArray[$att[1] . '-' . $att[2]], $str);
						// $str = str_replace('#4', $att[2], $str);

						if (count($attid))
							$str = str_replace('#3', $attid[$counterEachAttributeData], $str);

						foreach ($attid as $id) {
							if ($att[1] == $id) {
								if (empty($group1[$id])) {
									$group1[$id] = $str;
								}
								else {
									$group1[$id] = $group1[$id] . ',' . $str;
								}
							}
						}

						$configChildPriceCount++;
						$counterEachAttributeData++;
					}
				}

				$config_att_str = '';
				$i = 0;
				foreach ($attid as $id) {
					$attribute = new Mage_Catalog_Model_Entity_Attribute();
					$attribute->load($id);

					$concat1 = '{"label": "#2", "use_default": "0", "position": "0",';
					$concat1 = str_replace('#2', $attribute->getFrontend()->getLabel(), $concat1);

					$concat2 = '"attribute_id": "#1", "attribute_code": "#2", "frontend_label": "#3", "store_label": "#4", "html_id": "configurable__attribute_#5"}';
					$concat2 = str_replace('#1', $id, $concat2);
					$concat2 = str_replace('#2', $attribute->getAttributeCode(), $concat2);
					$concat2 = str_replace('#3', $attribute->getFrontend()->getLabel(), $concat2);
					$concat2 = str_replace('#4', $attribute->getFrontend()->getLabel(), $concat2);
					$concat2 = str_replace('#5', $i, $concat2);

					$config_att_str .= $concat1 . '"values"' . ':[' . $group1[$id] . '],' . $concat2;
					if (count($attid) > 1 && $i < count($attid) - 1) {
						$config_att_str .= ',';
					}
					$i++;
				}
				$config_att_str = '[' . $config_att_str . ']';

				if ($newp=='true') {
					$product->setConfigurableAttributesData($this->jsonDecode($config_att_str));
					$product->setCanSaveConfigurableAttributes(true);
				}

				$product->setCanSaveCustomOptions(true);
				if (isset($importData['associated'])) {
					$product->setConfigurableProductsData($this->skusToIds($importData['associated'], $product));
				}
			}

			/**
			 * Start of Grouped Products Part
			 */
			if ($product_type == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
				$productIdsArray = $this->setGroupedProducts($importData['grouped']);
				if (is_array($productIdsArray)) {
					if (isset($productIdsArray) && !empty($productIdsArray)) {
						$product->setGroupedLinkData($productIdsArray);
					}
				}
			}
			/**
			 * End of Grouped Products Part
			 */
			/**
			 * Start of Bundle Products Part
			 */
			if ($product_type == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
				// Checks for the Shipment Type
				if (!isset($importData['shipment_type'])) {
					$message = Mage::helper('catalog')->__('Skip import row, as no value for "%s" exists', 'Shipment Type');
					Mage::log($message, Zend_Log::ERR, Insync_Sap_Model_Config::MAIN_LOG_FILE);
					Mage::throwException($message);
				}

				// Checks Bundle Options
				if (empty($this->data0)) {
					$message = Mage::helper('catalog')->__('Skip import row, as no value for "%s" exists', 'Bundle Options');
					Mage::log($message, Zend_Log::ERR, Insync_Sap_Model_Config::MAIN_LOG_FILE);
					Mage::throwException($message);
				}

				// Checks Bundle Selections
				if (empty($this->attributedata0)) {
					$message = Mage::helper('catalog')->__('Skip import row, as no value for "%s" exists', 'Bundle Selections');
					Mage::log($message, Zend_Log::ERR, Insync_Sap_Model_Config::MAIN_LOG_FILE);
					Mage::throwException($message);
				}

				/**
				 * Section of Bundle Options
				 *
				 * Required Properties of Bundle Options are:-
				 * 1. title
				 * 2. option_id
				 * 3. delete
				 * 4. type
				 * 5. required
				 * 6. position
				 *
				 * Properties Fetched, in the following order:-
				 * 1. Title
				 * 2. Type ['select', 'radio', 'checkbox', 'multi']
				 * 3. Required ['0' = No, '1' = Yes]
				 */
				Mage::register('product', $product);
				$prepareBundleOptions = array();
				$bundleOptions = explode('|', $this->data0);
				$bOptionCounter = 0;
				foreach ($bundleOptions as $eachBundleOption) {
					list($bOption['title'], $bOption['type'], $bOption['required']) = explode('~', $eachBundleOption);
					$bOption['position'] = $bOptionCounter++;
					$bOption['delete'] = $bOption['option_id'] = '';
					$prepareBundleOptions[] = $bOption;
				}
				$numBundleOptions = count($prepareBundleOptions);

				/**
				 * Section of Bundle Selections
				 *
				 * Required Properties of Bundle Selections
				 * 1.	selection_id
				 * 2.	option_id
				 * 3.	product_id
				 * 4.	delete
				 * 5.	selection_price_value
				 * 6.	selection_price_type
				 * 7.	selection_qty
				 * 8.	selection_can_change_qty
				 * 9.	position
				 * 10.	is_default
				 *
				 * Properties Fetched, in the following order:-
				 * 1. Product Sku
				 * 2. Default Quantity
				 * 3. User Defined Quantity
				 * 4. Price (corresponds to "selection_price_value")
				 * 5. Price Type ['0' = Fixed, '1' = Percent]
				 */
				$prepareBundleSelections = array();
				$bundleSelections = explode('|', $this->attributedata0['bundle_selections_data']);
				if (count($bundleSelections) != $numBundleOptions) {
					$message = Mage::helper('catalog')->__('Skip import row, as the number of "%s" has not been sent correctly', 'Bundle Selections');
					Mage::log($message, Zend_Log::CRIT, Insync_Sap_Model_Config::MAIN_LOG_FILE);
					Mage::throwException($message);
				}

				foreach ($bundleSelections as $eachBundleSelection) {
					unset($eachModifiedBundleSelection);
					$eachModifiedBundleSelection = array();

					if (empty($eachBundleSelection)) {
						$message = Mage::helper('catalog')->__('No selections exist');
						Mage::log($message, Zend_Log::WARN, Insync_Sap_Model_Config::MAIN_LOG_FILE);
					}
					else {
						$allSelections = explode('~', $eachBundleSelection);
						$flag = 0;
						$defaultSelection = 0;
						foreach ($allSelections as $eachSelection) {
							unset($tempVar);
							$tempVar = explode(':', $eachSelection);

							if (!$flag) {
								if (empty($tempVar[1])) {
									$defaultSelection = '';
								}else {
									$defaultSelection = explode(',', $tempVar[1]);
								}
								$flag++;
								continue;
							}

							/**
							 * The following Condition was used in the Preliminary Version, in
							 * which the property / attribute "default_selection" was supplied with
							 * the numbers corresponding to the number of Bundle Selection Items.
							 *
							 * The following Condition stands cancelled till it is revoked completely.
							 */
							/* if (isset($defaultSelection) && in_array($flag, $defaultSelection)) {
							  $eachModifiedSelection['is_default'] = 1;
							  } */

							if (!empty($defaultSelection) && in_array($tempVar[0], (array) $defaultSelection)) {
								$eachModifiedSelection['is_default'] = 1;
							}

							$objP = new Mage_Catalog_Model_Product();
							$eachModifiedSelection['product_id'] = $objP->getIdBySku($tempVar[0]);
							$eachModifiedSelection['selection_qty'] = $tempVar[1];
							$eachModifiedSelection['selection_can_change_qty'] = $tempVar[2];
							$eachModifiedSelection['position'] = $flag++;

							$eachModifiedSelection['selection_price_value']
									= $eachModifiedSelection['selection_price_type']
									= 0;

							if ($importData['price_type'] == '1') {
								if (isset($tempVar[3]))
									$eachModifiedSelection['selection_price_value'] = $tempVar[3];
								if (isset($tempVar[4]))
									$eachModifiedSelection['selection_price_type'] = $tempVar[4];
							}

							$eachModifiedSelection['delete']
									= $eachModifiedSelection['option_id']
									= $eachModifiedSelection['selection_id']
									= '';
							$eachModifiedBundleSelection[] = $eachModifiedSelection;
							unset($eachModifiedSelection['is_default']);
						}
					}

					$prepareBundleSelections[] = $eachModifiedBundleSelection;
				}

				$product->setBundleOptionsData($prepareBundleOptions);
				$product->setBundleSelectionsData($prepareBundleSelections);
				$product->setCanSaveBundleSelections(true);
				$product->setAffectBundleProductSelections(true); // if($newp=='true') 
			}
			/**
			 * End of Bundle Products Part
			 */
			$product->setIsMassupdate(true);
			$product->setExcludeUrlRewrite(true);

			$product->setData('sap_sync', 1);
			$product->save();
			$product_id = $product->getId();
			
			

			for ($i = 0; $i < count($this->options); $i++) {
				$this->setCustomOption($product_id, $this->options[$i], $this->values[$i]);
			}

			if ($flagConvertWebsiteWisePrices) {
				$this->_setWebSiteWisePrice($product_id, $importData['price']);

				// Mage::log('default');
				// Mage::log('-------------------------------------------');
				if($importData['store']==null){
					$paramAttributes = array(
						'name',
						'description',
						'short_description',
						'status',
						'visibility',
						'tax_class_id',
						'url_key',
						'image',
						'small_image',
						'thumbnail'
					);
					if ($store == Mage_Core_Model_App::ADMIN_STORE_ID) {
						$this->_setDefaultChecked($product->getId(), $product->getWebsiteIds(), $paramAttributes);
					}
				}
			}
			
			$data1 = Mage::getResourceModel('tax/class_collection')
                ->addFieldToFilter('class_type', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
                ->addFieldToFilter('class_name', $importData['tax_class_id'])
                ->load()
                ->toOptionArray();
			
			$taxid=0;
			foreach($data1 as $d){
				$taxid = $d['value'];
			}
			unset($data1);

			$objSapConfig = new Insync_Sap_Model_Config();
			
			if($importData['store']==null || $importData['store']==''){ // if store blank will apply like reset
				$stores = Mage::getModel('core/store')->getCollection();
				foreach ($stores as $store) {
					Mage::log('-----------$store--------------------------------');
					Mage::log($store->getId());
					$product = $objSapConfig->_getProduct($product_id, ($store->getId()), 'id');
					$product->setData('name',$importData['name']);
					$product->setData('description',$importData['description']);
					$product->setData('short_description',$importData['short_description']);
					$product->setData('tax_class_id',$taxid);
					$product->setData('status', $this->getStatus($importData['status']));
					$product->save();
					unset($product);
				}
			}else{
				$product1 = $objSapConfig->_getProduct($product_id, $store, 'id');
				$product1->setData('name',$importData['name']);
				$product1->setData('description',$importData['description']);
				$product1->setData('short_description',$importData['short_description']);
				$product1->setData('tax_class_id',$taxid);
				$product1->setData('status', $this->getStatus($importData['status']));
				$product1->save();
				unset($product1);
				
				$product1 = $objSapConfig->_getProduct($product_id, Mage_Core_Model_App::ADMIN_STORE_ID, 'id');
				$product1->setData('name',$adminname);
				$product1->setData('description',$admindesc);
				$product1->setData('short_description',$adminshortdesc);
				$product1->setData('tax_class_id',$taxid);
				$product1->setData('status', $this->getStatus($adminstatus));
				$product1->save();
				unset($product1);
			}
			return $product_id;
		} catch (Exception $e) {
			echo $e;
			//return sprintf('Product %s not uploaded', $importData['sku']);
		}
	}
	
	public function getStatus($stat){
		return (strtolower($stat)=='disabled')?Mage_Catalog_Model_Product_Status::STATUS_DISABLED:Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
	}
	
	protected function _setDefaultChecked($productId, $productWebsiteIds, $paramAttributes) {
		if (is_array($productWebsiteIds) && count($productWebsiteIds)) {
			foreach ($productWebsiteIds as $_eachWebsiteId) {
				$objWebsite = new Mage_Core_Model_Website();
				$objWebsite->load($_eachWebsiteId);
				$_storeIds = $objWebsite->getStoreIds();

				foreach ($_storeIds as $_eachStoreId) {
					$product = Mage::getModel('catalog/product')
							->setStoreId($_eachStoreId)
							->setData('_edit_mode', true)
							->load($productId);

					foreach ($paramAttributes as $_eachAttribute) {
						$product->setData($_eachAttribute, false);
					}

					$product->save();
					unset($product);
				}
			}
		}
	}

	public function setGroupedProducts($productSkus) {
		$productIdsArray = array();
		if (is_string($productSkus)) {
			if (isset($productSkus) && !empty($productSkus)) {
				if (strpos($productSkus, ',')) {
					$productSkusArray = explode(',', $productSkus);
					foreach ($productSkusArray as $productSku) {
						$product = new Mage_Catalog_Model_Product();
						$productIdsArray[$product->getIdBySku($productSku)] = array(
							'qty' => '',
							'position' => ''
						);
					}
				}
			}
		}
		return $productIdsArray;
	}

	public function setCustomOption($productId, array $optionData, array $values = array()) {
		Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
		if (!$product = Mage::getModel('catalog/product')->load($productId)) {
			//throw new Exception('Can not find product: ' . $productId);
			return sprintf('Product Id %s does not exist', $productId);
		}

		$defaultData = array(
			'title' => '',
			'is_require' => 0,
			'price' => 0,
			'price_type' => 'fixed',
			'type' => 'field'
		);
		$data = array_merge($defaultData, $optionData, array(
			'product_id' => (int) $productId,
			'values' => $values
				));
		$product->setHasOptions(1)->save();
		$option = Mage::getModel('catalog/product_option')
				->setData($data)
				->setProduct($product)
				->save();
		return $option;
	}

	public function addOptionArray($allCustomOptionsData) {
		if (!empty($allCustomOptionsData)) {
			$tempArrAllImportData = explode('~', $allCustomOptionsData);
			$importData = array();
			foreach ($tempArrAllImportData as $_eachImportData) {
				$arrCustomOptionsProps = explode(':', $_eachImportData);
				$importData[$arrCustomOptionsProps[0]] = $arrCustomOptionsProps[1];
			}
		}
		else {
			return true;
		}

		if (isset($importData['titlecount']) && !empty($importData['titlecount'])) {
			$titleCount = $importData['titlecount'];
			if ($titleCount) {
				for ($i = 0; $i < $titleCount; $i++) {
					if ($importData['is_required' . $i] == 'Y') {
						$status = 1;
					}
					else {
						$status = 0;
					}
					$this->options[] = array(
						'title' => $importData['title' . $i],
						'is_require' => $status,
						'price' => 0,
						'price_type' => 'fixed',
						'type' => $importData['inputtype' . $i]
					);
					if (isset($importData['valuecount' . $i]) && !empty($importData['valuecount' . $i])) {
						$valuesCount = $importData['valuecount' . $i];
						if ($valuesCount) {
							$value = array();
							for ($j = 0; $j < $valuesCount; $j++) {
								$value[] = array(
									'title' => $importData['value' . $i . $j],
									'price' => $importData['price' . $i . $j],
									'sku' => $importData['sku' . $i . $j],
									'price_type' => 'fixed'
								);
							}
							$this->values[$i] = $value;
						}
					}
				}
			}
		}
	}

	public function updateStock($productId, $qty) {
		try {
			$status = 0;
			$backorderStatus = 0;
			if (Insync_Sap_Model_Config::IS_BACKORDER_REQUIRED) {
				$backorderStatus = 1;
			}

			if ($qty) {
				$status = 1;
			}
			if ($qty <= 0 && Insync_Sap_Model_Config::IS_PRODUCT_ALWAYS_IN_STOCK) {
				$status = 1;
			}
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

			$product = Mage::getModel('catalog/product')
					->setStoreId(Mage::app()->getStore()->getId())
					->load($productId)
					->setStockData(array(
						'is_in_stock' => $status,
						'qty' => $qty,
						'backorders' => $backorderStatus
					));
			$product->save();
		} catch (Exception $e) {
			//echo $e;
		}
	}

	public function deleteOptions($sku, $specialFrom = '', $specialTo = '') {
		try {
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$p = new Mage_Catalog_Model_Product();
			$product_id = $p->getIdBySku($sku);
			if ($product_id) {
				$p->load($product_id);
				foreach ($p->getOptions() as $o) {
					$o->getValueInstance()->deleteValue($o->getId());
					$o->deletePrices($o->getId());
					$o->deleteTitles($o->getId());
					$o->delete();
				}
			}

			#$p->setData($field, $setValue);
		} catch (Exception $e) {
			//echo $e;
		}
	}

	public function deleteBundleItems($productSku) {
		try {
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$objProduct = new Mage_Catalog_Model_Product();
			$productId = $objProduct->getIdBySku($productSku);
			$objProduct->load($productId);

			if ($objProduct->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
				$objBundle = new Mage_Bundle_Model_Product_Type();

				$prepareBundleOptions = array();
				$prepareBundleSelections = array();

				foreach ($objBundle->getOptionsCollection($objProduct)->getData() as $_optionCollection) {
					$_optionCollection['delete'] = 1;
					$eachBundleOptionSelection = array();
					$dataBundleSelectionsCollection = $objBundle->getSelectionsCollection(array($_optionCollection['option_id']), $objProduct)
							->getData();

					if (isset($dataBundleSelectionsCollection) && !empty($dataBundleSelectionsCollection)) {
						foreach ($dataBundleSelectionsCollection as $_selectionCollection) {
							$_selectionCollection['delete'] = 1;
							$eachBundleOptionSelection[] = $_selectionCollection;
						}
					}

					$prepareBundleSelections[] = $eachBundleOptionSelection;
					$prepareBundleOptions[] = $_optionCollection;
				}
				$objProduct->setBundleOptionsData($prepareBundleOptions);
				$objProduct->setBundleSelectionsData($prepareBundleSelections);
				$objProduct->setCanSaveBundleSelections(true);
				$objProduct->save();
			}
		} catch (Exception $e) {
			//echo $e;
		}
	}

	public function _setWebSiteWisePrice($productId, $websitePrices) {
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));

		// The variable "$websitePrices" is kept as a backup.
		$newWebsitePrices = $websitePrices;

		/**
		 * Start of Section for setting the Price for the "Default Values" Store View
		 */
		$duplicateWebsitePrices = $websitePrices;
		$arrDuplicateWebsitePrices = explode('@', $duplicateWebsitePrices);

		if (count($arrDuplicateWebsitePrices)) {
			$rowDuplicateWebsiteData = explode('^', $arrDuplicateWebsitePrices[0]);

			if (count($rowDuplicateWebsiteData) == 3) {
				$defaultPrice = $rowDuplicateWebsiteData[1];
				$defaultCurrencyCode = $rowDuplicateWebsiteData[2];

				$newWebsitePrices = Mage_Core_Model_App::ADMIN_STORE_ID . '^' . $defaultPrice . '^' . $defaultCurrencyCode . '@' . $websitePrices;
			}

			unset($rowDuplicateWebsiteData);
		}

		unset($duplicateWebsitePrices, $arrDuplicateWebsitePrices);
		/**
		 * End of Section
		 */
		/**
		 * Start of Section for setting the Prices for all the Website-wise Store Views
		 */
		$arrWebsitePrices = explode('@', $newWebsitePrices);

		if (count($arrWebsitePrices)) {
			$objDirCurrency = new Mage_Directory_Model_Currency();
			$arrAllowedCurrencies = $objDirCurrency->getConfigAllowCurrencies();

			foreach ($arrWebsitePrices as $_eachWebsitePriceCombination) {
				$rowWebsiteData = explode('^', $_eachWebsitePriceCombination);

				if (count($rowWebsiteData) == 3) {
					$websiteId = $rowWebsiteData[0];
					$price = $rowWebsiteData[1];
					$websiteCurrentCurrencyCode = strtoupper($rowWebsiteData[2]);

					$websiteObj = new Mage_Core_Model_Website();
					$websiteObj->load($websiteId);
					$websiteBaseCurrencyCode = $websiteObj->getBaseCurrency()->getCode();
					if (empty($websiteBaseCurrencyCode)) {
						$websiteBaseCurrencyCode = Mage::app()->getBaseCurrencyCode();
					}

					if ($websiteCurrentCurrencyCode != $websiteBaseCurrencyCode && $websiteCurrentCurrencyCode != NULL) {
						$arrCurrencyRates = $objDirCurrency->getCurrencyRates($websiteBaseCurrencyCode, array_values($arrAllowedCurrencies));

						$currencyRate = '1';
						if (count($arrCurrencyRates)) {
							$currencyRate = $arrCurrencyRates[$websiteCurrentCurrencyCode];
						}

						try {
							$price = round(floatval($price) / floatval($currencyRate), 2);
						} catch (Mage_Core_Exception $e) {
							echo $e->getMessage();
						}

						unset($arrCurrencyRates, $currencyRate);
					}

					$storeIds = $websiteObj->getStoreIds();
					$product = Mage::getModel('catalog/product')
							->setStoreId(end($storeIds))
							->load($productId);
					$product->setData('price', $price);
					$product->save();

					unset($websiteId, $price, $websiteCurrentCurrencyCode, $websiteBaseCurrencyCode, $websiteObj, $storeIds, $product);
				}

				unset($rowWebsiteData);
			}
		}
		/**
		 * End of Section
		 */
	}

}