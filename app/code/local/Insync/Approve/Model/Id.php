<?php

class Insync_Approve_Model_Id extends Mage_Sales_Model_Order {

    public function Id($id, $customerId, $storeid = null, $currencyCode = null) { // class Mage_SalesRule_Model_Observer
        $contractleveloverridepermission = '';
        $contractShipName = '';
        $contractCode = '';
        $contractShipping = '';
        $order = Mage::getModel('sales/order')->load($id);
        $storeId = $order->getStoreId();
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
        $quote = $order->getQuoteId();
        $firstApp = '';
        $model = Mage::getModel('custom/custom_quote')->getCollection()->addFieldToFilter('quote_id', $quote);
        foreach ($model as $each) {
            $firstApp = $each->getValue();
        }
        unset($model);
		
        // $objSapConfig = new Insync_Sap_Model_Config();
        $order1 = Mage::getModel('sales/order')->load($id);
        $orderItem = Mage::getModel('sales/order_item')->load($id);
        $collection = Mage::getModel("sales/order")->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $id);
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $data['value'] = 1;
        $tableName = Mage::getSingleton("core/resource")->getTableName('catalog_product_entity_int');
        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cust_category');

        foreach ($collection as $orders) {
            $storeId = $order->getStoreId();
            // Mage::log('store idf');
            // Mage::log($storeId);
            foreach ($orders->getAllItems() as $item) {
                $item->toArray();
                $where = array('entity_id=?' => $item['product_id'],
                    'attribute_id=?' => 96,
                    'store_id=?' => $storeId
                );
                $db->update($tableName, $data, $where);
                unset($where);
            }
        }
        $date = $order->getCreatedAtFormated('long');
        $BaseGrandTotal = $order1->getBaseGrandTotal();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $contractId = $customer->getContractId();
        $authorityLimit = $customer->getAuthorityLimit();
        $customerAddressId = $customer->getTempShipping();
        //Contract Shipping in Sales Flat Order
        //order comment


