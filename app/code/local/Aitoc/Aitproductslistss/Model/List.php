<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('d02ab91a64066f19e4277ec8b973b88d'); ?><?php
class Aitoc_Aitproductslists_Model_List extends Mage_Core_Model_Abstract
{
    protected $_summaryQty = null;
    
    /**
     * @var Aitoc_Aitproductslists_Model_Quote
     */
    protected $_quote;
    
    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;
    
    /**
     * @var Aitoc_Aitproductslists_Model_List_Discount 
     */
    protected $_discount;
    
    /**
     * @var Aitoc_Aitproductslists_Model_List_Reminder 
     */
    protected $_reminder;
    
    const AITPPL_MERGE_IN = "in";
    const AITPPL_MERGE_OUT = "out";
    const AITPPL_MERGE_NEW = "new";
    
    const AITPPL_LIST_DISCONT_STATUS_NO = 0;
    const AITPPL_LIST_DISCONT_STATUS_WAITING = 1;
    const AITPPL_LIST_DISCONT_STATUS_DECLINE = 2;
    const AITPPL_LIST_DISCONT_STATUS_APPROVE = 3;

    protected function _construct()
    {
        $this->_init('aitproductslists/list');
    }
    
    /**
     * Retrieve PPL session model
     *
     * @return Aitoc_Aitproductslists_Model_Session
     */
    public function getListSession()
    {
        return Mage::getSingleton('aitproductslists/session');
    }
    
    /**
     * Retrieve owner of the list or current customer
     * 
     *  @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if(!$this->_customer)
        {
            if($this->getData('customer_id'))
            {
                $this->_customer = Mage::getModel('customer/customer')->load($this->getData('customer_id'));
            }
            else
            {
                $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
            }
        }
        return $this->_customer;
    }
    
    public function getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomerId();
    }
    
    public function getDiscount()
    {
        if(!$this->_discount)
        {
            $this->_discount = Mage::getModel('aitproductslists/list_discount')->load($this->getId(), 'list_id');
            $this->_discount->setList($this);
        }
        return $this->_discount;
    }

    public function checkDiscount()
    {
        if (!$this->getDiscount()->getPrice())
        {
            return false;
        }
        if ($this->getDiscount()->getPrice()==0)
        {
            return false;    
        }
        if ($this->getPayQty() < (int) $this->getDiscount()->getMinQty())
        {
            return false;    
        }
        if ($this->getDiscount()->getToDate())
        {
            $dateFrom = Mage::getModel('core/date')->timestamp($this->getDiscount()->getFromDate());
            $dateTo = Mage::getModel('core/date')->timestamp($this->getDiscount()->getToDate());
            $dateNow = Mage::getModel('core/date')->timestamp(time());
            if ($dateFrom <= $dateNow AND $dateNow <= $dateTo)
            {
                 return true;        
            }
            else
            {
                return false;
            }
        }
        return true;     
    }
    
    public function getReminder()
    {
        if(!$this->_reminder)
        {
            $this->_reminder = Mage::getModel('aitproductslists/list_reminder')->load($this->getId(), 'list_id');
            $this->_reminder->setList($this);
        }
        return $this->_reminder;
    }

    public function addProduct(Mage_Catalog_Model_Product $product, $quoteId=null)
    {
        $params = Mage::app()->getRequest()->getParams();
        $request = Mage::helper('aitproductslists')->getProductRequest($params);
        $quote = $this->getQuote($quoteId);
        if (!$quote->getId())
        {
            $quote->getBillingAddress();
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }
            try {
            $result = $quote->addProduct($product, $request);
            } 
            catch(Mage_Core_Exception $e)
            {
                $result = $e->getMessage();
            }
             if (is_string($result)) {
                 Mage::throwException($result);
                 return false;
            }
            
            $quote->collectTotals();
	    $quote->save();
            foreach ($quote->getAllItems() as $_item)
            {
                 Mage::getModel('aitproductslists/list_item')->addFromController($_item->getId());
            }
            if ($this->getCustomerId() AND $this->getProductChangeNotifyStatus() == 1)
            {
                    $this->getNotifier()->subscribeToProductAlert($product,$this->getCustomerId());
            }
            if(Mage::getStoreConfig('aitproductslists/customer/reset'))
            {
                $this->clearDiscount();
            }
   	return $this;
    }
    
    /**
     * Get quote object associated with list. By default it is current customer session quote
     *
     * @return Aitoc_Aitproductslists_Model_Quote
     */
    public function getQuote($quoteId = false)
    {
        $quote = Mage::getModel("aitproductslists/quote");
        
        if ($quoteId)
        {
           return $quote->loadByIdWithoutStore($quoteId)->collectTotals()->save();
        }
        if ($this->getQuoteId())
        {
            if(!$this->_quote)
            {
                $this->_quote = $quote->loadByIdWithoutStore($this->getQuoteId());
            }
            return $this->_quote;
        }

        return $quote->applyBaseData();
    }
    
