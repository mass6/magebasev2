<?php


class Insync_Sales_Model_Order_Api_V2 extends Mage_Sales_Model_Order_Api {

    /**
     * Retrieve list of orders by filters
     *
     * @param array $filters
     * @return array
     */
    public function items($filters = null) {
        //TODO: add full name logic

        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';

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
		} else {
			$websiteid = explode(',', $websiteid);
		}
		
		$filterstore = array();
		foreach ($websiteid as $website) {
			$website_model = Mage::getModel('core/website');
			$_storeIds = $website_model->load($website, 'website_id')->getStoreIds();

			foreach ($_storeIds as $key => $value) {
				$filterstore[] = $value;
			}
		}

		$result = array();
		$websitearray = $this->getWebsiteArray();
        $collection = Mage::getModel("sales/order")->getCollection()
                ->addAttributeToSelect('*')
                ->addAddressFields()
                ->addExpressionFieldToSelect(
                        'billing_firstname', "{{billing_firstname}}", array('billing_firstname' => "$billingAliasName.firstname")
                )
                ->addExpressionFieldToSelect(
                        'billing_lastname', "{{billing_lastname}}", array('billing_lastname' => "$billingAliasName.lastname")
                )
                ->addExpressionFieldToSelect(
                        'shipping_firstname', "{{shipping_firstname}}", array('shipping_firstname' => "$shippingAliasName.firstname")
                )
                ->addExpressionFieldToSelect(
                        'shipping_lastname', "{{shipping_lastname}}", array('shipping_lastname' => "$shippingAliasName.lastname")
                )
                ->addExpressionFieldToSelect(
                        'billing_name', "CONCAT({{billing_firstname}}, ' ', {{billing_lastname}})", array('billing_firstname' => "$billingAliasName.firstname", 'billing_lastname' => "$billingAliasName.lastname")
                )
                ->addExpressionFieldToSelect(
                'shipping_name', 'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})', array('shipping_firstname' => "$shippingAliasName.firstname", 'shipping_lastname' => "$shippingAliasName.lastname")
				)
				->addFieldToFilter('status', array(
					'neq' => 'pending_approval'
				))
				->addFieldToFilter('store_id',$filterstore)
				->addFieldToFilter('SapSync', 0);

