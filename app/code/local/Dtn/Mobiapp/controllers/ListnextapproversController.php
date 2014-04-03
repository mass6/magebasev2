<?php

class Dtn_Mobiapp_ListnextapproversController extends Mage_Core_Controller_Front_Action {
    
    public function testAction() {
        echo Mage::app()->getStore()->getId();die;
    }

    public function indexAction() {
        $session = Mage::getSingleton('customer/session');
        if (Mage::helper('customer')->isLoggedIn()) {
            $approver = $session->getCustomer();
        }
        $result = array();
        if (isset($approver)) {
            $orderId = $this->getRequest()->getParam('orderid');
            $_order = Mage::getModel('sales/order')->load($orderId);
            if (!$_order->getId()) {
                $result['result_code'] = 0;
                $result['message'] = 'Order not found';
            } else {
                $helper = Mage::helper('mobiapp/attribute');
                $check = 1;
                $x = 1;
                $l1 = $_order->getL1();
                $l2 = $_order->getL2();
                $l3 = $_order->getL3();
                $l4 = $_order->getL4();
                $l5 = $_order->getL5();
                // get order contract approvers list
                $contractId = $_order->getContractId();
                $contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
                $userId = array();
                foreach ($contractDetails as $each) {
                    for ($iterator = 1; $iterator <= 20; $iterator++) {
                        if ($each['user' . $iterator] != 0)
                            $userId[] = $each['user' . $iterator];
                    }
                }
                // get id of approver customer category
                $appId = $helper->getCustomerApproverCategoryId();
                // logged in customer approval level
                $level = $approver->getApprovalLevel();
                // get all approval_level attribute values
                list($applevel1, $applevel2, $applevel3, $applevel4, $applevel5) = $helper->getApprovalLevelValues();
                // get active_approver attribute value
                $appA = $helper->getActiveApproverAttributeValue();
                
                $nextApprovers = array();
                $optionalApprovers = array();
                
                if ($applevel1 == $level) {
                    if ($l1 == 0) {
                        if ($l2 != 0) {
                            foreach ($userId as $key => $value) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $value)
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel2);
                                foreach ($collection as $customer) {
                                    $nextApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            $level1appr = $_order->getApproversLevel1();
                            $tempLevel = explode(',', $level1appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel1);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            if ($l2 != 0 && count($nextApprovers) == 0) {
                                $result['result_code'] = 0;
                                $result['message'] = 'Cannot proceed with approval process: Additional approvers are required, but are either inactive or not configured.';
                                $x = 0;
                            }
                        } else if ($l2 == 0) {
                            $check = 0;
                            $level1appr = $_order->getApproversLevel1();
                            $tempLevel = explode(',', $level1appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel1);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        } else {
                            $level1appr = $_order->getApproversLevel1();
                            $tempLevel = explode(',', $level1appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel1);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        }
                    } else {
                        $level1appr = $_order->getApproversLevel1();
                        $tempLevel = explode(',', $level1appr);
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            $collection = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('entity_id', $tempLevel[$i])
                                    ->addFieldToFilter('cust_category', $appId)
                                    ->addFieldToFilter('active_approver', $appA)
                                    ->addFieldToFilter('approval_level', $applevel1);
                            foreach ($collection as $customer) {
                                $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                            }
                        }
                        if ($l1 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                        if ($l2 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                    }
                } else if ($applevel2 == $level) {
                    if ($l2 == 0) {
                        if ($l3 != 0) {
                            foreach ($userId as $key => $value) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $value)
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel3);
                                foreach ($collection as $customer) {
                                    $nextApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            $level2appr = $_order->getApproversLevel2();
                            $tempLevel = explode(',', $level2appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel2);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            if ($l3 != 0 && count($nextApprovers) == 0) {
                                $result['result_code'] = 0;
                                $result['message'] = 'Cannot proceed with approval process: Additional approvers are required, but are either inactive or not configured.';
                                $x = 0;
                            }
                        } else if ($l3 == 0) {
                            $check = 0;
                            $level2appr = $_order->getApproversLevel2();
                            $tempLevel = explode(',', $level2appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel2);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        } else {
                            $level2appr = $_order->getApproversLevel2();
                            $tempLevel = explode(',', $level2appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel2);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        }
                    } else {
                        $level2appr = $_order->getApproversLevel2();
                        $tempLevel = explode(',', $level2appr);
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            $collection = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('entity_id', $tempLevel[$i])
                                    ->addFieldToFilter('cust_category', $appId)
                                    ->addFieldToFilter('active_approver', $appA)
                                    ->addFieldToFilter('approval_level', $applevel2);
                            foreach ($collection as $customer) {
                                $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                            }
                        }
                        if ($l2 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                        if ($l3 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                    }
                } else if ($applevel3 == $level) {
                    if ($l3 == 0) {
                        if ($l4 != 0) {
                            foreach ($userId as $key => $value) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $value)
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel4);
                                foreach ($collection as $customer) {
                                    $nextApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            $level3appr = $_order->getApproversLevel3();
                            $tempLevel = explode(',', $level3appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel3);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            if ($l4 != 0 && count($nextApprovers) == 0) {
                                $result['result_code'] = 0;
                                $result['message'] = 'Cannot proceed with approval process: Additional approvers are required, but are either inactive or not configured.';
                                $x = 0;
                            }
                        } else if ($l4 == 0) {
                            $check = 0;
                            $level3appr = $_order->getApproversLevel3();
                            $tempLevel = explode(',', $level3appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel3);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        } else {
                            $level3appr = $_order->getApproversLevel3();
                            $tempLevel = explode(',', $level3appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel3);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        }
                    } else {
                        $level3appr = $_order->getApproversLevel3();
                        $tempLevel = explode(',', $level3appr);
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            $collection = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('entity_id', $tempLevel[$i])
                                    ->addFieldToFilter('cust_category', $appId)
                                    ->addFieldToFilter('active_approver', $appA)
                                    ->addFieldToFilter('approval_level', $applevel3);
                            foreach ($collection as $customer) {
                                $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                            }
                        }
                        if ($l4 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                        if ($l3 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                    }
                } else if ($applevel4 == $level) {
                    if ($l4 == 0) {
                        if ($l5 != 0) {
                            foreach ($userId as $key => $value) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $value)
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel5);
                                foreach ($collection as $customer) {
                                    $nextApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            $level4appr = $_order->getApproversLevel4();
                            $tempLevel = explode(',', $level4appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel4);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                            if ($l5 != 0 && count($nextApprovers) == 0) {
                                $result['result_code'] = 0;
                                $result['message'] = 'Cannot proceed with approval Process: Additional approvers are required, but are either inactive or not configured.';
                                $x = 0;
                            }
                        } else if ($l5 == 0) {
                            $check = 0;
                            $level5appr = $_order->getApproversLevel5();
                            $tempLevel = explode(',', $level5appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel5);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        } else {
                            $level4appr = $_order->getApproversLevel4();
                            $tempLevel = explode(',', $level4appr);
                            for ($i = 0; $i < count($tempLevel); $i++) {
                                $collection = Mage::getModel('customer/customer')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', $tempLevel[$i])
                                        ->addFieldToFilter('cust_category', $appId)
                                        ->addFieldToFilter('active_approver', $appA)
                                        ->addFieldToFilter('approval_level', $applevel4);
                                foreach ($collection as $customer) {
                                    $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                                }
                            }
                        }
                    } else {

                        $level4appr = $_order->getApproversLevel4();
                        $tempLevel = explode(',', $level4appr);
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            $collection = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('entity_id', $tempLevel[$i])
                                    ->addFieldToFilter('cust_category', $appId)
                                    ->addFieldToFilter('active_approver', $appA)
                                    ->addFieldToFilter('approval_level', $applevel4);
                            foreach ($collection as $customer) {
                                $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                            }
                        }
                        if ($l5 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                        if ($l4 != 0 && count($optionalApprovers) == 0) {
                            $result['result_code'] = 0;
                            $result['message'] = 'Cannot proceed with approval process: Additional approvers are required at your same level, but are either inactive or not configured.';
                            $x = 0;
                        }
                    }
                } else {
                    if ($l5 == 0) {
                        $check = 0;
                        $level5appr = $_order->getApproversLevel5();
                        $tempLevel = explode(',', $level5appr);
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            $collection = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('entity_id', $tempLevel[$i])
                                    ->addFieldToFilter('cust_category', $appId)
                                    ->addFieldToFilter('active_approver', $appA)
                                    ->addFieldToFilter('approval_level', $applevel5);
                            foreach ($collection as $customer) {
                                $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                            }
                        }
                    } else {
                        $level5appr = $_order->getApproversLevel5();
                        $tempLevel = explode(',', $level5appr);
                        for ($i = 0; $i < count($tempLevel); $i++) {
                            $collection = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('entity_id', $tempLevel[$i])
                                    ->addFieldToFilter('cust_category', $appId)
                                    ->addFieldToFilter('active_approver', $appA)
                                    ->addFieldToFilter('approval_level', $applevel5);
                            foreach ($collection as $customer) {
                                $optionalApprovers[] = array('id' => $customer->getId(), 'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'), 'email' => $customer->getEmail());
                            }
                        }
                    }
                }
                if (!isset($result['result_code']))
                    $result['result_code'] = 1;
                if (count($optionalApprovers) > 0 && $check == 1 && count($nextApprovers) == 0) {
                    $temp = $nextApprovers;
                    $nextApprovers = $optionalApprovers;
                    $optionalApprovers = $temp;
                }
                $result['approvers']['next'] = $nextApprovers;
                $result['approvers']['optional'] = $optionalApprovers;
                $result['can_give_final'] = $check == 0 ? 'true' : 'false';
            }
        } else {
            $result['result_code'] = '0';
            $result['message'] = 'Please log in';
        }

        $this->getResponse()->setBody(json_encode($result));
    }

}
