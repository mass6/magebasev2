<?php

/**
 * @category Parameterized Product download
 * @package Insync_Product
 * @copyright InSync Tech-Fin Solutions Ltd.
 */
abstract class Insync_Product_Model_All_Abstract extends Mage_Api_Model_Resource_Abstract {

	/**
	 * Get List of all types of product based on the filter type
	 * @param type $productType
	 * @param type $filters
	 * @param type $withsync
	 * @return type 
	 */
	public function info($productType = null, $filters = null, $withsync = true) {
		try {
			$preparedFilters = array();
			if (isset($filters->filter)) {
				foreach ($filters->filter as $_filter) {
					$preparedFilters[$_filter->key] = $_filter->value;
				}
			}
			if (isset($filters->complex_filter)) {
				foreach ($filters->complex_filter as $_filter) {
					$_value = $_filter->value;
					if ($_value->key == 'in' && strpos($_value->value, ',')) {
						$_value->value = explode(',', $_value->value);
					}
					$preparedFilters[$_filter->key] = array(
						$_value->key => $_value->value
					);
				}
			}
			$websiteid = array();
			$prodLimittemp = array();
			if (!empty($preparedFilters)) {
				foreach ($preparedFilters as $field => $value) {
					if ($field == 'website_id') {
						$websiteid = $value;
					}else if ($field == 'limit') {
						$prodLimittemp = $value;
					}
				}
			}
			$prodLimit = '250';
			foreach ($prodLimittemp as $key => $value) {
				if ($key == 'eq') {
					$prodLimit = $value;
				}
			}
			if (is_array($websiteid)) {
				foreach ($websiteid as $key => $value) {
					if ($key == 'in') {
						$websiteid = $value;
					} else {
						$websiteid = array($value);
					}
				}
			} else {
				$websiteid = explode(',', $websiteid);
			}
			$productArray = array();
			$productCollection = Mage::getResourceModel('catalog/product_collection');
			
			if ($withsync) {
				$productCollection->addFieldToFilter('type_id', $productType)
						->addAttributeToFilter('sap_sync', array('eq' => 0))
						->addWebsiteFilter($websiteid);
			}
			if (!empty($preparedFilters)) {
				try {
					foreach ($preparedFilters as $field => $value) {
						if ($field == 'website_id' || $field == 'limit' ) {
							continue;
						}
						if (isset($this->_filtersMap[$field])) {
							$field = $this->_filtersMap[$field];
						}
						$productCollection->addFieldToFilter($field, $value);
						$productCollection->addAttributeToFilter($field, $value);
					}
				} catch (Mage_Core_Exception $e) {
					$this->_fault('filters_invalid', $e->getMessage());
				}
			}
			
			if($prodLimit!= '') 
				$productCollection->getSelect()->limit($prodLimit);
			
			Mage::log($productCollection->getSelectSql(true));
			$attributesCache = array();
			foreach (Mage::getResourceModel('catalog/product_attribute_collection') as $attribute1) {
				$attributeCode = $attribute1->getAttributeCode();
				$attributeId = $attribute1->getId();
				if ($attribute1->getIsVisible()) {
					if (!isset($attributesCache[$attributeId])) {
						$attributesCache[$attributeId] = $attribute1->getApplyTo();
					}
				}
			}
			
			$objWebsites = Mage::getResourceModel('core/website_collection');
			$objStores = Mage::getResourceModel('core/store_collection');
			
			$countProducts = 0;
			foreach ($productCollection as $product) {
				$productObj = new Mage_Catalog_Model_Product();
				$productObj->load($product['entity_id']);
				// Product ID
				$productArray[$countProducts]['productId'] = $productObj->getId();
				// Attribute Set ID
				$productArray[$countProducts]['attributeSetId'] = $productObj->getAttributeSetId();
				// Attribute Set Name
				$attributeSet = new Mage_Eav_Model_Entity_Attribute_Set();
				$productArray[$countProducts]['attributeSetName'] = $attributeSet->load($productObj->getAttributeSetId())->getData('attribute_set_name');
				// Product Type
				$productArray[$countProducts]['productType'] = ucfirst($productObj->getTypeId());
				// Product Website ID
				$tempArrWebsiteId = array();
				$tempArrWebsiteId=$productObj->getWebsiteIds();
				$tempArrWebsiteId[]=0;
				$productArray[$countProducts]['websiteId'] = $tempArrWebsiteId;
				
				foreach($objWebsites as $objWebsite){
					if(in_array($objWebsite['website_id'], $tempArrWebsiteId)){
						$tempArrWebsiteCode[]=$objWebsite['code'];
					}
					
				}
				
				
				$tempArrWebsiteCode[] = 'admin';
				
				$productArray[$countProducts]['productWebsiteCode'] = $tempArrWebsiteCode;
				unset($tempArrWebsiteCode);
				// Product Store ID
				$tempArrStoreId = array();
				$tempArrStoreId=$productObj->getStoreIds();
				$tempArrStoreId[]=0;
				$productArray[$countProducts]['productStoreIds'] = $tempArrStoreId;
				foreach($objStores as $objStore){
					if(in_array($objStore['website_id'], $tempArrStoreId)){
						$tempArrStoreCode[]=$objStore['code'];
					}
					
				}
				
				$tempArrStoreCode[] = 'admin';
				$productArray[$countProducts]['productStoreCode'] = $tempArrStoreCode;
				unset($tempArrStoreCode);
				// Product Tax Class
				$productArray[$countProducts]['productTaxClass'] = $productObj->getTaxClassId();
				// Product Sku
				$productArray[$countProducts]['sku'] = $productObj->getSku();
				// Product  UOM
				$productArray[$countProducts]['uom'] = $productObj->getUom();
				// Product Name
				$productArray[$countProducts]['productName'] = ($productObj->getName());
				// Product Description
				$productArray[$countProducts]['productDescription'] = ($productObj->getDescription());
				// Product Short Description
				$productArray[$countProducts]['productShortDescription'] = ($productObj->getShortDescription());
				// Product Short Description
				$productArray[$countProducts]['weight'] = $productObj->getWeight();
				// Product Price and description by store
				$tempPriceArray = array();
				$descriptionByStore = array();
				
				foreach ($tempArrStoreId as $_storeId) {
					$tempStoreObj = new Mage_Core_Model_Store();
					$tempStoreObj->load($_storeId);
					$tempProductObj = new Mage_Catalog_Model_Product();
					$tempProductObj->setStoreId($_storeId);
					$tempProductObj->load($productObj->getId());
					
					$tempPriceArray[] = array(
						'websiteId' => $tempStoreObj->getWebsiteId(),
						'price' => $tempProductObj->getPrice(),
						'baseCurrency' => $tempStoreObj->getBaseCurrencyCode(),
					);
					
					// Product Detail
					$descriptionByStore[count($descriptionByStore)] = array('storeid' => $_storeId, 'name' => $tempProductObj->getData('name'),
					'description' => $tempProductObj->getData('description'),
					'short_description' => $tempProductObj->getData('short_description'));
					unset($tempStoreObj);
					unset($tempProductObj);
				}
				$productArray[$countProducts]['productPrice'] = $tempPriceArray;
				
				$productArray[$countProducts]['productDetailByStore']=$descriptionByStore;
				// Product Status ID
				$productArray[$countProducts]['productStatus'] = $productObj->getStatus();
				// Product Status Long
				if ($productObj->getStatus()) {
					$status = 'Enabled';
				} else {
					$status = 'Disabled';
				}
				$productArray[$countProducts]['productStatusName'] = $status;
				// Product Visibility ID
				$productArray[$countProducts]['productVisibilityId'] = $productObj->getVisibility();
				// Product Visibility
				$visibility = new Mage_Catalog_Model_Product_Visibility();
				$productArray[$countProducts]['productVisibility'] = $visibility->getOptionText($productObj->getVisibility());
				// Categories
				$_catArray = array();
				foreach ($productObj->getCategoryIds() as $_eachCategoryId) {
					$tempCategory = Mage::getModel('catalog/category')->load($_eachCategoryId);
					$_catArray[] = array(
						'categoryId' => $tempCategory->getId(),
						'categoryName' => $tempCategory->getName()
					);
					unset($tempCategory);
				}
				$productArray[$countProducts]['categories'] = $_catArray;
				unset($_catArray);
				// Product Images Name and Path
				// Image
				$tempImageContainer = array();
				if ($productObj->getImage() != 'no_selection') {
					$imgpath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $productObj->getImage();
					$encimg = base64_encode(file_get_contents($imgpath));
					$tempImageContainer[] = array(
						'name' => substr($productObj->getImage(), strrpos($productObj->getImage(), '/') + 1),
						'path' => $encimg,
						'extn' => substr($productObj->getImage(), strrpos($productObj->getImage(), '.') + 1),
						'ftppath' => $productObj->getData('image')
					);
				}
				$productArray[$countProducts]['image'] = $tempImageContainer;
				unset($tempImageContainer);
				// Small Image
				$tempSmallImageContainer = array();
				if ($productObj->getSmallImage() != 'no_selection') {
					$imgpath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $productObj->getSmallImage();
					$encimg = base64_encode(file_get_contents($imgpath));
					$tempSmallImageContainer[] = array(
						'name' => substr($productObj->getSmallImage(), strrpos($productObj->getSmallImage(), '/') + 1),
						'path' => $encimg,
						'extn' => substr($productObj->getSmallImage(), strrpos($productObj->getSmallImage(), '.') + 1),
						'ftppath' => $productObj->getData('small_image')
					);
				}
				$productArray[$countProducts]['smallimage'] = $tempSmallImageContainer;
				unset($tempSmallImageContainer);
				// Thumbnail Image
				$tempThumbnailContainer = array();
				if ($productObj->getThumbnail() != 'no_selection') {
					$imgpath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $productObj->getThumbnail();
					$encimg = base64_encode(file_get_contents($imgpath));
					$tempThumbnailContainer[] = array(
						'name' => substr($productObj->getThumbnail(), strrpos($productObj->getThumbnail(), '/') + 1),
						'path' => $encimg,
						'extn' => substr($productObj->getThumbnail(), strrpos($productObj->getThumbnail(), '.') + 1),
						'ftppath' => $productObj->getData('thumbnail')
					);
				}
				$productArray[$countProducts]['thumbnail'] = $tempThumbnailContainer;
				unset($tempThumbnailContainer);
				// Gallery Images
				$allImagesFile = array();
				$otherImages = array(
					$productObj->getImage(),
					$productObj->getSmallImage(),
					$productObj->getThumbnail()
				);
				foreach ($productObj->getMediaGalleryImages() as $galleryImage) {
					$allImagesFile[] = $galleryImage->getData('file');
				}
				$galleryImagesFile = array_diff($allImagesFile, $otherImages);
				$tempGallery = array();
				foreach ($galleryImagesFile as $galleryImage) {
					$imgpath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $galleryImage;
					$encimg = base64_encode(file_get_contents($imgpath));
					$tempGallery[] = array(
						'name' => substr($galleryImage, strrpos($galleryImage, '/') + 1),
						'path' => $encimg,
						'extn' => substr($galleryImage, strrpos($galleryImage, '.') + 1),
						'ftppath' => $galleryImage
					);
				}
				$productArray[$countProducts]['gallery'] = $tempGallery;
				unset($tempGallery);
				// Product Attributes
				$attributes = Mage::getModel('eav/entity_attribute')
						->getResourceCollection()
						->addHasOptionsFilter()
						->addAttributeGrouping()
						->setAttributeSetFilter($productObj->getAttributeSetId())
						->load();

				$tempAttribute = array();
				foreach ($attributes as $attribute) {

					if ($attribute->getIsUserDefined() && ($attribute->getData('frontend_input') == 'select') || ($attribute->getData('frontend_input') == 'multiselect')) {

						if ($attribute->getData('frontend_input') == 'multiselect') {
							if (Insync_Product_Model_All_Abstract::getAttributeOptions($attribute->getData('attribute_code'), $productObj->getData($attribute->getData('attribute_code'))) == '')
								continue;
						}else {
							if (Insync_Product_Model_All_Abstract::getAttributeValue($productObj->getAttributeText($attribute->getData('attribute_code'))) == '')
								continue;
						}

						$isfound = false;
						foreach ($attributesCache as $key => $value) {
							if ($key == $attribute->getData('attribute_id')) {
								if (count($value) > 0) {
									$isfound = true;
									foreach ($value as $data) {
										if ($data == $productObj->getTypeId()) {
											$isfound = false;
											break;
										}
									}
								}
							}
						}
						if ($isfound)
							continue;
						$attvalue = '';
						if ($attribute->getData('frontend_input') == 'multiselect') {
							$attvalue = Insync_Product_Model_All_Abstract::getAttributeOptions($attribute->getData('attribute_code'), $productObj->getData($attribute->getData('attribute_code')));
						} else {
							$attvalue = Insync_Product_Model_All_Abstract::getAttributeValue($productObj->getAttributeText($attribute->getData('attribute_code')));
						}

						$tempAttribute[] = array(
							'attributeid' => $attribute->getData('attribute_id'),
							'code' => $attribute->getData('attribute_code'),
							'attributename' => $attribute->getData('frontend_label'),
							'attributevalueid' => $productObj->getData($attribute->getData('attribute_code')),
							'attributevalue' => $attvalue,
							'type' => $attribute->getData('frontend_input'),
							'attributevalueallstore' => Insync_Product_Model_All_Abstract::getAttributeValueAllStore($productObj, $attribute->getData('attribute_code'), $attribute->getData('frontend_input'))
						);
						
					}
				}
				
				$productArray[$countProducts]['attribute'] = $tempAttribute;
				unset($tempAttribute);
				// Product Options
				$countOption = 0;
				foreach ($productObj->getOptions() as $option) {
					$productArray[$countProducts]['options'][$countOption]['optionid'] = $option->getData('option_id');
					$productArray[$countProducts]['options'][$countOption]['optiontype'] = $option->getData('type');
					$productArray[$countProducts]['options'][$countOption]['optionisrequire'] = $option->getData('is_require');
					$productArray[$countProducts]['options'][$countOption]['optiontitle'] = $option->getData('title');
					$countOptionVal = 0;
					foreach ($option->getValues() as $value) {
						$productArray[$countProducts]['options'][$countOption]['optionvalue'][$countOptionVal]['optionvalueid'] = $value->getData('option_type_id');
						$productArray[$countProducts]['options'][$countOption]['optionvalue'][$countOptionVal]['optionvaluesku'] = $value->getData('sku');
						$productArray[$countProducts]['options'][$countOption]['optionvalue'][$countOptionVal]['optionvaluetitle'] = $value->getData('title');
						$productArray[$countProducts]['options'][$countOption]['optionvalue'][$countOptionVal]['optionvalueprice'] = $value->getData('price');
						$productArray[$countProducts]['options'][$countOption]['optionvalue'][$countOptionVal]['optionvaluepricetype'] = $value->getData('price_type');
						$countOptionVal++;
					}
					$countOption++;
				}
				// Configurable Attributes
				if ($productObj->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
					$configAttributesArray = $productObj->getTypeInstance(true)->getConfigurableAttributesAsArray($productObj);
					
					if (is_array($configAttributesArray) && !empty($configAttributesArray)) {
						$tempConfigAtt = array();
						foreach ($configAttributesArray as $configAttributeData) {
						
							$tempConfigAtt[] = array(
								'configurableattributeid' => $configAttributeData['attribute_id'],
								'configurableattributecode' => $configAttributeData['attribute_code']
							);
							$count++;
							
						}
						$productArray[$countProducts]['configurableattributes'] = $tempConfigAtt;
						unset($tempConfigAtt);
					}
					// Associated Product
					$configurableProduct = new Mage_Catalog_Model_Product_Type_Configurable();
					$childrenIds = $configurableProduct->getChildrenIds($productObj->getId());
					if (is_array($childrenIds) && !empty($childrenIds)) {
						$tempAssoProdArr = array();
						foreach ($childrenIds as $childrenIdArray) {
							foreach ($childrenIdArray as $childrenId) {
								$associateProducts = new Mage_Catalog_Model_Product();
								$associateProducts->load($childrenId);
								$tempAssoProdArr[] = array(
									'associatedproductid' => $childrenId,
									'associatedproductsku' => $associateProducts->getSku()
								);
								unset($associateProducts);
							}
						}
						$productArray[$countProducts]['associatedproduct'] = $tempAssoProdArr;
						unset($tempAssoProdArr);
					}
				}
				// Grouped Product
				if ($productObj->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
					$grouped = new Mage_Catalog_Model_Product_Type_Grouped();
				
					$tempAssoProd = array();
					foreach ($grouped->getAssociatedProducts($productObj) as $groupedProduct) {
						$tempAssoProd[] = array(
							'groupedproductid' => $groupedProduct->getData('entity_id'),
							'groupedproductsku' => $groupedProduct->getData('sku')
						);
					}
					$productArray[$countProducts]['groupedproduct'] = $tempAssoProd;
					unset($tempAssoProd);
				}
				// Bundle product
				if ($productObj->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
					// Sku Type Id
					$skuTypeId = $productObj->getData('sku_type');
					$productArray[$countProducts]['skutype'] = $skuTypeId;
					// Sku Type Name
					if ($skuTypeId)
						$skuTypeName = 'Fixed';
					else
						$skuTypeName = 'Dynamic';
					$productArray[$countProducts]['skutypename'] = $skuTypeName;
					// Weight Type Id
					$weightTypeId = $productObj->getData('weight_type');
					$productArray[$countProducts]['weighttype'] = $weightTypeId;
					// Weight Type Name
					if ($weightTypeId)
						$weightTypeName = 'Fixed';
					else
						$weightTypeName = 'Dynamic';
					$productArray[$countProducts]['weighttypename'] = $weightTypeName;
					// Price Type Id
					$priceTypeId = $productObj->getData('price_type');
					$productArray[$countProducts]['pricetype'] = $priceTypeId;
					// Price Type Name
					if ($priceTypeId)
						$priceTypeName = 'Fixed';
					else
						$priceTypeName = 'Dynamic';
					$productArray[$countProducts]['pricetypename'] = $priceTypeName;
					// Price View Id
					$priceViewId = $productObj->getData('price_view');
					$productArray[$countProducts]['priceview'] = $priceViewId;
					// Price View Name
					if ($priceViewId)
						$priceViewName = 'As low as';
					else
						$priceViewName = 'Price Range';
					$productArray[$countProducts]['priceviewname'] = $priceViewName;
					// Shipment Type Id
					$shipmentTypeId = $productObj->getData('shipment_type');
					$productArray[$countProducts]['shipmenttype'] = $shipmentTypeId;
					// Shipment Type Name
					if ($shipmentTypeId)
						$shipmentTypeName = 'Separately';
					else
						$shipmentTypeName = 'Together';
					$productArray[$countProducts]['shipmenttypename'] = $shipmentTypeName;
					$objBundle = new Mage_Bundle_Model_Product_Type();
					$i = 0;
					
					foreach ($objBundle->getOptionsCollection($productObj)->getData() as $_optionCollection) {
						$productArray[$countProducts]['bundleoptions'][$i]['defaulttitle'] = $_optionCollection['default_title'];
						$productArray[$countProducts]['bundleoptions'][$i]['type'] = $_optionCollection['type'];
						$productArray[$countProducts]['bundleoptions'][$i]['required'] = $_optionCollection['required'];
						 $selectionCollection = $productObj->getTypeInstance(true)->getSelectionsCollection(
						 $_optionCollection['option_id'], $productObj
						);
						$j = 0;
						foreach ($selectionCollection as $_eachSelectionCollection) {
							$productArray[$countProducts]['bundleoptions'][$i]['bundleselections'][$j]['sku'] = $_eachSelectionCollection->getSku();
							$productArray[$countProducts]['bundleoptions'][$i]['bundleselections'][$j]['selectionqty'] = intval($_eachSelectionCollection['selection_qty']);
							$productArray[$countProducts]['bundleoptions'][$i]['bundleselections'][$j]['usercanchangeqty'] = $_eachSelectionCollection['selection_can_change_qty'];
							$productArray[$countProducts]['bundleoptions'][$i]['bundleselections'][$j]['defaultselection'] = $_eachSelectionCollection['is_default'];
							$productArray[$countProducts]['bundleoptions'][$i]['bundleselections'][$j]['selectionpricetype'] = intval($_eachSelectionCollection['selection_price_type']);
							$productArray[$countProducts]['bundleoptions'][$i]['bundleselections'][$j]['selectionpricevalue'] = floatval($_eachSelectionCollection['selection_price_value']);
							unset($dummyProductObj);
							$j++;
						}
						$i++;
					}
				}

				// Downloadable product
				if ($productObj->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
					$downloadprod = new Mage_Downloadable_Model_Product_Type();
					if ($downloadprod->hasLinks($productObj)) {
						$dproduct = array();
						$downloaddetails = $downloadprod->getLinks($productObj);
						foreach ($downloaddetails as $item) {
							$dprod = array();
							$dprod['linkid'] = $item['link_id'];
							$dprod['numberofdownloads'] = $item['number_of_downloads'];
							$dprod['linktype'] = $item['link_type'];
							$dprod['defaulttitle'] = $item['default_title'];
							$dprod['storetitle'] = $item['store_title'];
							$dprod['title'] = $item['title'];
							$dprod['defaultprice'] = $item['default_price'];
							$dprod['websiteprice'] = $item['website_price'];
							$dproduct[] = $dprod;
						}
						$productArray[$countProducts]['downloadableitemdata'] = $dproduct;
					}
				}

				$countProducts++;
				unset($productObj);
				
			}
			unset($productCollection);
			return $productArray;
		} catch (Exception $e) {
			echo $e;
		}
	}