    /**
     * Load collection of lists by customer
     * 
     * @return Aitoc_Aitproductslists_Model_Mysql4_List_Collection
     */
    public function getCollectionByCustomer($customerId)
    {
        return $this->getCollection()->getCollectionByCustomer($customerId);
    }
    
    /**
     * Load collection of lists by their ids
     * 
     * @return Aitoc_Aitproductslists_Model_Mysql4_List_Collection
     */
    public function loadByIds($listIds = array())
    {
        if (count($listIds)<1)
        {
            return array();
        }
        return $this->getCollection()->loadByIds($listIds);
    }
    
    public function validate()
    {
        if(!$this->getName())
        {
            //$this->getListSession()->addError(Mage::helper('aitproductslists')->__('Please specify the name of the list.'));
            return false;
        }
    
        return true;
    }
    
    protected function _beforeSave()
    {
        if (!$this->validate())
        {
            return ;
        }
        if(!$this->getId() && !$this->getQuoteId())
        {
            $quote = $this->getQuote()->setCustomerId($this->getCustomerId());
            if(Mage::app()->getStore()->isAdmin() && !$this->getId() && $this->getStoreId())
            {
                $quote->setStoreId($this->getStoreId());
            }
            $quote->save();
            $this->setQuoteId($quote->getId());
            
            $publicKeyParams = array(
                $this->getQuoteId(),
                $this->getCustomerId(),
                now(),
                $this->getName()
            );
            if (!Mage::app()->getStore()->isAdmin())
            {
                $this->setCustomerId($this->getCustomerId());                       # maybe bug. line commented from admin. don't save customer_id from admin area
            }
            $this->setPublicKey($this->generatePublicKey($publicKeyParams));
        }
    }
    
    protected function _afterSave()
    {
        if(!Mage::app()->getStore()->isAdmin())
        {
            if(!$this->getCustomerId())
            {
                $this->getListSession()->setNonLoginListId($this->getId());
            }
            $this->getListSession()->setCurrentListId($this->getId());
            $this->getListSession()->setQuoteId($this->getQuoteId());
        }
        
        $this->load($this->getId());  
        
        if ($this->getCustomerId() AND $this->getProductChangeNotifyStatus() == 1)
        {
            foreach ($this->getQuote()->getItemsCollection() as $item)
            {
                if (!$item->isDeleted() && !$item->getParentItemId()) {
                    $this->getNotifier()->subscribeToProductAlert($item->getProduct(),$this->getCustomerId());
                }
            }
        }
        
        if ($this->getCustomerId() AND $this->getProductChangeNotifyStatus() == 0)
        {
            foreach ($this->getQuote()->getItemsCollection() as $item)
            {
                if (!$item->isDeleted() && !$item->getParentItemId()) {
                    $this->getNotifier()->unsubscribeToProductAlert($item->getProduct(),$this->getCustomerId());
                }
            }
        }
        
        $reminderId = null;
     
        if($this->getReminder()->getId())
        {
            $reminderId = $this->getReminder()->getId();
        }
        $reminderData = array();
        if (Mage::app()->getRequest()->getPost('reminder'))
        {
            $reminderData = Mage::app()->getRequest()->getPost('reminder');
        }
        if (Mage::app()->getRequest()->getPost('aitppl_reminder'))
        {
             $reminderData = Mage::app()->getRequest()->getPost('aitppl_reminder');
        }
        if (sizeof($reminderData)>0)
        {
            $this->getReminder()->setData($reminderData)->setId($reminderId)->save();
        }
        $this->getDiscount()->save();
        
        $this->getService()->saveItemNotes();
       
    }
    
    public function generatePublicKey($params,$exists = false)
    {
        $string = join('',$params);
        $key = md5($string);
        if ($exists)
        {
            $this->setPublicKey($key)->save();
        }
        return $key;
    }

