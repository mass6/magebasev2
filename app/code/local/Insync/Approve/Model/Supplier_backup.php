<?php

class Insync_Approve_Model_Supplier extends Mage_Sales_Model_Order {

    public function Split($id, $storeid = null, $currencyCode = null) {
        Mage::log('-------------------------------------------store id in Split---');
		// one mail from \app\code\core\Mage\Sales\Model\Order.php
        Mage::log($storeid);
        $supplierDetails = Mage::getModel('supplier/supplier')->getCollection();
        $suplierId = array();

        foreach ($supplierDetails as $each) {
            $suplierId[$each['name']] = $each['id'] . '#' . $each['sup_email'] . '#' . $each['sup_tel'] . '#' . $each['sup_name'];
            if ($each['name'] == '36s') {
                $supId = $each['id'];
            }
        }
        // $productSupplier = array();
        $supplier = null;
        $productSupplier1 = array();
        $productSupplier2 = array();
        $collection = Mage::getModel("sales/order")->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $id);
        $j = 1;
        $i = 1;
        $s = 2;
        foreach ($collection as $order) {
            foreach ($order->getAllItems() as $item) {
                $item->toArray();
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item['sku']);
                $productsupplier = $product->getSupplier();
                if ($i == 1) {
                    $supplier = $product->getSupplier();
                } else {
                    if ($testSupplier != $productsupplier) {
                        $j++;
                    }
                }
                $i++;
                unset($product);
            }
        }
        if ($j == 1) {
            if ($testSupplier == $supId) {
                $result = '36s';
            } else {
                $result = $supplier;
            }
        } else {
            $result = 'multi';
        }

        if ($result == 'multi') {
            foreach ($collection as $order) {
                $emailSupplier = '';
                $mainOrderId = $order['entity_id'];
                $contractId = $order['contract_id'];
                $customerId = $order['customer_id'];
                $storeId = $order['store_id'];
                $incrementId = $order['increment_id'];
                $contractShip = $order['contract_shipping'];
                $contractBill = $order['contract_billing'];
                Mage::helper('insync_approve')->sendEmail($customerId, $order, 'sales_email_order_multi', $storeid, array());

                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $sql = sprintf(" update  `sales_flat_order` set `SapSync`= 2,`supplier`= 1 where `entity_id` ='%d'", $mainOrderId);
                $write->query($sql);
                //unset($write,$custEmail,$customer1,$emailTemplate,$emailTemplateVariables);

                $read = Mage::getSingleton('core/resource')->getConnection('core_read');
                $sql = "SELECT  `comment` FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $mainOrderId . " AND  `customer_id` =0";
                $tempComment = $read->fetchAll($sql);
                $comment = '';
                foreach ($tempComment as $key) {
                    foreach ($key as $key1 => $value) {
                        $comment = $value;
                    }
                }
                unset($read);
                foreach ($suplierId as $key => $value) {
                    Mage::log('-------------------------------------------supplier id---');
                    $t = 0;
                    $temp = explode('#', $value);
                    $telephone = $temp[2];
                    $supplierName = $temp[3];
                    $test = array();
                    $productSupplierId = '';
                    foreach ($order->getAllItems() as $item) {
                        $item->toArray();
                        $itemA['sku'] = $item['sku'];
                        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item['sku']);
                        if ($temp[0] == $product->getSupplier()) {
                            $t = 1;
                            $productSupplierId = $product->getSupplier();
                            $test[] = $temp[1] . '||' . $key . '||' . $product->getSupplier() . '||' . $product->getSku() . '||' . $item['qty_ordered'] . '||' . $item['price_incl_tax'];
                        }
                    }
                    if ($t == 1) {
                        $data = array();
                        $data1 = array();
                        foreach ($test as $key => $value) {

                            $tempPro = explode('||', $value);
                            $data['sku'] = $tempPro[3];
                            $data['qty'] = $tempPro[4];
                            $data['price'] = $tempPro[5];
                            $data1[] = $data;
                        }
                        unset($test);
                        $Shipname = explode('<br/>', $contractShip);
                        $contractShipName = $Shipname[0];
                        $Billname = explode('<br/>', $contractBill);
                        $contractBillName = $Billname[0];
                        $sales = new Insync_Sales_Model_Order_Api_V2();
                        $createO = array();
                        $createO = $sales->create($customerId, 'freeshipping_freeshipping', null, $data1, false, $storeid, $contractBill, $contractShip, $currencyCode);
                        $newIncrement = $incrementId . '-' . $tempPro[1];
                        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                        $sql = sprintf(" update  `sales_flat_order_grid` set `increment_id`='%s',`contractbilling_name`='%s',`contractshipping_name`='%s' where `entity_id` ='%d'", $newIncrement, $contractBillName, $contractShipName, $createO['orderid']);
                        $write->query($sql);
                        unset($write);
                        if ($supId == $productSupplierId) {
                            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                            $sql = sprintf(" update  `sales_flat_order` set `increment_id`='%s',`contract_id`='%d',`view`= 1,`SapSync`= 0 where `entity_id` ='%d'", $newIncrement, $contractId, $createO['orderid']);
                            $write->query($sql);
                            unset($write);
                            $order1 = Mage::getModel('sales/order')->load($createO['orderid']);
                            $customer = Mage::getModel('customer/customer')->load($customerId);
                            // $storee = Mage::getModel('core/store')->load($storeId);
							$storee = Mage::app()->getStore($storeId);
                            Mage::app()->setCurrentStore($storee);
                            $custEmail = $customer->getEmail();
                            $emailTemplate1 = Mage::getModel('core/email_template');
                            $templateObj = $emailTemplate1->loadDefault('sales_email_order_36');
                            $emailTemplateVariables = array();
                            $emailTemplateVariables['username'] = $customer->getFirstname() . ' ' . $customer->getLastname();
                            $emailTemplateVariables['order_id'] = $order1->getIncrementId();
                            $emailTemplateVariables['order'] = $order1;
                            $emailTemplateVariables['store_name'] = $storee->getName();
                            $emailTemplateVariables['store_url'] = $storee->getHomeUrl();

                            $tempdata = explode('?', $storee->getUrl());
                            $emailTemplateVariables['linkurl'] = $tempdata[0];
                            $emailTemplateVariables['loginurl'] = $tempdata[0] . 'customer/account/login/';

                            $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/email/logo/' . Mage::getStoreConfig('design/email/logo', $storeId);
                            $emailTemplate1->sendTransactional(
                                    $templateObj, 'sales', $custEmail, $custEmail, $emailTemplateVariables, $storeId
                            );
                            unset($order1, $customer, $emailTemplate1, $emailTemplateVariables, $templateObj);
                        } else {
                            $emailSupplier = $temp[1];
                            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                            $sql = sprintf(" update  `sales_flat_order` set `increment_id`='%s',`contract_id`='%d',`view`= 1,`supplier`= 1,`SapSync`='%d' where `entity_id` ='%d'", $newIncrement, $contractId, $s, $createO['orderid']);
                            $write->query($sql);
                            unset($write);
                            $order1 = Mage::getModel('sales/order')->load($createO['orderid']);
                            $custoId = $order1->getCustomerId();
                            $Tempdate = $order1->getCreatedAt();
                            $date = explode(' ', $Tempdate);
                            $contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
                            foreach ($contractDetails as $each) {
                                $contractName = $each['name'];
                            }
                            unset($contractDetails);
                            $customer = Mage::getModel('customer/customer')->load($customerId);
                            $custGroup = $customer->getGroupId();
                            $custEmail = $customer->getEmail();
                            $group = Mage::getModel('customer/group')->load($custGroup);
                            $email = $group->getEmail();
                            unset($group);

                            // $storee = Mage::getModel('core/store')->load($storeid);
							$storee = Mage::app()->getStore($storeid);
                            Mage::app()->setCurrentStore($storee);
                            $emailTemplate1 = Mage::getModel('core/email_template');
                            $emailTemplateVariables = array();
                            $templateObj = $emailTemplate1->loadDefault('sales_email_order_supplier');
                            $emailTemplateVariables['shippinginfo'] = $order1->getShippingDescription();
                            $emailTemplateVariables['shipping'] = $order1->getContractShipping();
                            $emailTemplateVariables['billing'] = $order1->getContractBilling();
                            $emailTemplateVariables['payment'] = $order1->getPayment()->getMethodInstance()->getTitle();
                            $emailTemplateVariables['custom'] = $order1->getIsNotVirtual();
                            $emailTemplateVariables['date'] = $date[0];
                            $emailTemplateVariables['field'] = $contractName;
                            $emailTemplateVariables['realusername'] = $customer->getFirstname() . ' ' . $customer->getLastname();
                            
							// it will be the supplier name
							// $emailTemplateVariables['username'] = $customer->getFirstname() . ' ' . $customer->getLastname();
							$emailTemplateVariables['username'] = $supplierName;

                            $emailTemplateVariables['forwardemail'] = $email . '?cc==' . $order1->getCustomerEmail();
                            $emailTemplateVariables['mailname'] = $email;
                            $emailTemplateVariables['order_id'] = $order1->getIncrementId();
                            $emailTemplateVariables['order'] = $order1;
                            $emailTemplateVariables['comment'] = $comment;
                            $emailTemplateVariables['store_name'] = $storee->getName();
                            $emailTemplateVariables['store_url'] = $storee->getHomeUrl();
                            $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/email/logo/' . Mage::getStoreConfig('design/email/logo', $storeid);
							// $emailTemplate1->setReplyTo($email);
							$emailTemplate1->setReplyTo($order1->getCustomerEmail());
							Mage::log('-------------------------------------------mail done 2---');
							
							// http://stackoverflow.com/questions/12725307/how-to-send-email-to-multiple-users-in-magento
							$sender = Array(
								'name' => $order1->getCustomerEmail(),
								'email' => $order1->getCustomerEmail()
							);
							$recipients = array(
								$emailSupplier => $emailSupplier,
								$email => $email /*,
								$order1->getCustomerEmail() => $order1->getCustomerEmail()
								 */ // said by jyoti
							);
							
							Mage::log($recipients);
							Mage::log('-------------------------------------------mail start here---');
                            // $emailTemplate1->sendTransactional(
                                    // $templateObj, 'sales', $emailSupplier, $emailSupplier, $emailTemplateVariables, $storeid
                            // );
                            $emailTemplate1->sendTransactional(
                                    $templateObj, 'sales', array_keys($recipients),array_values($recipients), $emailTemplateVariables, $storeid
                            );
							// $emailTemplate1->sendTransactional(
                                    // $templateObj, $sender, array_keys($recipients),array_values($recipients), $emailTemplateVariables, $storeid
                            // );
                            unset($emailTemplate1, $templateObj, $emailTemplateVariables);
							Mage::log('-------------------------------------------mail end here---');

                            // $storee1 = Mage::getModel('core/store')->load($storeid);
							$storee1 = Mage::app()->getStore($storeid);
                            // Mage::app()->setCurrentStore($storee1);
                            $emailTemplate1 = Mage::getModel('core/email_template');
                            $templateObj = $emailTemplate1->loadDefault('sales_email_order_Itemsupplier');
                            $emailTemplateVariables = array();
                            $emailTemplateVariables['realusername'] = $customer->getFirstname() . ' ' . $customer->getLastname();
                            $emailTemplateVariables['username'] = $customer->getFirstname() . ' ' . $customer->getLastname();
                            $emailTemplateVariables['order_id'] = $order1->getIncrementId();
                            $emailTemplateVariables['order'] = $order1;
                            $emailTemplateVariables['supEmail'] = $emailSupplier;
                            $emailTemplateVariables['supplier'] = $supplierName;
                            $emailTemplateVariables['telephone'] = $telephone;
                            $emailTemplateVariables['store_name'] = $storee1->getName();
                            $emailTemplateVariables['store_url'] = $storee1->getUrl();
                            $tempdata = explode('?', $storee1->getUrl());
                            $emailTemplateVariables['linkurl'] = $tempdata[0];
                            $emailTemplateVariables['loginurl'] = $tempdata[0] . 'customer/account/login/';
                            Mage::log('-------------------------------------------url---');
                            Mage::log($emailTemplateVariables['linkurl']);
                            Mage::log($storee1->getHomeUrl());
                            Mage::log('-------------------------------------------url---');

                            $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/email/logo/' . Mage::getStoreConfig('design/email/logo', $storeid);
                            $emailTemplate1->sendTransactional(
                                    $templateObj, 'sales', $custEmail, $custEmail, $emailTemplateVariables, $storeid
                            );
                            Mage::log('-------------------------------------------3---');
                            unset($templateObj, $emailTemplate1, $customer, $emailTemplateVariables, $order1);
                        }
                    }
                }
            }
        } else if ($result == '36s') {
            // $storee = Mage::getModel('core/store')->load($storeid);
			$storee = Mage::app()->getStore($storeid);
            Mage::app()->setCurrentStore($storee);

            $order = Mage::getModel('sales/order')->load($id);
            $custId = $order->getCustomerId();
            $customer1 = Mage::getModel('customer/customer')->load($custId);
            $custEmail = $customer1->getEmail();
            $emailTemplate = Mage::getModel('core/email_template');
            $templateObj = $emailTemplate->loadDefault('sales_email_order_complete');
            $emailTemplateVariables['username'] = $customer1->getFirstname() . ' ' . $customer1->getLastname();
            $emailTemplateVariables['order_id'] = $order->getIncrementId();
            $emailTemplateVariables['store_name'] = $order->getStoreName();
            $emailTemplateVariables['store_url'] = $storee->getHomeUrl();
            $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/default/default/' . Mage::getStoreConfig('design/header/logo_src');
            $emailTemplate->sendTransactional(
                    $templateObj, 'sales', $custEmail, $custEmail, $emailTemplateVariables, $storeid
            );
            Mage::log('-------------------------------------------mail send for 36s---');
            unset($order, $customer1, $emailTemplate, $templateObj);
        } else {
            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $sql = "SELECT  `comment` FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $id . " AND  `customer_id` =0";
            $tempComment = $read->fetchAll($sql);
            $comment = '';
            foreach ($tempComment as $key) {
                foreach ($key as $key1 => $value) {

                    $comment = $value;
                }
            }
            unset($read);
            $supplierDetails1 = Mage::getModel('supplier/supplier')->getCollection();
            $supplierName = '';
            $emailSupplier = '';
            $contractName = '';
            foreach ($supplierDetails as $each) {
                if ($each['id'] == $supplier) {
                    $supplierName = $each['sup_name'];
                    $emailSupplier = $each['sup_email'];
                    $telephone = $each['sup_tel'];
                }
            }
            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
            $sql = sprintf(" update  `sales_flat_order` set `SapSync`='%d',`supplier`= 1 where `entity_id` ='%d'", $s, $id);
            $write->query($sql);
            unset($write);
            $order = Mage::getModel('sales/order')->load($id);
            $custId = $order->getCustomerId();
            $Tempdate = $order->getCreatedAt();
            $date = explode(' ', $Tempdate);
            $contractId = $order->getContractId();
            $contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
            foreach ($contractDetails as $each) {
                $contractName = $each['name'];
            }
            unset($contractDetails);
			
            $customer = Mage::getModel('customer/customer')->load($custId);
            $custGroup = $customer->getGroupId();
            $custEmail = $customer->getEmail();
            $group = Mage::getModel('customer/group')->load($custGroup);
            $email = $group->getEmail();
            // $storee = Mage::getModel('core/store')->load($storeid);
			$storee = Mage::app()->getStore($storeid);
            Mage::app()->setCurrentStore($storee);
            $emailTemplate1 = Mage::getModel('core/email_template');
            $templateObj = $emailTemplate1->loadDefault('sales_email_order_supplier');
            $emailTemplateVariables = array();
            $emailTemplateVariables['shippinginfo'] = $order->getShippingDescription();
            $emailTemplateVariables['shipping'] = $order->getContractShipping();
            $emailTemplateVariables['billing'] = $order->getContractBilling();
            $emailTemplateVariables['custom'] = $order->getIsNotVirtual();
            $emailTemplateVariables['date'] = $date[0];
            $emailTemplateVariables['field'] = $contractName;
            $emailTemplateVariables['realusername'] = $customer->getFirstname() . ' ' . $customer->getLastname();
            $emailTemplateVariables['mailname'] = $email;
            $emailTemplateVariables['forwardemail'] = $email . '?cc==' . $order->getCustomerEmail();

            $emailTemplateVariables['comment'] = $comment;
            $emailTemplateVariables['order_id'] = $order->getIncrementId();
            $emailTemplateVariables['order'] = $order;
            $emailTemplateVariables['store_name'] = $storee->getName();
            $emailTemplateVariables['store_url'] = $storee->getHomeUrl();
            $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/email/logo/' . Mage::getStoreConfig('design/email/logo', $storeid);
			$emailTemplate1->setReplyTo($order->getCustomerEmail());
			Mage::log('-------------------------------------------mail done 1---');
			
			// $emailInfo = Mage::getModel('core/email_info');
			// $emailInfo->addBcc($order->getCustomerEmail(), $order->getCustomerEmail());
			// $emailTemplate1->addEmailInfo($emailInfo);
			
			$recipients = array(
				$emailSupplier => $emailSupplier,
				$email => $email /*,
				$order1->getCustomerEmail() => $order1->getCustomerEmail()
				 */ // said by jyoti
			);
            // $emailTemplate1->sendTransactional(
                    // $templateObj, 'sales', $emailSupplier , $emailSupplier, $emailTemplateVariables, $storeid
            // );
			$emailTemplate1->sendTransactional(
                    $templateObj, 'sales', array_keys($recipients),array_values($recipients), $emailTemplateVariables, $storeid
            );
            Mage::log('-------------------------------------------4---');
            // unset($order);
            unset($emailTemplate1, $templateObj, $emailTemplateVariables);

            $emailTemplate = Mage::getModel('core/email_template');
            $templateObj = $emailTemplate->loadDefault('sales_email_order_Thirdsupplier');
            $emailTemplateVariables['username'] = $customer->getFirstname() . ' ' . $customer->getLastname();
            $emailTemplateVariables['order_id'] = $order->getIncrementId();
            $emailTemplateVariables['supplier'] = $supplierName;
            $emailTemplateVariables['supEmail'] = $emailSupplier;
            $emailTemplateVariables['telephone'] = $telephone;
            $emailTemplateVariables['store_name'] = $order->getStoreName();
            $emailTemplateVariables['store_url'] = $storee->getHomeUrl();
            $emailTemplateVariables['logo_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/email/logo/' . Mage::getStoreConfig('design/email/logo', $storeid);
            $emailTemplate->sendTransactional(
                    $templateObj, 'sales', $custEmail, $custEmail, $emailTemplateVariables
            );
            unset($customer, $emailTemplate, $templateObj, $emailTemplateVariables);
        }
        unset($collection);
    }

}
