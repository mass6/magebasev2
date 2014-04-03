<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Observer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('583c93dfc2e7d4af02663af3f93cc308'); ?><?php
class Aitoc_Aitproductslists_Model_Observer extends Mage_Adminhtml_Model_Sales_Order_Create
{
    private $_customerSession;
    private $_listSession;
    
    public function __construct() {
        $this->_customerSession = Mage::getSingleton('customer/session');
        $this->_listSession = Mage::getSingleton('aitproductslists/session');
        parent::__construct();
    }
    
    protected function isAdmin()
    {
        if (Mage::app()->getSafeStore()->getId() == 0)
        {
            return true;
        }
        return false;
    }
     public function customerAlert($observer)
    {

    }
    
     public function customerLogin()
    {
   
        $listIds = $this->_listSession->getNonLoginListIds();
         if (sizeof($listIds)<1)
        {
            return ;
        }
        $lists = Mage::getModel('aitproductslists/list')->loadByIds($listIds);
        if(!$lists)
        {
            return ;
        }
        
        foreach ($lists as $list)
        {
            $list->setCustomerId($this->_customerSession->getId())->save();
            $list->getQuote()->setCustomer($this->_customerSession->getCustomer())->save();
        }
        $this->_listSession->setNonLoginListIds(array());
        return ;
    }
    
    public function removeItemNotice($observer)
    {
        $item = $observer->getQuoteItem();
        # send email what item was deleted form list
    }
    
    
    public function addProductToOrder($observer)
    {
        $post = $observer->getRequest();
        $list = Mage::getModel('aitproductslists/list');
        $items = array();
        $aQty = array();
        if (isset($post['sidebar']))
        {
            if(isset($post['sidebar']['aitproductslists_add']))
            {
                $items = $post['sidebar']['aitproductslists_add'];
                $aQty = $post['sidebar']['aitproductslists_qty'];
                $observer->getOrderCreateModel()->getQuote()->setStoreId($post['store_id']);
                foreach ($items as $itemId => $qty) {
                    if (isset($aQty[$itemId]))
                    {
                        $qty = $aQty[$itemId];
                    }
                    $list->load($itemId, 'quote_id');
                    $item = $list->getQuote()->getItemById($itemId);
                    if ($item) {
                        $this->moveQuoteItem($item, 'order', $qty);
                    }
                } 
            }
            if(isset($post['sidebar']['aitproductslists_sidebar']))
            {
                $_items = array();
                $items = $post['sidebar']['aitproductslists_sidebar'];
                $observer->getOrderCreateModel()->getQuote()->setStoreId($post['store_id']);
                foreach ($items as $itemId => $qty) {
                    $list->load($itemId);
                    $_items = $list->getQuote()->getItemsCollection($itemId);
                    foreach ($_items as $_item)
                    {
                        $this->moveQuoteItem($_item, 'order', $_item->getQty());
                    }
                } 
            }
        }
        
    }
    
    public function installCartRule($observer)
    {       
          if ($observer->getControllerAction()->getRequest()->getModuleName() == "aitproductslists")
          {
                $this->_installRule();
          }
    }
    
    private function _installRule()
    {
        $name = "All products list rule";
        $websiteIds = array();
        if (Mage::getModel('salesrule/rule')->load($name,'name')->getId())
        {
            return ;
        }
        foreach (Mage::app()->getWebsites() as $website)
        {
            $websiteIds[] = $website->getId();
        }
        $customerGroupId = 1;
        $actionType = 'by_percent';
        $discount = 0;
         
        $shoppingCartPriceRule = Mage::getModel('salesrule/rule');
         
        $shoppingCartPriceRule
            ->setName($name)
            ->setDescription('')
            ->setIsActive(1)
            ->setWebsiteIds($websiteIds)
            ->setCustomerGroupIds(array($customerGroupId))
            ->setFromDate('')
            ->setToDate('')
            ->setSortOrder('')
            ->setSimpleAction($actionType)
            ->setDiscountAmount($discount)
            ->setStopRulesProcessing(0);
         
        $skuCondition = Mage::getModel('salesrule/rule_condition_product')
                            ->setType('salesrule/rule_condition_address')
                            ->setAttribute('base_subtotal')
                            ->setOperator('>')
                            ->setValue(0);
                           ;
         
        try {
            $shoppingCartPriceRule->getConditions()->addCondition($skuCondition);
            $shoppingCartPriceRule->save();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('catalog')->__($e->getMessage()));
            return;
        }
    }
    
    public function sentAdminNotice()
    {
        $collection = Mage::getModel('aitproductslists/notices')->getCollection()->selectNew();
        foreach ($collection as $notice)
        {
        $_notice[] = array(
                    'internal'      => '123123123123',
                    'severity'      => (int) Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE,
                    'date_added'    => gmdate('Y-m-d H:i:s', time()),
                    'title'         => Mage::helper('aitproductslists')->__("Customer Discount Request"),
                    'description'   => $notice->getMessage(),
                    'url'           => Mage::getModel('adminhtml/url')->getUrl('adminhtml/aitppl/edit',array('list_id'=>$notice->getListId(),"customer_id"=>$notice->getCustomerId(),'_secure'=>true)),
                );
         Mage::getModel('adminnotification/inbox')->parse(array_reverse($_notice));    
         $notice->setStatus('sent')->save();
      //  echo "<pre>"; print_r($notice); exit;
        }
    }
} } 