    /**
     * Get shopping cart items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount()*1;
    }

    /**
     * Get shopping cart summary qty
     *
     * @return decimal
     */
    public function getItemsQty()
    {
        return $this->getQuote()->getItemsQty()*1;
    }
    
    public function getItemsSummaryQty()
    {
        return $this->getQuote()->getItemsSummaryQty();
    }

    /**
     * Get shopping cart items summary (include config settings)
     *
     * @return decimal
     */
    public function getSummaryQty()
    {
        $quoteId = $this->getListSession()->getQuoteId();
        if (!$quoteId && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $quote = $this->getQuote();
            $quoteId = Mage::getSingleton('aitproductslists/session')->getQuoteId();
        }

        if ($quoteId && $this->_summaryQty === null) {
            if (Mage::getStoreConfig('checkout/cart_link/use_qty')) {
                $this->_summaryQty = $this->getItemsQty();
            } else {
                $this->_summaryQty = $this->getItemsCount();
            }
        }
        return $this->_summaryQty;
    }
    
    /** Remove item from list
     *
     * @param   int $itemId
     * @return  Aitoc_Aitproductslists_Model_List
     */
    public function removeItem($itemId, $listId=null)
    {
        if ($listId)
        {
            $list = $this->load($listId);
            $quote = $this->getQuote($list->getQuoteId());
        }
        else
        {
            $quote = $this->getQuote();
        }
        $item = $quote->getItemById($itemId);
        $product = $item->getProduct();
        $quote->removeItem($itemId);
        $quote->collectTotals()->save();
        if ($this->getCustomerId() AND $this->getProductChangeNotifyStatus() == 1)
        {
//                $item = $quote->getItemById($itemId);
                $this->getNotifier()->unsubscribeToProductAlert($product,$this->getCustomerId());
        }
        if(Mage::getStoreConfig('aitproductslists/customer/reset'))
        {
            $this->clearDiscount();
        }
        return $this;
    }
    
//    public function clearPrivateData()
//    {
//        $data = array(
//            "pay_qty"                       => 0,
//            "product_change_notify_status"  => 0, 
//        );
//        $this->getReminder()->setStatus(0);
//        $this->getDiscount()->setStatus(0);
//        
//        foreach ($data as $k=>$v)
//        {
//            $this->setData($k,$v);
//        }
//        return $this;
//    }
    
    public function clearDiscount()
    {
         $discount = array(
            "id"                            => $this->getDiscount()->getId(),
            "price"                         => 0,
            "min_qty"                       => "", 
            "from_date"                     => null, 
            "to_date"                       => null, 
            "is_approved"                   => 0, 
        );
        if ($this->getDiscount()->getId())
        {
            $this->getDiscount()->setData($discount)->save();
        }
        
        $data = array(
            "pay_qty"                       => 0,
            "discount_list_status"          => 0,
        );
        foreach ($data as $k=>$v)
        {
            $this->setData($k,$v);
        }
        $this->save();
        
        return $this;
    }
    
    public function clearPrivateData()
    {
        $reminder = array(
            'id'                            => $this->getReminder()->getId(),
            "status"                        => 0,
            "start_date"                    => null,
            "period"                        => 0, 
            "frequency"                     => 0, 
            "max_notify_qty"                => 0, 
        );
    
        if($this->getReminder()->getId())
        {
            $this->getReminder()->setData($reminder)->save();
            $this->getReminder()->getShedule()->clear($this->getId());
        }
        
        $this->setData('product_change_notify_status',0)->save();
        
        $this->clearDiscount();
        
        return $this;
    }
    