        $custFirstName = $order1->getCustomerFirstname();
        $custLastName = $order1->getCustomerLastname();
        $name = $custFirstName . ' ' . $custLastName;
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT MAX(`entity_id`) FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $id . "";
        $entityid = $read->fetchAll($sql);
        $tempentityid = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {
                $tempentityid = $value;
            }
        }
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT  `comment` FROM  `sales_flat_order_status_history` WHERE  `entity_id` = " . $tempentityid . "";
        $entityid = $read->fetchAll($sql);
        $tempCustId = '';
        $tempComment = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {
                if ($key1 == 'comment') {
                    $tempComment = $value;
                }
            }
        }
        if ($tempComment != '') {
            $finalComment = $name . ' | ' . $tempComment;
            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
            $sql = sprintf("update `sales_flat_order_status_history` set `comment` = '%s' where `entity_id` = '%d'", $finalComment, $tempentityid);
            $write->query($sql);
            unset($write);
        } else {
            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
            $sql = sprintf("update `sales_flat_order_status_history` set `comment` = '%s' where `entity_id` = '%d'", $name, $tempentityid);
            $write->query($sql);
            unset($write);
        }

        //end 
        $contractName = '';
        $contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
        $userId = array();
        $tempContractShipping = array();
        foreach ($contractDetails as $each) {
            //user id
            $contractleveloverridepermission = $each['alo'];
            if ($each['user1'] != '0')
                $userId[] = $each['user1'];
            if ($each['user2'] != '0')
                $userId[] = $each['user2'];
            if ($each['user3'] != '0')
                $userId[] = $each['user3'];
            if ($each['user4'] != '0')
                $userId[] = $each['user4'];
            if ($each['user5'] != '0')
                $userId[] = $each['user5'];
            if ($each['user6'] != '0')
                $userId[] = $each['user6'];
            if ($each['user7'] != '0')
                $userId[] = $each['user7'];
            if ($each['user8'] != '0')
                $userId[] = $each['user8'];
            if ($each['user9'] != '0')
                $userId[] = $each['user9'];
            if ($each['user10'] != '0')
                $userId[] = $each['user10'];
            if ($each['user11'] != '0')
                $userId[] = $each['user11'];
            if ($each['user12'] != '0')
                $userId[] = $each['user12'];
            if ($each['user13'] != '0')
                $userId[] = $each['user13'];
            if ($each['user14'] != '0')
                $userId[] = $each['user14'];
            if ($each['user15'] != '0')
                $userId[] = $each['user15'];
            if ($each['user16'] != '0')
                $userId[] = $each['user16'];
            if ($each['user17'] != '0')
                $userId[] = $each['user17'];
            if ($each['user18'] != '0')
                $userId[] = $each['user18'];
            if ($each['user19'] != '0')
                $userId[] = $each['user19'];
            if ($each['user20'] != '0')
                $userId[] = $each['user20'];
            $contractName = $each['name'];
            //end user id

            $country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
            foreach ($country_list as $country) {
                // $temp[]= $country['Please Select'] ;
                if ($country['value'] == $each['country_bill'])
                    $tempBill = $country['label'];
            }
            if ($each['name_bill'] != '') {
                $tempContractBilling[] = $each['name_bill'];
                $contractBillName = $each['name_bill'];
            }
            if ($each['street_bill'] != '')
                $tempContractBilling[] = $each['street_bill'];
            if ($each['street1_bill'] != '')
                $tempContractBilling[] = $each['street1_bill'];
            if ($each['city_bill'] != '')
                $tempContractBilling[] = $each['city_bill'];
            if ($each['country_bill'] != 0)
                $tempContractBilling[] = $tempBill;
            if ($each['state_bill'] != '')
                $tempContractBilling[] = $each['state_bill'];
            if ($each['zip_bill'] != '')
                $tempContractBilling[] = $each['zip_bill'];
            $contractCode = implode('<br/>', $tempContractBilling);


            if ($customerAddressId == 10001) {
                $country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
                foreach ($country_list as $country) {
                    // $temp[]= $country['Please Select'] ;
                    if ($country['value'] == $each['country_ship1'])
                        $tempShip1 = $country['label'];
                }

                if ($each['name_ship1'] != '') {
                    $tempContractShipping[] = $each['name_ship1'];
                    $contractShipName = $each['name_ship1'];
                }
                if ($each['street_ship1'] != '')
                    $tempContractShipping[] = $each['street_ship1'];
                if ($each['street1_ship1'] != '')
                    $tempContractShipping[] = $each['street1_ship1'];
                if ($each['city_ship1'] != '')
                    $tempContractShipping[] = $each['city_ship1'];
                if ($each['country_bill'] != 0)
                    $tempContractShipping[] = $tempShip1;
                if ($each['state_ship1'] != '')
                    $tempContractShipping[] = $each['state_ship1'];
                if ($each['zip_ship1'] != '')
                    $tempContractShipping[] = $each['zip_ship1'];

                $contractShipping = implode('<br/>', $tempContractShipping);
            }

            if ($customerAddressId == 10002) {
                $country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
                foreach ($country_list as $country) {
                    // $temp[]= $country['Please Select'] ;
                    if ($country['value'] == $each['country_ship2'])
                        $tempShip2 = $country['label'];
                }

                if ($each['name_ship2'] != '') {
                    $tempContractShipping[] = $each['name_ship2'];
                    $contractShipName = $each['name_ship2'];
                }
                if ($each['street_ship2'] != '')
                    $tempContractShipping[] = $each['street_ship2'];
                if ($each['street1_ship2'] != '')
                    $tempContractShipping[] = $each['street1_ship2'];
                if ($each['city_ship2'] != '')
                    $tempContractShipping[] = $each['city_ship2'];
                if ($each['country_bill2'] != 0)
                    $tempContractShipping[] = $tempShip2;
                if ($each['state_ship2'] != '')
                    $tempContractShipping[] = $each['state_ship2'];
                if ($each['zip_ship2'] != '')
                    $tempContractShipping[] = $each['zip_ship2'];
                $contractShipping = implode('<br/>', $tempContractShipping);
            }

            if ($customerAddressId == 10003) {
                $country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
                foreach ($country_list as $country) {
                    // $temp[]= $country['Please Select'] ;
                    if ($country['value'] == $each['country_ship3'])
                        $tempShip3 = $country['label'];
                }
                if ($each['name_ship3'] != '') {
                    $tempContractShipping[] = $each['name_ship3'];
                    $contractShipName = $each['name_ship3'];
                }
                if ($each['street_ship3'] != '')
                    $tempContractShipping[] = $each['street_ship3'];
                if ($each['street1_ship3'] != '')
                    $tempContractShipping[] = $each['street1_ship3'];
                if ($each['city_ship3'] != '')
                    $tempContractShipping[] = $each['city_ship3'];
                if ($each['country_bill3'] != 0)
                    $tempContractShipping[] = $tempShip3;
                if ($each['state_ship3'] != '')
                    $tempContractShipping[] = $each['state_ship3'];
                if ($each['zip_ship3'] != '')
                    $tempContractShipping[] = $each['zip_ship3'];
                $contractShipping = implode('<br/>', $tempContractShipping);
            }

            if ($customerAddressId == 10004) {
                $country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
                foreach ($country_list as $country) {
                    // $temp[]= $country['Please Select'] ;
                    if ($country['value'] == $each['country_ship4'])
                        $tempShip4 = $country['label'];
                }

                if ($each['name_ship4'] != '') {
                    $tempContractShiping[] = $each['name_ship4'];
                    $contractShipName = $each['name_ship4'];
                }
                if ($each['street_ship4'] != '')
                    $tempContractShiping[] = $each['street_ship4'];
                if ($each['street1_ship4'] != '')
                    $tempContractShipping[] = $each['street1_ship4'];
                if ($each['city_ship4'] != '')
                    $tempContractShipping[] = $each['city_ship4'];
                if ($each['country_bill4'] != 0)
                    $tempContractShipping[] = $tempShip4;
                if ($each['state_ship4'] != '')
                    $tempContractShipping[] = $each['state_ship4'];
                if ($each['zip_ship4'] != '')
                    $tempContractShipping[] = $each['zip_ship4'];
                $contractShipping = implode('<br/>', $tempContractShipping);
            }

            if ($customerAddressId == 10005) {
                $country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
                foreach ($country_list as $country) {
                    if ($country['value'] == $each['country_ship5'])
                        $tempShip5 = $country['label'];
                }
                if ($each['name_ship5'] != '') {
                    $tempContractShipping[] = $each['name_ship5'];
                    $contractShipName = $each['name_ship5'];
                }
                if ($each['street_ship5'] != '')
                    $tempContractShipping[] = $each['street_ship5'];
                if ($each['street1_ship5'] != '')
                    $tempContractShipping[] = $each['street1_ship5'];
                if ($each['city_ship5'] != '')
                    $tempContractShipping[] = $each['city_ship5'];
                if ($each['country_bill5'] != 0)
                    $tempContractShipping[] = $tempShip5;
                if ($each['state_ship5'] != '')
                    $tempContractShipping[] = $each['state_ship5'];
                if ($each['zip_ship5'] != '')
                    $tempContractShipping[] = $each['zip_ship5'];
                $contractShipping = implode('<br/>', $tempContractShipping);
            }
        }

        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
        $sql = sprintf(" update  `sales_flat_order` set `override`= '%d',`contract_id`= '%d',`contract_billing`='%s',`contract_shipping`='%s',`approver`='%d',`contractship`='%s' where `entity_id` ='%d'", $contractleveloverridepermission, $contractId, $contractCode, $contractShipping, $firstApp, $customerAddressId, $id);
        $write->query($sql);
        unset($write);
        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
        $sql = sprintf(" update  `sales_flat_order_grid` set `contractbilling_name`='%s',`contractshipping_name`='%s' where `entity_id` ='%d'", $contractBillName, $contractShipName, $id);
        $write->query($sql);
        unset($write);
        //End of Sales Flat Order
		
        if ($BaseGrandTotal > $authorityLimit && $authorityLimit != null) {
            //Rule Checking
            $ruleId = array();
            $rules = Mage::getModel('rule/rule')->getCollection();
            foreach ($rules as $rule) {
                $TempruleId = $rule['rule_id'];
                $read = Mage::getSingleton('core/resource')->getConnection('core_read');
                $sql = "SELECT  `website_ids` FROM `rule` WHERE `rule_id` = " . $TempruleId . " ";
                $rul = $read->fetchAll($sql);
                $temprul = '';
                foreach ($rul as $key) {
                    foreach ($key as $key1 => $value) {
                        if ($key1 = 'website_ids') {
                            $temprul = $value;
                        }
                    }
                }
                unset($read);
                if ($temprul == $websiteId) {
					Mage::log('-------------------------------------------rule id---'.$temprul);
                    if ($rule->getIsActive()) {
                        $orderamt = $rule->getOrderamount();
                        if (is_numeric($orderamt)) {
                            $amount = (float) $orderamt;
                            if ($BaseGrandTotal == $amount) {
                                $ruleId[] = $rule['rule_id'];
                            }
                        } else if (strpos($orderamt, '<=') !== false) {
                            $amount = explode('<=', $orderamt);
                            $min = (int) $amount[0];
                            $max = (int) $amount[1];
                            $gt = '';
                            if ($min == 0) {
                                $gt = $max;
                            } else if ($max == 0) {
                                $gt = $min;
                            }
                            if ($BaseGrandTotal <= $gt) {
                                $ruleId[] = $rule['rule_id'];
                            }
                        } else if (strpos($orderamt, '>=') !== false) {
                            $amount = explode('>=', $orderamt);
                            $min = (int) $amount[0];
                            $max = (int) $amount[1];
                            $gt = '';
                            if ($min == 0) {
                                $gt = $max;
                            } else if ($max == 0) {
                                $gt = $min;
                            }
                            if ($BaseGrandTotal >= $gt) {
                                $ruleId[] = $rule['rule_id'];
                            }
                        } else if (strpos($orderamt, '<') !== false) {
                            $amount = explode('<', $orderamt);
                            $min = (int) $amount[0];
                            $max = (int) $amount[1];
                            $gt = '';
                            if ($min == 0) {
                                $gt = $max;
                            } else if ($max == 0) {
                                $gt = $min;
                            }
                            if ($BaseGrandTotal < $gt) {
                                $ruleId[] = $rule['rule_id'];
                            }
                        } else if (strpos($orderamt, '>') !== false) {
                            $amount = explode('>', $orderamt);
                            $min = (int) $amount[0];
                            $max = (int) $amount[1];
                            $gt = '';
                            if ($min == 0) {
                                $gt = $max;
                            } else if ($max == 0) {
                                $gt = $min;
                            }
                            if ($BaseGrandTotal > $gt) {
                                $ruleId[] = $rule['rule_id'];
                            }
                        } else if (strpos($orderamt, '-') !== false) {
                            $amount = explode('-', $orderamt);
                            $min = (int) $amount[0];
                            $max = (int) $amount[1];
                            if ($BaseGrandTotal >= $min && $BaseGrandTotal <= $max) {
                                $ruleId[] = $rule['rule_id'];
                            }
                        }
                    }
                }
            }

            if (!empty($ruleId)) {
                $rId = $ruleId[0];
                $tempRule = Mage::getModel('rule/rule')->load($rId);
                $le1 = $tempRule->getL1();
                $l1 = $le1 - 1;
                $l2 = $tempRule->getL2();
                $l3 = $tempRule->getL3();
                $l4 = $tempRule->getL4();
                $l5 = $tempRule->getL5();
                $totalApprover = $le1 + $l2 + $l3 + $l4 + $l5;
				
				Mage::log('Id.php------------------------------------order status change to hold-----');
                //order state change
                $tempIncrementId = $order1->getIncrementId();
                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $sql = sprintf("update  `sales_flat_order` set `state` = 'holded'  ,`total_approvers`= '%d', `status` = 'pending_approval' where `increment_id` = '%d'", $totalApprover, $tempIncrementId);
                $write->query($sql);
                unset($write);

                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $sql = sprintf("update `sales_flat_order_grid` set `status` = 'pending_approval' where `increment_id` = '%d'", $tempIncrementId);
                $write->query($sql);
                unset($write);

                //End order state change
                //Mail to the user stating the order status is Pending Approval and mail to approver stating to approve the order

                $approverObj = Mage::getModel('customer/customer')->load($firstApp);

				// $storee = Mage::getModel('core/store')->load($storeid);
				$storee = Mage::app()->getStore($storeid);
				// Mage::log($storee); 
				Mage::app()->setCurrentStore($storee);
                $custId = $order1->getCustomerId();
                $customer1 = Mage::getModel('customer/customer')->load($custId);
                $email = $customer1->getEmail();
                $emailTemplate = Mage::getModel('core/email_template');
                $test = $emailTemplate->loadDefault('sales_email_order_custom1');
                $emailTemplateVariables['username'] = $customer1->getFirstname() . ' ' . $customer1->getLastname();
                $emailTemplateVariables['order_id'] = $order1->getIncrementId();
                $emailTemplateVariables['store_name'] = $order1->getStoreName();
                $emailTemplateVariables['approver'] = $approverObj->getFirstname() . ' ' . $approverObj->getLastname();
                $emailTemplateVariables['store_url'] = $storee->getHomeUrl();
				// $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/'.Mage::getStoreConfig('design/header/logo_src');
				$emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/email/logo/'.Mage::getStoreConfig('design/email/logo', $storeid);
				$emailTemplate->sendTransactional(
                        $test, 'sales', $email, $email, $emailTemplateVariables
                );
				
				Mage::log('First client conformation mail');
				Mage::log('-------------------------------------------mail send 1---');
                unset($emailTemplate);
                $email1 = $approverObj->getEmail();
                $emailTemplate1 = Mage::getModel('core/email_template');
                $test1 = $emailTemplate1->loadDefault('sales_email_order_custom');
                $emailTemplateVariables1['shippinginfo'] = $order1->getShippingDescription();
                $emailTemplateVariables1['shipping'] = $contractShipping;
                $emailTemplateVariables1['field'] = $contractName;
                $emailTemplateVariables1['billing'] = $contractCode;
                $emailTemplateVariables1['payment'] = $order1->getPayment()->getMethodInstance()->getTitle();
                $emailTemplateVariables1['custom'] = $order1->getIsNotVirtual();
                $emailTemplateVariables1['date'] = $date;
                $emailTemplateVariables1['realusername'] = $customer1->getFirstname() . ' ' . $customer1->getLastname();
                $emailTemplateVariables1['username'] = $approverObj->getFirstname() . ' ' . $approverObj->getLastname();
                $emailTemplateVariables1['order_id'] = $order1->getIncrementId();
                $emailTemplateVariables1['order'] = $order1;
				$tempdata = explode('?',$storee->getUrl());
				$emailTemplateVariables1['linkurl'] = $tempdata[0];
				$emailTemplateVariables1['loginurl'] = $tempdata[0].'customer/account/login/';
				
                $emailTemplateVariables1['store_name'] = $storee->getName();
                $emailTemplateVariables1['store_url'] = $storee->getUrl();
				$emailTemplateVariables1['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/email/logo/'.Mage::getStoreConfig('design/email/logo', $storeid);
				
                $emailTemplate1->sendTransactional(
                        $test1, 'sales', $email1, $email1, $emailTemplateVariables1, $storeid
                );
				Mage::log('-------------------------------------------mail send 2---');
                //End of the mail to the user
                // push notification to approver touch device
                Mage::getModel('mobiapp/apn')->newApprovalMessage($approverObj, $order1);
                // end push notification
                //taking out level 1 to 5 approver entity id
                $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cust_category');
                $options1 = array();
                $options2 = array();
                if ($attribute->usesSource()) {
                    foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
                        foreach ($optionValue as $key => $value) {
                            if ($key == 'label')
                                $options1[] = $value;
                            if ($key == 'value')
                                $options2[] = $value;
                        }
                    }
                }
                unset($attribute);
                $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'approval_level');
                $options3 = array();
                $options4 = array();
                if ($attribute->usesSource()) {
                    foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
                        foreach ($optionValue as $key => $value) {
                            if ($key == 'label')
                                $options3[] = $value;
                            if ($key == 'value')
                                $options4[] = $value;
                        }
                    }
                }
                unset($attribute);
                $appId = '';
                for ($i = 0; $i < count($options1); $i++) {
                    if ($options1[$i] == 'Approver')
                        $appId = $options2[$i];
                }
                $applevel1 = '';
                $applevel2 = '';
                $applevel3 = '';
                $applevel4 = '';
                $applevel5 = '';
                for ($i = 0; $i < count($options3); $i++) {
                    if ($options3[$i] == 1) {
                        $applevel1 = $options4[$i];
                    } else if ($options3[$i] == 2) {
                        $applevel2 = $options4[$i];
                    } else if ($options3[$i] == 3) {
                        $applevel3 = $options4[$i];
                    } else if ($options3[$i] == 4) {
                        $applevel4 = $options4[$i];
                    } else if ($options3[$i] == 5) {
                        $applevel5 = $options4[$i];
                    }
                }
                $level1Id = array();
                $level2Id = array();
                $level3Id = array();
                $level4Id = array();
                $level5Id = array();
                if ($l1 != -1) {
                    foreach ($userId as $key => $value) {
                        if ($value != $firstApp) {
                            $collection = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('entity_id', $value)
                                    ->addFieldToFilter('cust_category', $appId)
                                    ->addFieldToFilter('approval_level', $applevel1);
                            foreach ($collection as $customer) {
                                $level1Id[] = $customer->getId();
                            }
                            unset($collection);
                        } else {
                            continue;
                        }
                    }
                    if (!empty($level1Id)) {
                        $level1 = implode(',', $level1Id);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approvers_level1`= '%s' where `entity_id` ='%d'", $level1, $id);
                        $write->query($sql);
                        unset($write);
                    }
                }
                if ($l2 != 0) {
                    foreach ($userId as $key => $value) {
                        $collection = Mage::getModel('customer/customer')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('entity_id', $value)
                                ->addFieldToFilter('cust_category', $appId)
                                ->addFieldToFilter('approval_level', $applevel2);
                        foreach ($collection as $customer) {
                            $level2Id[] = $customer->getId();
                        }
                    }
                    if (!empty($level2Id)) {
                        $level2 = implode(',', $level2Id);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approvers_level2`= '%s' where `entity_id` ='%d'", $level2, $id);
                        $write->query($sql);
                        unset($write);
                    }
                }
                if ($l3 != 0) {
                    foreach ($userId as $key => $value) {
                        $collection = Mage::getModel('customer/customer')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('entity_id', $value)
                                ->addFieldToFilter('cust_category', $appId)
                                ->addFieldToFilter('approval_level', $applevel3);
                        foreach ($collection as $customer) {
                            $level3Id[] = $customer->getId();
                        }
                    }
                    if (!empty($level3Id)) {
                        $level3 = implode(',', $level3Id);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approvers_level3`= '%s' where `entity_id` ='%d'", $level3, $id);
                        $write->query($sql);
                        unset($write);
                    }
                }
                if ($l4 != 0) {
                    foreach ($userId as $key => $value) {
                        $collection = Mage::getModel('customer/customer')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('entity_id', $value)
                                ->addFieldToFilter('cust_category', $appId)
                                ->addFieldToFilter('approval_level', $applevel4);
                        foreach ($collection as $customer) {
                            $level4Id[] = $customer->getId();
                        }
                    }
                    if (!empty($level4Id)) {
                        $level4 = implode(',', $level4Id);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approvers_level4`= '%s' where `entity_id` ='%d'", $level4, $id);
                        $write->query($sql);
                        unset($write);
                    }
                }
                if ($l5 != 0) {
                    foreach ($userId as $key => $value) {
                        $collection = Mage::getModel('customer/customer')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('entity_id', $value)
                                ->addFieldToFilter('cust_category', $appId)
                                ->addFieldToFilter('approval_level', $applevel5);
                        foreach ($collection as $customer) {
                            $level5Id[] = $customer->getId();
                        }
                    }
                    if (!empty($level5Id)) {
                        $level5 = implode(',', $level5Id);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approvers_level5`= '%s' where `entity_id` ='%d'", $level5, $id);
                        $write->query($sql);
                        unset($write);
                    }
                }
                //end level 1 to 5 approver enitity id
                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $sql = sprintf(" update  `sales_flat_order` set `l1`= '%d',`l2`='%d',`l3`='%d',`l4`='%d',`l5`='%d' where `entity_id` ='%d'", $l1, $l2, $l3, $l4, $l5, $id);
                $write->query($sql);
                unset($write);
            }
        } else {
			Mage::log('-----------1--------------------------------before final approved---');
            $split = new Insync_Approve_Model_Supplier();
			$order = Mage::getModel('sales/order')->load($id);
			Mage::log($id);
			Mage::log($order->getData());
			Mage::log('-------------1------------------------------before final approved---');
            $split->Split($id, $order->getData('store_id'), $order->getData('order_currency_code'));
        }
    }

    public function Approver($nextAppId, $id, $customerId) {
		Mage::log('-------------------------------------------approve start---');
        $order = Mage::getModel('sales/order')->load($id);
        $customerDetails = Mage::getModel('customer/customer')->load($customerId);
        $name = $customerDetails->getFirstname() . ' ' . $customerDetails->getLastname();
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT MAX(`entity_id`) FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $id . "";
        $entityid = $read->fetchAll($sql);
        $tempentityid = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {
                $tempentityid = $value;
            }
        }
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT `customer_id`, `comment` FROM  `sales_flat_order_status_history` WHERE  `entity_id` = " . $tempentityid . "";
        $entityid = $read->fetchAll($sql);
        $tempCustId = '';
        $tempComment = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {

                if ($key1 == 'customer_id') {
                    $tempCustId = $value;
                }
                if ($key1 == 'comment') {
                    $tempComment = $value;
                }
            }
        }
        if ($tempComment == '') {
            if ($tempCustId != $customerId) {
                $comment = 'Order Approved by ' . $name;
                $objSapConfig = new Insync_Serverdatetime_Model_Serverdatetime_Api_V2();
                $datetime = $objSapConfig->getServerDateTime();
                $date = '';
                $time = '';
                foreach ($datetime as $each) {
                    foreach ($each as $key => $value) {
                        if ($key == 'server_date') {
                            $date = $value;
                        }
                        if ($key == 'server_time') {
                            $time = $value;
                        }
                    }
                }
                $tempDate = explode('-', $date);
                $finalDate = $tempDate[2] . "-" . $tempDate[0] . "-" . $tempDate[1];
                $newDate = $finalDate . " " . $time;
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $sql = " INSERT INTO `sales_flat_order_status_history` (`entity_id`, `parent_id`, `is_customer_notified`, `is_visible_on_front`, `comment`, `status`, `created_at`, `entity_name`, `customer_id`) VALUES (NULL, '" . $id . "', NULL, '0', '" . $comment . "', NULL, '" . $newDate . "', NULL, '" . $customerId . "') ";
                $write->query($sql);
                unset($write);
            }
        } else {
            if ($tempCustId != $customerId) {
                $comment = 'Order Approved by ' . $name;
                $objSapConfig = new Insync_Serverdatetime_Model_Serverdatetime_Api_V2();
                $datetime = $objSapConfig->getServerDateTime();
                $date = '';
                $time = '';
                foreach ($datetime as $each) {
                    foreach ($each as $key => $value) {
                        if ($key == 'server_date') {
                            $date = $value;
                        }
                        if ($key == 'server_time') {
                            $time = $value;
                        }
                    }
                }
                $tempDate = explode('-', $date);
                $finalDate = $tempDate[2] . "-" . $tempDate[0] . "-" . $tempDate[1];
                $newDate = $finalDate . " " . $time;
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $sql = " INSERT INTO `sales_flat_order_status_history` (`entity_id`, `parent_id`, `is_customer_notified`, `is_visible_on_front`, `comment`, `status`, `created_at`, `entity_name`, `customer_id`) VALUES (NULL, '" . $id . "', NULL, '0', '" . $comment . "', NULL, '" . $newDate . "', NULL, '" . $customerId . "') ";
                $write->query($sql);
                unset($write);
            } else {
                $finalC = explode('|', $tempComment);
                $finalComment = 'Your order is approved by ' . $name . ' | ' . $finalC[1];
                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $sql = sprintf("update `sales_flat_order_status_history` set `comment` = '%s' where `entity_id` = '%d'", $finalComment, $tempentityid);
                $write->query($sql);
                unset($write);
            }
        }

        $contractName = '';
        $tempIncrementId = $order->getIncrementId();
        if ($nextAppId == 0) {
			
			/*
			$approvalTimestamp = date('Y-m-d H:i:s');
            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
            $sql = sprintf("update  `sales_flat_order` set `state` = 'new'  , `status` = 'pending', `approved_at` = '%s' where `increment_id` = '%d'", $approvalTimestamp, $tempIncrementId);
            $write->query($sql);
			*/
			
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tableName = Mage::getSingleton("core/resource")->getTableName('sales_flat_order');
			$now = Varien_Date::now();
			$data = array(
			  'state' => 'new',
			  'status' => 'pending',
			  'approved_at' => $now,
			  'approved_by' => $name,
			  );
			$write->update($tableName, $data, 'increment_id = ' . '"' . $tempIncrementId . '"');
			
			
            unset($write);
            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
            $sql = sprintf("update `sales_flat_order_grid` set `status` = 'pending' where `increment_id` = '%d'", $tempIncrementId);
            $write->query($sql);
            unset($write);
			
            $split = new Insync_Approve_Model_Supplier();
			// $order = Mage::getModel('sales/order')->load($id);
            $split->Split($id, $order->getData('store_id'), $order->getData('order_currency_code'));
			
            // $split = new Insync_Approve_Model_Supplier();
            // $split->Split($id, $storeid, $currencyCode);

			// $storee = Mage::getModel('core/store')->load($order->getData('store_id'));
			// Mage::app()->setCurrentStore($storee);
            // $custId = $order->getCustomerId();
            // $customer1 = Mage::getModel('customer/customer')->load($custId);
            // $custEmail = $customer1->getEmail();
            // $emailTemplate = Mage::getModel('core/email_template');
            // $test = $emailTemplate->loadDefault('sales_email_order_complete');
            // $emailTemplateVariables['username'] = $customer1->getFirstname() . ' ' . $customer1->getLastname();
            // $emailTemplateVariables['order_id'] = $order->getIncrementId();
            // $emailTemplateVariables['store_name'] = $order->getStoreName();
            // $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			// $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/'.Mage::getStoreConfig('design/header/logo_src');
            // $emailTemplate->sendTransactional(
				// $test, 'sales', $custEmail, $custEmail, $emailTemplateVariables, $order->getData('store_id')
            // );
			// Mage::log('store id'.$order->getData('store_id'));
			// Mage::log(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/'.Mage::getStoreConfig('design/header/logo_src'));
			// Mage::log('-------------------------------------------mail send 3---');
        } else {
            $currentAppId = $order->getApprover();
            $custId = $order->getCustomerId();
            $currentCustomerDetails = Mage::getModel('customer/customer')->load($currentAppId);
            $nextCustomerDetails = Mage::getModel('customer/customer')->load($nextAppId);
            $customer1 = Mage::getModel('customer/customer')->load($custId);
            $contractId = $customer1->getContractId();
            $contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
            foreach ($contractDetails as $each) {
                $contractName = $each['name'];
            }

			// $storee = Mage::getModel('core/store')->load($order->getData('store_id'));
			$storee = Mage::app()->getStore($order->getData('store_id'));
			Mage::app()->setCurrentStore($storee);
            $custEmail = $customer1->getEmail();
            $emailTemplate = Mage::getModel('core/email_template');
            $test = $emailTemplate->loadDefault('sales_email_order_notification');
            $emailTemplateVariables['username'] = $customer1->getFirstname() . ' ' . $customer1->getLastname();
            $emailTemplateVariables['approved'] = $currentCustomerDetails->getFirstname() . ' ' . $currentCustomerDetails->getLastname();
            $emailTemplateVariables['approver'] = $nextCustomerDetails->getFirstname() . ' ' . $nextCustomerDetails->getLastname();
            $emailTemplateVariables['order_id'] = $order->getIncrementId();
            // $emailTemplateVariables['store_name'] = $order->getStoreName();
            // $emailTemplateVariables['store_url'] = $storee->getHomeUrl();
			
			$tempdata = explode('?',$storee->getUrl());
			$emailTemplateVariables['linkurl'] = $tempdata[0];
			$emailTemplateVariables['loginurl'] = $tempdata[0].'customer/account/login/';
			$emailTemplateVariables['store_name'] = $storee->getName();
			$emailTemplateVariables['store_url'] = $storee->getUrl();
			
			$emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/email/logo/'.Mage::getStoreConfig('design/email/logo', $order->getData('store_id'));
            $emailTemplate->sendTransactional(
                    $test, 'sales', $custEmail, $custEmail, $emailTemplateVariables, $order->getData('store_id')
            );
			Mage::log('-------------------------------------------mail send 4---');

            $email1 = $nextCustomerDetails->getEmail();
            $emailTemplate1 = Mage::getModel('core/email_template');
            $test1 = $emailTemplate1->loadDefault('sales_email_order_custom');
            $emailTemplateVariables1['shippinginfo'] = $order->getShippingDescription();
            $emailTemplateVariables1['shipping'] = $order->getContractShipping();
            $emailTemplateVariables1['billing'] = $order->getContractBilling();
            $emailTemplateVariables1['payment'] = $order->getPayment()->getMethodInstance()->getTitle();
            $emailTemplateVariables1['custom'] = $order->getIsNotVirtual();
            $emailTemplateVariables1['date'] = $date;
            $emailTemplateVariables1['field'] = $contractName;
            $emailTemplateVariables1['realusername'] = $customer1->getFirstname() . ' ' . $customer1->getLastname();
            $emailTemplateVariables1['username'] = $nextCustomerDetails->getFirstname() . ' ' . $nextCustomerDetails->getLastname();
            $emailTemplateVariables1['order_id'] = $order->getIncrementId();
            $emailTemplateVariables1['order'] = $order;
            $emailTemplateVariables1['store_name'] = $order->getStoreName();
            $emailTemplateVariables1['store_url'] = $storee->getHomeUrl();
			$emailTemplateVariables1['loginurl'] = $tempdata[0] . 'customer/account/login/';
			$emailTemplateVariables1['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/email/logo/'.Mage::getStoreConfig('design/email/logo', $order->getData('store_id'));
            $emailTemplate1->sendTransactional(
                    $test1, 'sales', $email1, $email1, $emailTemplateVariables1, $order->getData('store_id')
            );
			Mage::log('-------------------------------------------mail send 5---');
            // push notification
            Mage::getModel('mobiapp/apn')->newApprovalMessage($nextCustomerDetails, $order);
            // end push
            $l1 = $order->getL1();
            $l2 = $order->getL2();
            $l3 = $order->getL3();
            $l4 = $order->getL4();
            $l5 = $order->getL5();

            $currentCustomerLevel = $currentCustomerDetails->getData('approval_level');
            $nextCustomerLevel = $nextCustomerDetails->getData('approval_level');
            unset($currentCustomerDetails);
            unset($nextCustomerDetails);
            $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'approval_level');
            $options3 = array();
            $options4 = array();
            if ($attribute->usesSource()) {
                foreach ($attribute->getSource()->getAllOptions() as $optionValue) { {
                        foreach ($optionValue as $key => $value) {
                            if ($key == 'label')
                                $options3[] = $value;
                            if ($key == 'value')
                                $options4[] = $value;
                        }
                    }
                }
            }

            $applevel1 = '';
            $applevel2 = '';
            $applevel3 = '';
            $applevel4 = '';
            $applevel5 = '';
            for ($i = 0; $i < count($options3); $i++) {
                if ($options3[$i] == 1) {
                    $applevel1 = $options4[$i];
                } else if ($options3[$i] == 2) {
                    $applevel2 = $options4[$i];
                } else if ($options3[$i] == 3) {
                    $applevel3 = $options4[$i];
                } else if ($options3[$i] == 4) {
                    $applevel4 = $options4[$i];
                } else if ($options3[$i] == 5) {
                    $applevel5 = $options4[$i];
                }
            }
            unset($attribute);
            if ($currentCustomerLevel == $nextCustomerLevel) {
                if ($applevel1 == $nextCustomerLevel) {
                    if ($l1 != 0) {
                        $l1 = $l1 - 1;
                    } else if ($l1 == 0) {
                        
                    }

                    $level1appr = $order->getApproversLevel1();
                    $tempLevel = explode(',', $level1appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount == 0) {
                        
                    } else {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d', `l1`= '%d' , `approvers_level1`= '%s' where `entity_id` ='%d'", $currentAppId, $nextAppId, $l1, $finalApprovers, $id);
                        $write->query($sql);
                        unset($write);
                    }
                } else if ($applevel2 == $nextCustomerLevel) {
                    if ($l2 != 0) {
                        $l2 = $l2 - 1;
                    } else if ($l2 == 0) {
                        
                    }
                    $level2appr = $order->getApproversLevel2();
                    $tempLevel = explode(',', $level2appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount == 0) {
                        
                    } else {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d', `l2`= '%d' ,`approvers_level2`= '%s' where `entity_id` ='%d'", $currentAppId, $nextAppId, $l2, $finalApprovers, $id);
                        $write->query($sql);
                        unset($write);
                    }
                } else if ($applevel3 == $nextCustomerLevel) {
                    if ($l3 != 0) {
                        $l3 = $l3 - 1;
                    } else if ($l3 == 0) {
                        
                    }
                    $level3appr = $order->getApproversLevel3();
                    $tempLevel = explode(',', $level3appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount != 0) {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d', `l3`= '%d' ,`approvers_level3`= '%s' where `entity_id` ='%d'", $currentAppId, $nextAppId, $l3, $finalApprovers, $id);
                        $write->query($sql);
                        unset($write);
                    }
                } else if ($applevel4 == $nextCustomerLevel) {
                    if ($l4 != 0) {
                        $l4 = $l4 - 1;
                    } else if ($l4 == 0) {
                        
                    }
                    $level4appr = $order->getApproversLevel4();
                    $tempLevel = explode(',', $level4appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount != 0) {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d', `l4`= '%d' ,`approvers_level4`= '%s' where `entity_id` ='%d'", $currentAppId, $nextAppId, $l4, $finalApprovers, $id);
                        $write->query($sql);
                        unset($write);
                    }
                } else if ($applevel5 == $nextCustomerLevel) {
                    if ($l5 != 0) {
                        $l5 = $l5 - 1;
                    } else if ($l5 == 0) {
                        
                    }
                    $level5appr = $order->getApproversLevel5();
                    $tempLevel = explode(',', $level5appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount != 0) {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d', `l5`= '%d' ,`approvers_level5`= '%s' where `entity_id` ='%d'", $currentAppId, $nextAppId, $l5, $finalApprovers, $id);
                        $write->query($sql);
                        unset($write);
                    }
                }
            } else {
                if ($applevel2 == $nextCustomerLevel) {
                    $le1 = $l1 - 1;
                    $le2 = $l2 - 1;
                    $level2appr = $order->getApproversLevel2();
                    $tempLevel = explode(',', $level2appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount != 0) {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d',`approvers_level2`= '%s', `l1`= '%d', `l2`= '%d'  where `entity_id` ='%d'", $currentAppId, $nextAppId, $finalApprovers, $le1, $le2, $id);
                        $write->query($sql);
                        unset($write);
                    }
                } else if ($applevel3 == $nextCustomerLevel) {
                    $le2 = $l2 - 1;
                    $le3 = $l3 - 1;
                    $level3appr = $order->getApproversLevel3();
                    $tempLevel = explode(',', $level3appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount != 0) {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d',`approvers_level3`= '%s', `l2`= '%d', `l3`= '%d' where `entity_id` ='%d'", $currentAppId, $nextAppId, $finalApprovers, $le2, $le3, $id);
                        $write->query($sql);
                        unset($write);
                    }
                } else if ($applevel4 == $nextCustomerLevel) {
                    $le3 = $l3 - 1;
                    $le4 = $l4 - 1;
                    $level4appr = $order->getApproversLevel4();
                    $tempLevel = explode(',', $level4appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount != 0) {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d',`approvers_level4`= '%s', `l3`= '%d', `l4`= '%d' where `entity_id` ='%d'", $currentAppId, $nextAppId, $finalApprovers, $le3, $le4, $id);
                        $write->query($sql);
                        unset($write);
                    }
                } else if ($applevel5 == $nextCustomerLevel) {
                    $le4 = $l4 - 1;
                    $le5 = $l5 - 1;
                    $level5appr = $order->getApproversLevel5();
                    $tempLevel = explode(',', $level5appr);
                    $approversCount = count($tempLevel);
                    if ($approversCount != 0) {
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            if ($tempLevel[$i] == $nextAppId) {
                                continue;
                            } else {
                                $tempApprovers[] = $tempLevel[$i];
                            }
                        }
                        $finalApprovers = implode(',', $tempApprovers);
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order` set `approved` = '%d', `approver` = '%d',`approvers_level5`= '%s', `l4`= '%d', `l5`= '%d' where `entity_id` ='%d'", $currentAppId, $nextAppId, $finalApprovers, $le4, $le5, $id);
                        $write->query($sql);
                        unset($write);
                    }
                }
            }
        }
    }

    public function Reject($customerId, $orderId) {
        $customerDetails = Mage::getModel('customer/customer')->load($customerId);
        $name = $customerDetails->getFirstname() . ' ' . $customerDetails->getLastname();
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT MAX(`entity_id`) FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $orderId . "";
        $entityid = $read->fetchAll($sql);
        $tempentityid = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {

                $tempentityid = $value;
            }
        }
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT `comment` FROM  `sales_flat_order_status_history` WHERE  `entity_id` = " . $tempentityid . "";
        $entityid = $read->fetchAll($sql);
        $tempComment = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {
                if ($key1 == 'comment') {
                    $tempComment = $value;
                }
            }
        }
        $Fcomment = explode('|', $tempComment);
        $commentF = $Fcomment[1];
        $finalComment = "@Your order is rejected by " . $name . " | " . $Fcomment[1] . "@";
        $ultimateComment = str_replace('@', '"', $finalComment);

        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
        // $sql = sprintf("update `sales_flat_order_status_history` set `comment` = '%s' where `entity_id` = '%d'", $finalComment, $tempentityid);
        $sql = "update `sales_flat_order_status_history` set `comment` = " . $ultimateComment . " where `entity_id` = " . $tempentityid;
        $write->query($sql);
        unset($write);
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT MAX(  `entity_id` ) as 'EntityId' FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $orderId . "";
        $entityid = $read->fetchAll($sql);
        $tempentityid = '';
        $tempcustomerid = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {
                if ($key1 == 'EntityId') {
                    $tempentityid = $value;
                }
            }
        }

        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT `customer_id` FROM  `sales_flat_order_status_history` WHERE  `entity_id` =" . $tempentityid . "";
        $customer = $read->fetchAll($sql);
        $tempcustomerid = '';
        foreach ($customer as $key) {
            foreach ($key as $key1 => $value) {

                $tempcustomerid = $value;
            }
        }

        $order = Mage::getModel('sales/order')->load($orderId);
        $tempIncrementId = $order->getIncrementId();

        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
        $sql = sprintf("update  `sales_flat_order` set `state` = 'canceled'  , `status` = 'canceled' where `increment_id` = '%d'", $tempIncrementId);
        $write->query($sql);
        unset($write);
        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
        $sql = sprintf("update `sales_flat_order_grid` set `status` = 'canceled' where `increment_id` = '%d'", $tempIncrementId);
        $write->query($sql);
        unset($write);
        $this->mailTemplate($orderId, $tempcustomerid, $customerId, $tempentityid);
    }

    public function mailTemplate($id, $tempcustomerid, $customerId, $tempentityid) {

        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT `comment` FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $id . " and `entity_id` =" . $tempentityid . "";
        $comment = $read->fetchAll($sql);
        $tempcomment = '';
        foreach ($comment as $key) {
            foreach ($key as $key1 => $value) {

                $tempcomment = $value;
            }
        }
        $order = Mage::getModel('sales/order')->load($id);
        $custId = $order->getCustomerId();
        $customer1 = Mage::getModel('customer/customer')->load($custId);
        $custEmail = $customer1->getEmail();
        $email = $custEmail;
        $emailTemplate = Mage::getModel('core/email_template');
        $test = $emailTemplate->loadDefault('sales_email_order_cancel');
        if ($tempcustomerid == $customerId) {
            if ($tempcomment != '') {
                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $sql = sprintf("update `sales_flat_order_status_history` set `status` = 'canceled' where `entity_id` = '%d'", $tempentityid);
                $write->query($sql);
                unset($write);
            }

            $emailTemplateVariables['comment'] = $tempcomment;
        } else {
            $emailTemplateVariables['comment'] = ' ';
        }

        $emailTemplateVariables['username'] = $customer1->getFirstname() . ' ' . $customer1->getLastname();
        $emailTemplateVariables['order_id'] = $order->getIncrementId();
        $emailTemplateVariables['store_name'] = $order->getStoreName();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailTemplate->sendTransactional(
                $test, 'sales', $email, $email, $emailTemplateVariables
        );
		Mage::log('-------------------------------------------mail send 6---');
    }

}
