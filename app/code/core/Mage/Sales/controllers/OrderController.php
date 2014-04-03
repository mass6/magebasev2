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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales orders controller
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_OrderController extends Mage_Sales_Controller_Abstract
{

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

	public function approveAction() {
		
		$url = Mage::helper('core/url')->getCurrentUrl();
		
		 $tempUrl = explode('=',$url);
		$nextAppId = $tempUrl[1];
		Mage::log($nextAppId);
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$tempApprovers=array();
		$orderId = (int) $this->getRequest()->getParam('order_id');
		$objSapConfig = new Insync_Approve_Model_Id();
		$objSapConfig->Approver($nextAppId,$orderId,$customerId);
		$this->loadLayout('empty');
		$this->renderLayout();
		 $this->_redirect('sales/order/orderapproval');
	}
	
	public function nextApproverAction(){
		Mage::log('hit');
		
		$url = Mage::helper('core/url')->getCurrentUrl();
		Mage::log($url);
		// $tempUrl = explode('?',$url);
		// $tempStartUrl = $tempUrl[1];
		// $tempAppUrl = explode('&',$tempStartUrl);
		// $nextAppId = '';
		// if(count($tempAppUrl)== 2){
			// $app = $tempAppUrl[0];
			// $tempApp = explode('=',$app);
			// $app1 = $tempAppUrl[1];
			// $tempApp1 = explode('=',$app1);
			// if($tempApp[1] == 0){
				// $nextAppId = $tempApp1[1];
			// }else{
				// $nextAppId = $tempApp[1];
			// }
		// }else{
			// $tempApp2 = explode('=',$tempStartUrl);
			// $nextAppId = $tempApp2[1];
		// }
		// $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		// $tempApprovers=array();
		// $orderId = (int) $this->getRequest()->getParam('order_id');
		// $objSapConfig = new Insync_Approve_Model_Id();
		// $objSapConfig->Approver($nextAppId,$orderId,$customerId);
		$this->loadLayout('empty');
		$this->renderLayout();
		 $this->_redirect('sales/order/orderapproval');
	}
    /**
     * Customer order history
     */
    public function historyAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Orders'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

	    public function orderapprovalAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Approvals'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
			
		
	public function contractAction()
		{
		$url = Mage::helper('core/url')->getCurrentUrl();
		$pos = strpos($url, 'contract');
		if($pos != null){
			$tempContractId = explode('=',$url);
			$contractId = $tempContractId[1];
			if($contractId != '' && $contractId !=0){
			$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
			$write = Mage::getSingleton("core/resource")->getConnection("core_write");
			$sql = sprintf("update  `customer_entity` set `contract_id` = '%d' where `entity_id` = '%d' " ,$contractId,$customerId);
			$write->query($sql);
			$contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
			$contractStoreCode = '';
			foreach ($contractDetails as $each) {
				$contractStoreCode = $each['contract_store'];
				}
			if($contractId!=null){
				$cart = Mage::getSingleton('checkout/cart');
				foreach( Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item ){
					$cart->removeItem( $item->getId() );
				}
				$cart->save();
				$websiteStores = Mage::app()->getWebsite()->getStores();
				foreach ($websiteStores as $store) {
					$s=$store->getStoreId();
					if($s==$contractStoreCode){
						$store->setLocaleCode(Mage::getStoreConfig('general/locale/code', $store->getId()));
						$params = array(
                    '_query' => array()
						);
						$params['_query']['___store'] = $store->getCode();
						$baseUrl = $store->getUrl('', $params);
						$store->setHomeUrl($baseUrl);
						Mage::app()->getFrontController()
							->getResponse()
							->setRedirect($baseUrl);
						}
						else{
							continue;
						}
					}
				 unset($websiteStores);
				 }
			unset($stores);
			}
			}else{
			
		}
		
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Contract'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
	
	
	 public function rejectAction() {
	
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $orderId = (int) $this->getRequest()->getParam('order_id');
		$objSapConfig = new Insync_Approve_Model_Id();
		$objSapConfig->Reject($customerId,$orderId);
        $this->_redirect('sales/order/orderapproval/');
    }
	
    /**
     * Check osCommerce order view availability
     *
     * @deprecate after 1.6.0.0
     * @param   array $order
     * @return  bool
     */
    protected function _canViewOscommerceOrder($order)
    {
        return true;
    }

    /**
     * osCommerce Order view page
     *
     * @deprecate after 1.6.0.0
     *
     */
    public function viewOldAction()
    {
        $this->_forward('noRoute');
        return;
    }
	public function addCommentAction() {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$customer1= Mage::getModel('customer/customer')->load($customerId);
		$name=$customer1->getFirstname() . ' ' . $customer1->getLastname();
        $orderId = (int) $this->getRequest()->getParam('order_id');

        $order = Mage::getModel('sales/order')->load($orderId);
        $comment = (string) $this->getRequest()->getQuery('history');
		$final=$name.' | '.$comment;
        $order->addStatusHistoryComment($final, $order->getStatus());
        $order->save();
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT MAX(  `entity_id` ) FROM  `sales_flat_order_status_history` WHERE  `parent_id` = " . $orderId . "";
        $entityid = $read->fetchAll($sql);
        $tempentityid = '';
        foreach ($entityid as $key) {
            foreach ($key as $key1 => $value) {

                $tempentityid = $value;
            }
        }
        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
        $sql = sprintf("update `sales_flat_order_status_history` set `customer_id` = '%d' where `entity_id` = '%d'", $customerId, $tempentityid);
        $write->query($sql);
        $this->loadLayout('empty');
        $this->renderLayout();

        $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
    }
	
	
} 