    public function duplicate()
    {
        $parentQuote = $this->getQuote();
        $parentItems = $parentQuote->getAllVisibleItems();
        $quote = Mage::getModel('aitproductslists/quote')->setData($parentQuote->getData())->setId(null);
        $quote->getBillingAddress();
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->save();
       // echo "<pre>"; print_r($parentQuote->getItemsCollection()->getData());
        //$quote->merge($parentQuote)->collectTotals()->save();                 v 1.0
        foreach ($parentItems as $item)
        {
            Mage::helper('aitproductslists')->prepareItemRequest($item->getId());
            $product = Mage::helper('aitproductslists')->initProduct();
            $product->setStoreId($quote->getStoreId());
            $request = Mage::helper('aitproductslists')->getProductRequest(Mage::app()->getRequest()->getParams());
            $newItem = $quote->addProduct($product, $request);
        }
        
        $quote->collectTotals()->save();   
        foreach ($quote->getAllItems() as $item)
        {
            Mage::getModel('aitproductslists/list_item')->addFromController($item->getId());
        }
        $publicKeyParams = array(
            $quote->getId(),
            Mage::getSingleton('customer/session')->getCustomerId(),
            now(),
            $this->getName()
        );
        
        $newList = $this->setId(null)
                        ->setName($this->getName()."(d)")
                        ->setQuoteId($quote->getId())
                        ->setPublicKey($this->generatePublicKey($publicKeyParams))
                        ->clearPrivateData()
                        ->save();

        if (!Mage::getSingleton('customer/session')->getCustomerId() )
        {
            $this->getListSession()->setNonLoginListId($this->getId());
        }
        return $newList;
    }
    
    public function addShare($customerName)
    {
        $list = $this->duplicate();
		$customerId = 0;
		if (Mage::getSingleton('customer/session')->getCustomerId())
		{
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		}
        $list->setCustomerId($customerId)
                ->setName($list->getName()." ".Mage::helper('aitproductslists')->__("from")." ".$customerName."");
        $list->save();
        return true;
    }
    
    public function getService()
    {
        return Mage::getModel('aitproductslists/list_service');
    }  
    
    public function getNotifier()
    {
        return Mage::getModel('aitproductslists/list_notifier');
    }
    
    public function updateItem($itemId, $requestInfo = null, $updatingParams = null)
    {
        try {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                Mage::throwException(Mage::helper('aitproductslists')->__('Quote item does not exist.'));
            }
            $productId = $item->getProduct()->getId();
            $product = $this->_getProduct($productId);
            $request = $this->_getProductRequest($requestInfo);
            if ($product->getStockItem()) {
                $minimumQty = $product->getStockItem()->getMinSaleQty();
                // If product was not found in cart and there is set minimal qty for it
                if ($minimumQty && ($minimumQty > 0)
                    && ($request->getQty() < $minimumQty)
                    && !$this->getQuote()->hasProductId($productId)
                ) {
                    $request->setQty($minimumQty);
                }
            }

            $result = $this->getQuote()->updateItem($itemId, $request, $updatingParams);
            if (Mage::getStoreConfig('aitproductslists/customer/reset'))
            {
                $this->clearDiscount()->save();
            }
        } catch (Mage_Core_Exception $e) {
            $this->getListSession()->setUseNotice(false);
            $result = $e->getMessage();
        }

        /**
         * We can get string if updating process had some errors
         */
        if (is_string($result)) {
            if (Mage::getSingleton('checkout/session')->getUseNotice() === null) {
                Mage::getSingleton('checkout/session')->setUseNotice(true);
            }
            Mage::throwException($result);
        }

        Mage::dispatchEvent('aitproductslists_lists_product_update_after', array('quote_item' => $result, 'product' => $product));
        return $result;
    }
    
     protected function _getProduct($productInfo)
    {
        if ($productInfo instanceof Mage_Catalog_Model_Product) {
            $product = $productInfo;
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productInfo);
        } else {
            Mage::throwException(Mage::helper('aitproductslists')->__('The product could not be found.'));
        }
        return $product;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object();
            $request->setQty($requestInfo);
        } else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }
        return $request;
    }
    
    /**
     * Adding products to cart by ids
     *
     * @param   array $productIds
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProductsByIds($productIds)
    {
        $allAvailable = true;
        $allAdded     = true;

        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $productId = (int) $productId;
                if (!$productId) {
                    continue;
                }
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                if ($product->getId() && $product->isVisibleInCatalog()) {
                    try {
                        $this->getQuote()->addProduct($product);
                    } catch (Exception $e){
                        $allAdded = false;
                    }
                } else {
                    $allAvailable = false;
                }
            }

            if (!$allAvailable) {
                $this->getListSession()->addError(
                    Mage::helper('aitproductslists')->__('Some of the requested products are unavailable.')
                );
            }
            if (!$allAdded) {
                $this->getListSession()->addError(
                    Mage::helper('aitproductslists')->__('Some of the requested products are not available in the desired quantity.')
                );
            }
        }
        if(Mage::getStoreConfig('aitproductslists/customer/reset'))
        {
            $this->clearDiscount();
        }
        return $this;
    }
} } 