<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Notifier.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('9049d079ff5f79f394f3609626b423d0'); ?><?php
class Aitoc_Aitproductslists_Model_List_Notifier extends Aitoc_Aitproductslists_Model_List_Abstract
{
    
    public function sendNotice($recipient,$reason,$vars = array())
    {
        $email = $this->_getRecipient($recipient);
        $emailId = $this->_getReason($reason);
        $this->send($email, $emailId, $vars);
    }
    
    public function subscribeToProductAlert($product,$customerId)
    {
        
        /**
         *  Add to stock alert
         *  
         */
        $model = Mage::getModel('productalert/stock')
                ->setCustomerId($customerId)
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
        /**
         *  Add to price alert
         * 
         * 
         */
        $model  = Mage::getModel('productalert/price')
                ->setCustomerId($customerId)
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
    }
    
     public function unsubscribeToProductAlert($product,$customerId)
    {
        $canUnsubscribe = true;
        $listCollection = Mage::getModel('aitproductslists/list')->getCollection()->selectUnSubscribe($customerId);
        foreach ($listCollection as $_list)
        {
            if ($_list->getQuote()->getItemByProduct($product))
            {
                $canUnsubscribe = true;
                return ;
            }
        }
        /**
         *  Remove to stock alert
         *  
         */
        $model = Mage::getModel('productalert/stock')
                ->setCustomerId($customerId)
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $model->loadByParam()->delete();  
 
        /**
         *  Remove to price alert
         * 
         */
        $model  = Mage::getModel('productalert/price')
                ->setCustomerId($customerId)
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $model->loadByParam()->delete();
    }
    
    protected function _getReason($reason)
    {
        switch ($reason)
        {
            case "discount" :
                return Mage::getStoreConfig('aitproductslists/discount/template', Mage::app()->getStore()->getStoreId());
        }
    }
    
    protected function _getRecipient($recipient)
    {
        switch($recipient)
        {
            case "admin":
                if (!Mage::getStoreConfig('aitproductslists/discount/email'))
                {
                return Mage::getStoreConfig('trans_email/ident_general/email');
                }
                return Mage::getStoreConfig('aitproductslists/discount/email');
                break;
        }
    }
    
    public function send($email, $emailId, $vars = array())
    {
        $name = 'Mail Discount';
        $storeId = Mage::app()->getStore()->getStoreId();
     //   echo "<pre>"; print_r($vars);
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
        
        $mailTemplate = Mage::getModel("core/email_template");
        $mailTemplate->setDesignConfig(array("area"=>"frontend", "store"=>$storeId));
        $mailTemplate->setTemplateSubject($name);
        $mailTemplate->sendTransactional(
            $emailId,
            array("email"=>'noreply@owner.com', "name"=>"Customer Request"),
            $email,
            $name,
            $vars
        );
       //   echo "<pre>"; print_r($mailTemplate->getData()); exit;
        $translate->setTranslateInline(true);
        return $this;
    }
    
    public function changeProductStatus($product)
    {
        $collection = Mage::getModel('aitproductslists/quote_item')->getCollection()->getItemsToDisable(Mage::getModel('sales/quote'),$product->getId());
//        if (sizeof($collection->getData())==0)
//        {
//            return ;
//        }
//        echo "<pre>"; print_r($collection->getData()); exit;
        $email   = Mage::getStoreConfig('aitproductslists/product/disable');
        $customers = array();
        $vars = array();
        $_customer = Mage::getModel('customer/customer');

        foreach($collection->getData() as $item)
        {
            $item = new Varien_Object($item);                       
            
            $customer = $_customer->load($item->getCustomerId());
            if (!in_array($customer->getEmail(), $customers))
            {
                $emailId = Mage::getStoreConfig('aitproductslists/product/disable/template', $item->getStoreId());
        
                $vars = array('store' => Mage::app()->getStore($item->getStoreId())->getWebsite()->getName(),
                              'customer' => $customer->getName(),
                              'productName' => $item->getName(),
                              'listGridUrl'=> Mage::getUrl('aitproductslists/list/grid')
                        );
                $customers[] = $customer->getEmail();
                $this->sendDisableEmail($customer, $vars, $email, $emailId);
            }
        }
        
    }
    
    public function sendDisableEmail($customer,$vars,$email,$emailId)
    {
        $name = 'Product Disabled';
        $storeId = Mage::app()->getStore()->getStoreId();
       
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
        
        $mailTemplate = Mage::getModel("core/email_template");
        $mailTemplate->setDesignConfig(array("area"=>"frontend", "store"=>$storeId));
        $mailTemplate->setTemplateSubject($name);
        $mailTemplate->sendTransactional(
            $emailId,
            Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
            $customer->getEmail(),
            $name,
            $vars
        );
    //  echo "<pre>"; print_r($vars); print_r($mailTemplate->getData()); exit;
        $translate->setTranslateInline(true);
        return $this;
    }
    
} } 