	/**
	 * Get attribute data of given object
	 * @param type $productObj
	 * @param type $atttext
	 * @param type $da
	 * @return type 
	 */
	protected static function getAttributeValueAllStore($productObj, $atttext, $da) {
		$data = array();
		if ($da == 'text' || $da == 'textarea') {
			$dataarray = $productObj->getStoreIds();
			$dataarray[] = 0;
			foreach ($dataarray as $_storeId) {
				$tempProductObj = new Mage_Catalog_Model_Product();
				$tempProductObj->setStoreId($_storeId);
				$tempProductObj->load($productObj->getId());
				$data[count($data)] = array('storeid' => $_storeId, 'value' => $tempProductObj->getData($atttext));
				unset($tempProductObj);
			}
		}
		return $data;
	}

	protected static function getAttributeValue($obj) {
		if (is_array($obj)) {
			return implode(",", $obj);
		} else {
			return $obj;
		}
	}


	/**
	 * Get all option list of given attribute
	 * @param type $attributename
	 * @param type $arraydata
	 * @return type 
	 */
	protected static function getAttributeOptions($attributename, $arraydata) {
		$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributename);
		if ($attribute->usesSource()) {
			$options = $attribute->getSource()->getAllOptions(false);
		}
		$label = '';
		$dd = explode(',', $arraydata);