        if (!empty($preparedFilters)) {
            try {
                foreach ($preparedFilters as $field => $value) {
					if ($field == 'website_id') {
						continue;
					}
					
                    if (isset($this->_attributesMap['order'][$field])) {
                        $field = $this->_attributesMap['order'][$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }
        foreach ($collection as $order) {
            $rOrder = $this->_getAttributes($order, 'order');
			$contractId = $order->getContractId();
			$customerAddressId = $order->getContractship();
			Mage::log('$customerAddressId=======' );
			Mage::log($customerAddressId );
			$contractName = '';
			$contractBillName = '';
			$tempContractStreet = array();
			$tempContractCity = '';
			$tempContractCountry = '';
			$tempContracState = '';
			$tempContractZip = '';
			$contractShipName = '';
			$tempShipContractStreet = array();
			$tempShipContractCity = '';
			$tempShipContractCountry = '';
			$tempShipContracState = '';
			$tempShipContractZip = '';
			$contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
			foreach ($contractDetails as $each) {
			$contractName = $each['cname'];
			$contractCode = $each['code'];
			$refid = $each['name'];
			if ($each['name_bill'] != '') $contractBillName = $each['name_bill'];
			if ($each['street_bill'] != '') $tempContractStreet[] = $each['street_bill'];
            if ($each['street1_bill'] != '') $tempContractStreet[] = $each['street1_bill'];
            if ($each['city_bill'] != '') $tempContractCity = $each['city_bill'];
            if ($each['country_bill'] != '0') $tempContractCountry = $each['country_bill'];
            if ($each['state_bill'] != '') $tempContracState = $each['state_bill'];
            if ($each['zip_bill'] != '') $tempContractZip = $each['zip_bill'];
			
			if ($customerAddressId == 10001) {
                if ($each['name_ship1'] != '') $contractShipName = $each['name_ship1'];
				if ($each['street_ship1'] != '') $tempShipContractStreet[] = $each['street_ship1'];	
                if ($each['street1_ship1'] != '') $tempShipContractStreet[] = $each['street1_ship1'];
                if ($each['city_ship1'] != '') $tempShipContractCity = $each['city_ship1'];
                if ($each['country_ship1'] != '0') $tempShipContractCountry = $each['country_ship1'];
                if ($each['state_ship1'] != '') $tempShipContracState = $each['state_ship1'];
                if ($each['zip_ship1'] != '') $tempShipContractZip = $each['zip_ship1'];
            }else if($customerAddressId == 10002) {
                 if ($each['name_ship2'] != '') $contractShipName = $each['name_ship2'];
				if ($each['street_ship2'] != '') $tempShipContractStreet[] = $each['street_ship2'];	
                if ($each['street1_ship2'] != '') $tempShipContractStreet[] = $each['street1_ship2'];
                if ($each['city_ship2'] != '') $tempShipContractCity = $each['city_ship2'];
                if ($each['country_ship2'] != '0') $tempShipContractCountry = $each['country_ship2'];
                if ($each['state_ship2'] != '') $tempShipContracState = $each['state_ship2'];
                if ($each['zip_ship2'] != '') $tempShipContractZip = $each['zip_ship2'];
            }else if ($customerAddressId == 10003) {
                 if ($each['name_ship3'] != '') $contractShipName = $each['name_ship3'];
				if ($each['street_ship3'] != '') $tempShipContractStreet[] = $each['street_ship3'];	
                if ($each['street1_ship3'] != '') $tempShipContractStreet[] = $each['street1_ship3'];
                if ($each['city_ship3'] != '') $tempShipContractCity = $each['city_ship3'];
                if ($each['country_ship3'] != '0') $tempShipContractCountry = $each['country_ship3'];
                if ($each['state_ship3'] != '') $tempShipContracState = $each['state_ship3'];
                if ($each['zip_ship3'] != '') $tempShipContractZip = $each['zip_ship3'];
            } else if ($customerAddressId == 10004) {
                if ($each['name_ship4'] != '') $contractShipName = $each['name_ship4'];
				if ($each['street_ship4'] != '') $tempShipContractStreet[] = $each['street_ship4'];	
                if ($each['street1_ship4'] != '') $tempShipContractStreet[] = $each['street1_ship4'];
                if ($each['city_ship4'] != '') $tempShipContractCity = $each['city_ship4'];
                if ($each['country_ship4'] != '0') $tempShipContractCountry = $each['country_ship4'];
                if ($each['state_ship4'] != '') $tempShipContracState = $each['state_ship4'];
                if ($each['zip_ship4'] != '') $tempShipContractZip = $each['zip_ship4'];
            }else{
                if ($each['name_ship5'] != '') $contractShipName = $each['name_ship5'];
				if ($each['street_ship5'] != '') $tempShipContractStreet[] = $each['street_ship5'];	
                if ($each['street1_ship5'] != '') $tempShipContractStreet[] = $each['street1_ship5'];
                if ($each['city_ship5'] != '') $tempShipContractCity = $each['city_ship5'];
                if ($each['country_ship5'] != '0') $tempShipContractCountry = $each['country_ship5'];
                if ($each['state_ship5'] != '') $tempShipContracState = $each['state_ship5'];
                if ($each['zip_ship5'] != '') $tempShipContractZip = $each['zip_ship5'];
            }
			}
			$rOrder['ref_id'] = $refid;
			$rOrder['contract_code'] = $contractCode;
			$rOrder['contract_name'] = $contractName;
            $rOrder['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
			$rOrder['shipping_address']['street'] = $tempShipContractStreet;
			$rOrder['shipping_address']['firstname'] = $contractShipName;
			$rOrder['shipping_address']['lastname'] = '';
			$rOrder['shipping_address']['city'] = $tempShipContractCity;
			$rOrder['shipping_address']['postcode'] = $tempShipContractZip;
			$rOrder['shipping_address']['country_id'] = $tempShipContractCountry;
			$rOrder['shipping_address']['region'] = $tempShipContracState;
			$rOrder['shipping_address']['telephone'] = '';
			$rOrder['shipping_address']['address_id'] = '';
            $rOrder['billing_address'] = $this->_getAttributes($order->getBillingAddress(), 'order_address');
			$rOrder['billing_address']['street'] = $tempContractStreet;
			$rOrder['billing_address']['firstname'] = $contractBillName;
			$rOrder['billing_address']['lastname'] = '';
			$rOrder['billing_address']['city'] = $tempContractCity;
			$rOrder['billing_address']['postcode'] = $tempContractZip;
			$rOrder['billing_address']['country_id'] = $tempContractCountry;
			$rOrder['billing_address']['region'] = $tempContracState;
			$rOrder['billing_address']['telephone'] = '';
			$rOrder['billing_address']['address_id'] = '';
			
            if ($rOrder['shipping_address']['region_id'] != NULL && array_key_exists('region_id', $rOrder['shipping_address'])) {
                $regionObj = new Mage_Directory_Model_Region();
                $regionObj->load($rOrder['shipping_address']['region_id']);
                if ($regionObj->getData('code') != NULL) {
                    $rOrder['shipping_address']['region'] = $rOrder['shipping_address']['region'] . '@' . $regionObj->getData('code');
                }
                unset($regionObj);
            }
            if ($rOrder['billing_address']['region_id'] != NULL && array_key_exists('region_id', $rOrder['billing_address'])) {
                $regionObj = new Mage_Directory_Model_Region();
                $regionObj->load($rOrder['billing_address']['region_id']);
                if ($regionObj->getData('code') != NULL) {
                    $rOrder['billing_address']['region'] = $rOrder['billing_address']['region'] . '@' . $regionObj->getData('code');
                }
                unset($regionObj);
            }
            $rOrder['itemss'] = array();
			$ordertotaltaxamount=0;			
			foreach ($order->getAllItems() as $item) {
                $item['tax_code'] = $this->_setTaxCode($order);
				if(0==(int)$item['price'] && 0==(int)$item['base_price'] && 0==(int)$item['original_price']){
					$item['tax_code'] = '';
				}
				if(0==(int)$item['tax_percent']){
					$item['tax_code'] = '';
				}
				$ordertotaltaxamount += $item['tax_amount'];
                $rOrder['itemss'][] = $this->_getAttributes($item, 'order_item');
            }

            // couponcode start -------------------------------------------------
			$c = Mage::getResourceModel('salesrule/rule_collection');
			$c->getSelect();
			$discountPercent=0;
			$copcode;
			foreach ($c->getItems() as $item) {
				if (($item->getData('code') == '') ) {
					continue;
				}else if($item->getData('code')==$order->getCouponCode()){
					if ($item->getData('simple_action') == 'by_fixed') {
						Mage::log('by_fixed');
						$discountPercent = ($item->getData('discount_amount')) * count($rOrder['items']);
					} else if ($item->getData('simple_action') == 'by_percent') {
						Mage::log('by_percent');
						$discountPercent = ($item->getData('discount_amount') * 100) / $order->getBaseSubtotal();
					}
				}
			}
			unset($c);

			$rOrder['couponcode'] = $order->getCouponCode();
			$rOrder['coupondiscountpercent'] = $discountPercent;
			// couponcode end -------------------------------------------------
			//$rOrder['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');
			// payment detail start ---------------------------------------------
			$temppayment = $this->_getAttributes($order->getPayment(), 'order_payment');

			$temp = $temppayment['last_trans_id'];
			if (isset($temp)) {
				if (strlen($temp) > 5)
					$temppayment['last_trans_id'] = substr($temp, strlen($temp) - 5, strlen($temp) - 1);
				else
					$temppayment['last_trans_id'] = $temp;
			}
			$cc_type_name = array(
				'VI' => 'Visa', 'MC' => 'MasterCard', 'DI' => 'Discover', 'AE' => 'Amex', 'SM' => 'Maestro', 'SO' => 'Solo');

			$temppayment['cc_number'] = $order->getPayment()->getData('cc_number');
			$temppayment['cc_type_name'] = $cc_type_name[$order->getPayment()->getData('cc_type')];

			if (trim($temppayment['last_trans_id']) == '') { // if last_trans_id not found, look it in additional_information
				foreach ($temppayment['additional_information'] as $additionalarray1) {
					foreach ($additionalarray1 as $additionalarray2) {
						foreach ($additionalarray2 as $key => $value) {
							Mage::log($key);
							Mage::log($value);
							$temppayment[$key] = $value;
							
							if ($key == 'cc_type')
								$temppayment['cc_type_name'] = $cc_type_name[$value];
						}
					}
				}
			}else{
				foreach ($temppayment['additional_information'] as $key => $value) {
					if($key=='paypal_payer_id')	{
						$temppayment['cc_number'] = $value;
						break;
					}
							
				}
			}

			$rOrder['payment'] = $temppayment;
			$rOrder['status_history'] = array();
			foreach ($order->getAllStatusHistory() as $history) {
				$rOrder['status_history'][] = $this->_getAttributes($history, 'order_status_history');
			}
			
			if($rOrder['tax_amount'] != $ordertotaltaxamount){
				$rOrder['shipment_tax'] = $this->_getShipmentTaxDetails($order, 'code');
				$rOrder['shipping_tax_amount'] = $this->_getShipmentTaxDetails($order, 'amount');
			}else{
				$rOrder['shipment_tax'] = '';
				$rOrder['shipping_tax_amount'] = 0;
			}
			
			$result[] = $rOrder;
        }

        return $result;
    }
	
	protected function _getShipmentTaxDetails($order, $type = '') {
		$result = '';
		
		// --------------
		// $producttaxclass=-1;
		// foreach ($order->getAllItems() as $item) {
			// Mage::log('-------------------------------------------');
			// Mage::log($item->toArray());
			// $objSapConfig = new Insync_Sap_Model_Config();
			// $product = $objSapConfig->_getProduct($item['sku'], Mage_Core_Model_App::ADMIN_STORE_ID, 'sku');
			// $producttaxclass = $product->getTaxClassId();
			// unset($objSapConfig);
			// break;
		// }
		// --------------

		$storeId = $order->getStoreId();
		$store = Mage::getModel('core/store')->load($storeId);

		$customerTaxClassId = Mage::getModel('customer/group')->getTaxClassId($order->getCustomerGroupId());
		$shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $storeId);

		$taxCalculationModel = Mage::getModel('tax/calculation');
		$request = $taxCalculationModel->getRateRequest($order->getShippingAddress(), $order->getBillingAddress(), $customerTaxClassId, $store);
		$request->setProductClassId($shippingTaxClass);

		$taxMysqlCalculationModel = new Mage_Tax_Model_Mysql4_Calculation();
		$rateInfo = $taxMysqlCalculationModel->getRateInfo($request);
		Mage::log('------------$rateInfo-------------------------------');
		Mage::log($rateInfo);

		if (array_key_exists('process', $rateInfo)) {
			if (count($rateInfo['process'])) {
				
				$_amount=0;
				$_code = '';
				$_rate = 0;
				// if($producttaxclass!=0){
					$_rate = $rateInfo['value'];
					$_code = $rateInfo['process'][0]['id'];
					$_amount = number_format(round($order->getShippingAmount() * $_rate / 100, 4), 4);
				// }
				switch ($type) {
					case 'code':
						$result = $_code;
						break;

					case 'rate':
						$result = $_rate;
						break;

					case 'amount':
						$result = $_amount;
						break;

					default:
						$result = array(
							'code' => $_code,
							'rate' => $_rate,
							'amount' => $_amount,
						);
						break;
				}
			}
		}

		return $result;
	}

    protected function getWebsiteArray() {
		$stores=Mage::getModel('core/store')->getCollection();
		$websitearray = array();
		foreach ($stores as $store) {
			$websitearray[$store->getId()] = $store->getWebsiteId();
		}
		$websitearray[0] = 0;
        return $websitearray;
    }

    protected function _setTaxCode($ordObj) {
		$obj = new Mage_Tax_Model_Mysql4_Sales_Order_Tax_Collection();
		$obj->loadByOrder($ordObj);
		foreach($obj->getData('code') as $data){
			if(isset($data['code'])){
				return $data['code'];
			}else{
				return 'NoTaxCode';
			}
		}
	}
   
	public function syncOrderSBO($orderIncrementId, $flag) {
		try {
			$idarray = str_replace('|',',',$orderIncrementId);
			$idarray = str_replace('\'','',$idarray);
			$conn = Mage::getSingleton('core/resource')->getConnection('sales_write');
			$data = array('SapSync' => $flag);
			$where = "increment_id in (" . $idarray . ")";
			$conn->update(Insync_Sap_Model_Config::getTableSapContext('sales_order'), $data, $where);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	

	
    public final function listSalesOrderStatus() {
	
		$listStatus = Mage::getResourceModel('sales/order_status_collection')
            ->toOptionHash();
        $options = array();

        if (count($listStatus)) {
            foreach ($listStatus as $code => $name) {
                $options[] = array(
                    'status_name' => $name,
                    'status_code' => $code,
                );
            }
        }

        return $options;
    }

	public function create($customerid, $shippingmethod, $shippingdate, $productdata, $mailtoclient,$storeId=null,$BillingAdd='',$ShippingAdd='', $currencyCode=null) {
		$order = array();
		try {
			$msg = $this->validateShipping($shippingmethod, $shippingdate);
			if ($msg != '') {
				//$this->_fault('error');
				$order['error'] = $msg;
				return $order;
			}

			$productitem = array();
			foreach ($productdata as $partdata) {
				$skudata = '';
				$qtydata = '';
				$price = '';
				$discount = '';
				foreach ($partdata as $key => $value) {
					if ($key == 'sku'){
						$skudata = $value;
					}else if ($key == 'qty'){
						$qtydata = $value;
					}else if ($key == 'price'){
						$price = $value;
					}else if($key == 'discount'){
						$discount = $value;
					}
				}
				$productitem[$skudata] = $price;
				
				$part = array("PartId" => $skudata, "Quantity" => $qtydata, "Price" => $price, "Discount" => $discount);
				$shopping_cart[] = $part;
			}
			if (count($shopping_cart) == 0) {
				$this->_fault('error', 'No product is added for placing order');
				$order['error'] = 'No product is added for placing order';
				return $order;
			}

			$customer = Mage::getModel('customer/customer')
					->load($customerid);
			if (!$customer->getId()) {
				$this->_fault('error', 'Customer ID ' . $customerid . ' does not exist in web so order cannot be placed', $customerid);
				$order['error'] = 'Customer ID ' . $customerid . ' does not exist in web so order cannot be placed';
				return $order;
			}
			if (!$customer->getDefaultBillingAddress()) {
				$this->_fault('error', 'Billing Address for Customer ID ' . $customerid . ' does not exist in web so order cannot be placed');
				$order['error'] = 'Billing Address for Customer ID ' . $customerid . ' does not exist in web so order cannot be placed';
				return $order;
			}
			if (!$customer->getDefaultShippingAddress()) {
				$this->_fault('error', 'Shipping Address for Customer ID ' . $customerid . ' does not exist in web so order cannot be placed');
				$order['error'] = 'Shipping Address for Customer ID ' . $customerid . ' does not exist in web so order cannot be placed';
				return $order;
			}

			
			$params = array("AccountNo" => $customerid, "PartCart" => $shopping_cart);
			$quoteId = $this->PrepareOrder($params, $customerid, $currencyCode, $storeId);
			$order = $this->ConfirmOrder($quoteId, $mailtoclient, $productitem,$storeId,$BillingAdd,$ShippingAdd, $currencyCode);
			
			$order['quoteId'] = $quoteId;
			return $order;
		} catch (Exception $e) {
			Mage::log($e);
		}
	}

	public function validateShipping($shippingmethodparam, $shippingdate) {
		try {
			$carriers = Mage::getStoreConfig('carriers');
			$ship = explode('_', $shippingmethodparam);
			$shippingmethod = $ship[0];
			if (is_array($carriers)) {
				if (isset($carriers) && !empty($carriers)) {
					if ($shippingmethod != '') {
						if (isset($shippingmethod) && !empty($shippingmethod)) {
							if (array_key_exists(strtolower($shippingmethod), $carriers)) {
								if ($carriers[strtolower($shippingmethod)]['active']) {
									$this->shippingMethod = strtolower($shippingmethod);
								} else {
									return sprintf('Shipping method %s is not active in web', $shippingmethod);
								}
							} else {
								return sprintf('Shipping method %s does not exist in web', $shippingmethod);
							}
						} else {
							return sprintf('Shipping method cannot be empty');
						}
					} else {
						return sprintf("Shipping method information is required");
					}
				} else {
					return sprintf('No Shipping method exists in web');
				}
			}

			$this->paymentMethod = 'checkmo';
			if (Insync_Sap_Model_Config::IS_SHIPPING_DATE_REQUIRED) {
				if (isset($shippingdate) && !empty($shippingdate)) {
					$this->shipping_date = $shippingdate;
				}
			}
		} catch (Exception $e) {
			//echo $e;
		}
	}

	public function PrepareOrder($params, $customerid, $currencyCode=null, $storeId=null) {
		try {
			foreach ($params as $k => $v) {
				$$k = $v;
			}
			$customerObj = Mage::getModel('customer/customer')->load($customerid);
			$storeId = $customerObj->getStoreId();
			$quoteObj = Mage::getModel('sales/quote')->assignCustomer($customerObj);
			$address = $quoteObj->getShippingAddress();
			$address->setCollectShippingRates(true);
			$quoteObj->getShippingAddress()->setShippingMethod($this->shippingMethod);
			$storeObj = $quoteObj->getStore()->load($storeId);
			$quoteObj->setStore($storeObj);
			
			foreach ($PartCart as $part) {
				foreach ($part as $k => $v) {
					$$k = $v;
				}
				$productModel = Mage::getModel('catalog/product');
				$productObj = $productModel->load($productModel->getIdBySku($PartId));
				$quoteItem = Mage::getModel('sales/quote_item')->setProduct($productObj);
				$quoteItem->setQuote($quoteObj);
				$quoteItem->setQty($Quantity);
				$quoteItem->setPrice($Price);
				$quoteItem->setRowTotal($Price * $Quantity);
				$quoteItem->setBaseRowTotal($Price * $Quantity);
				$quoteObj->addItem($quoteItem);
			}
			$quoteObj->collectTotals();
			$quoteObj->save();
			$quoteId = $quoteObj->getId();
			return $quoteId;
		} catch (Exception $e) {
			Mage::log($e);
		}
	}

	public function ConfirmOrder($quoteId, $mailtoclient, $productitem,$storeId=null,$BillingAdd = '',$ShippingAdd='',$currencyCode=null) {
		try {
			$quoteObj = Mage::getModel('sales/quote')->load($quoteId);
			$items = $quoteObj->getAllItems();
			$quoteObj->collectTotals();
			$quoteObj->reserveOrderId();

			$quotePaymentObj = $quoteObj->getPayment();
			$quotePaymentObj->setMethod($this->paymentMethod);
			$quoteObj->setPayment($quotePaymentObj);

			$convertQuoteObj = Mage::getSingleton('sales/convert_quote');
			$orderPaymentObj = $convertQuoteObj->paymentToOrderPayment($quotePaymentObj);

			$orderObj = $convertQuoteObj->addressToOrder($quoteObj->getShippingAddress());
			
			$orderObj->setBillingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getBillingAddress()));
			$orderObj->setShippingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getShippingAddress()));
			if($storeId!=null)
			{
			$orderObj->setStoreId($storeId);
			}
			$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
			$temp = '';
			foreach ($methods as $shippigCode=>$shippingModel) 
			{
			$shippingTitle = Mage::getStoreConfig('carriers/'.$shippigCode.'/title');
			if($shippigCode == $this->shippingMethod)
			$temp = $shippingTitle;
			}
			$orderObj->setShippingDescription($temp);

			$orderObj->setPayment($convertQuoteObj->paymentToOrderPayment($quoteObj->getPayment()));
			if (Insync_Sap_Model_Config::IS_SHIPPING_DATE_REQUIRED) {
				$orderObj->setDate($this->shipping_date);
			}
			
			$subTotal = 0;
			foreach ($items as $item) {
				$orderItem = $convertQuoteObj->itemToOrderItem($item);
				if ($item->getParentItem()) {
					$orderItem->setParentItem($orderObj->getItemByQuoteItemId($item->getParentItem()->getId()));
				}
				$orderItem['price']=$item->getPrice();
				$orderItem['original_price']=$item->getPrice();
				$rowTotal = $item->getPrice() * $item->getQty();
				$subTotal += $rowTotal;
				$orderObj->addItem($orderItem);
			}
			$orderObj->setSubtotal($orderObj->getQuoteBaseGrandTotal())
				->setBaseSubtotal($orderObj->getQuoteBaseGrandTotal())
				->setGrandTotal($orderObj->getQuoteBaseGrandTotal())
				->setBaseGrandTotal($orderObj->getQuoteBaseGrandTotal());
			$orderObj->setBase_currency_code($currencyCode);
			$orderObj->setStore_currency_code($currencyCode);
			$orderObj->setOrder_currency_code($currencyCode);
			$orderObj->setGlobal_currency_code($currencyCode);
			
			$orderObj->setCanShipPartiallyItem(false);
			$totalDue = $orderObj->getTotalDue();
			$orderObj->place();
			$orderObj->setData('SapSync', 1);
			if($ShippingAdd!='')$orderObj->setData('contract_shipping', $ShippingAdd);
			if($BillingAdd!='')$orderObj->setData('contract_billing', $BillingAdd);
			$orderObj->save();
			// Mage::log($orderObj);
			
			$orderitem = array();
			foreach ($orderObj->getAllItems() as $item) {
			// $orderObj->setData('original_price', $data['price']);
			// $orderObj->save();
				$data = $this->_getAttributes($item, 'order_item');
				$orderitem[] = array('sku'=>$data['sku'], 'qty'=>$data['qty_ordered'], 'id'=>$data['item_id'], 'price'=>$data['price'],'discount'=>$data['discount']);
			}
			if ($mailtoclient == true){
				Mage::helper('insync_approve')->sendEmail($orderObj->getCustomerId(), $orderObj, 'sales_email_order_multi',$storeId, array());
			}

			$webOrderId = $orderObj->getIncrementId();
			$orderId = $orderObj->getId();
			$data = array('incrementid' => $webOrderId, 'orderid' => $orderId, 'orderitem'=>$orderitem);
			return $data;
		} catch (Exception $e) {
			Mage::log($e);
		}
	}
}