		foreach ($options as $key => $value) {
			$localbool = false;
			foreach ($value as $k => $v) {
				if ($localbool == true) {
					if (in_array($attribute->getSource()->getOptionId($v), $dd)) {
						$label = $label . $v . ',';
					}
				}
				$localbool = true;
			}
		}
		if ($label)
			$label = substr($label, 0, strlen($label) - 1);
		return $label;
	}

	/**
	 * Get all list of products without sapsync concept
	 * @param type $filters
	 * @return type 
	 */
	public function unsyncProducts($filters = null) {
		return $this->info($null, $filters, false);
	}
	
	// remove special char
	protected static function removeSpecialChar($string) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $string);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		return $clean;
	}

	// Product Sync
	public function syncUpdate($productId, $Flag, $websiteid) {
		$element = explode(",", $websiteid);
		$productsid=explode("|",$productId);
		$_storeIds = array();
		$_storeIds[0] = Mage_Core_Model_App::ADMIN_STORE_ID;
		foreach ($element as $website) {
			$website_model = Mage::getModel('core/website');
			$_storeIds = array_merge($_storeIds, $website_model->load($website, 'website_id')->getStoreIds());
		}
		
		$returnvalue = false;
		foreach ($_storeIds as $_eachStoreId) {
			$objSapConfig = new Insync_Sap_Model_Config();
			foreach($productsid as $product_id){
				$product = $objSapConfig->_getProduct($product_id, $_eachStoreId, 'id');
				if ($product->getId()) {
					$product->setData('sap_sync', $Flag);
					$product->save();
					$returnvalue = true;
				}
				unset($product);
			}
			
		}
		return $returnvalue;
	}

	protected static function _setDefaultChecked($productId, $productWebsiteIds) {
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

}
