<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Purchase.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('70b73780a2e405f9ff848c55ff996f9c'); ?><?php
class Aitoc_Aitproductslists_Model_List_Purchase extends Aitoc_Aitproductslists_Model_List_Abstract
{
    
    protected $_collection;
    protected $_updatedItems = array();
    protected $_listAmount = array();
    protected $_allowedMethods = array(
                'aitproductslists' => array('sendToCart', 'index'),
                'checkout' => 'updatePost',
    );

    protected function _construct()
    {
        $this->_init('aitproductslists/list_purchase');
    }
    
    private function _add($listId,$itemQty,$productId,$itemId,$cartId = null)
    {
        
        if (!$cartId)
        {
            $cartId = $this->_getCartId();
        }
        $listQty = $itemQty;
        if (!$listId && Mage::helper('aitproductslists')->isAiteditablecartActive())
        {
            $key = $this->_createItemKey(array($cartId,$itemQty,$productId));
            //echo "<pre>"; print_r($rItems = Mage::getSingleton('aitproductslists/session')->getData('remove_items')); exit;
            $rItems = Mage::getSingleton('aitproductslists/session')->getData('remove_items');
            if (isset($rItems[$key]))
            {
                $listId = $rItems[$key];
            }
        }
        $listQty = $this->_getListQty($listId,$productId);
        $data = array(
                'id' => null,
                'list_id' => $listId,
                'cart_id' => $cartId,
                'qty_in_list' => $listQty,
                'qty_in_cart' => $itemQty,
                'product_id' => $productId,
                'item_id' => (int)$itemId,
            );

            $this->setData($data)->save();
            $this->_update($itemId,$itemQty);
    }
    
    protected function _getListQty($listId,$productId)
    {
        $list = Mage::getModel('aitproductslists/list')->load($listId);
        return $list->getQuote()->getItemsCollection()->loadByProductId($productId)->getFirstItem()->getQty();
    }
    protected function _getAllowedMethods()
    {
        $methods = $this->_allowedMethods;
        if (!Mage::helper('aitproductslists')->isAiteditablecartActive())
        {
            unset($methods['checkout']);
        }
        return $methods;
    }
    
    /**
     * Sick function for getting list id.
     *
     * @return int
     */    
    protected function _getListId(Varien_Event_Observer $observer)
    {
        $listId = (int) (Mage::app()->getRequest()->getParam('list_id'));
        
        $helper = Mage::helper('aitproductslists');
        if (!$listId && $helper->isAiteditablecartActive())
        {
            if ($this->getListId())
            {
                return $this->getListId();
            }
            $listId = null;
            $item = $observer->getDataObject();            
            
            $aitproductslistsOption = $item->getOptionByCode('aitproductslists');
            if ($aitproductslistsOption instanceof Mage_Sales_Model_Quote_Item_Option)
            {
                $listId = $aitproductslistsOption->getValue();
            }
        }
        
        return $listId;
    }
    
    public function add($observer)
    {
        if ($this->_checkIsAddAllowed($observer, Mage::app()->getRequest()->getModuleName(), Mage::app()->getRequest()->getActionName()))
        {
            $item = $observer->getDataObject();
            if ($item->getParentItem())
            {
                return true;
            }
            $itemId = $item->getItemId();
            
            $qty = 1;
            $productId = "";
                $qty = $item->getQty();
                $productId = $item->getProduct()->getId();
            if ($this->_exist($item))
            {
               return $this->changeQty($item);
            }
            
            $this->_add($this->_getListId($observer), $qty, $productId, $itemId);
        }
        return true;
    }
    
    protected function _checkIsAddAllowed($observer, $module, $method)
    {        
        $result = false;
        
        foreach ($this->_getAllowedMethods() as $moduleName => $methods)
        {
            if ($module == $moduleName)
            {
                $methods = is_array($methods) ? $methods : array($methods);
                if (false !== in_array($method, $methods))
                {
                    $result = true;
                    break;
                }
            }
        }
        if (('aitproductslists' == $module) && ('sendToCart' == $method))
        {
            return $result;
        }
        $helper = Mage::helper('aitproductslists');
        if ($result && $helper->isAiteditablecartActive())
        {
            $result = false;

            $item = $observer->getDataObject();       
            $aitproductslistsOption = $item->getOptionByCode('aitproductslists');
            if ($aitproductslistsOption instanceof Mage_Sales_Model_Quote_Item_Option)
            {
                $listId = $aitproductslistsOption->getValue();
                $list = Mage::getModel('aitproductslists/list');
                $list->load($listId);
                if ($list->getId())
                {
                    $quote = Mage::getModel('sales/quote');
                    $quote->load($list->getQuoteId());
                    if ($quote->getId())
                    {
                       $item->addNotRepresentOptionCode('option_ids'); // FIX order of custom options in cart according to order in admin, then remove that string (only one)
                        $item->addNotRepresentOptionCode('aitproductslists');                        
                        $quoteItems = $quote->getItemsCollection(false);
                        foreach ($quoteItems as $quoteItem)
                        {                            
                            if ($item->compare($quoteItem))
                            {
                                $result = true;
                                break;
                            }
                        }
                        $item->removeNotRepresentOptionCode('aitproductslists');                        
                        $item->removeNotRepresentOptionCode('option_ids'); // FIX order of custom options in cart according to order in admin, then remove that string (only one)
                    }                        
                }
            }
            else
            {
                $result = true;
            }
        }
       
        return $result;
    }
    
    public function addFromAdmin($item,$listId,$cartId)
    {
            $itemId = $item->getItemId();
            $qty = 1;
            $productId = "";
            $qty = $item->getQty();
            $productId = $item->getProduct()->getId();
            if ($this->_exist($item,$cartId,$listId))
            {
               return $this->changeQty($item);
            }

            $this->_add($listId,$qty,$productId,$itemId,$cartId);
        
    }
    
    private function _createItemKey($data)
    {
        unset($data['item_id']);
        unset($data['qty_in_cart']);
        unset($data['id']);
        unset($data['list_id']);
        return join("-",$data); 
    }
    
    public function remove($observer)
    {
        $item = $observer->getQuoteItem();
        $_item = $this->load($item->getItemId(),'item_id');

        
        if ($_item)
        {
            $key = $this->_createItemKey($_item->getData());
            $rItems = Mage::getSingleton('aitproductslists/session')->getData('remove_items');
            $rItems[$key] = $_item->getListId();
            Mage::getSingleton('aitproductslists/session')->setData('remove_items',$rItems);
            $_item->delete();
        }
    }
    
    public function getOrderItemByProductId($order,$productId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId()==$quoteItemId) {
                return $item;
            }
        }
        return null;
    }
    
    public function confirmMultishipping($observer,$quote)
    {

        $amount = array();
        $order = $observer->getOrder();
        $oList = Mage::getModel('aitproductslists/list');
        $oMultishipping = Mage::getModel('aitproductslists/list_order_multishipping');
        $listOrder = Mage::getModel('aitproductslists/list_order');    
        $oItem = Mage::getModel("sales/quote_item")->setQuote($quote);
        $oProduct = Mage::getModel('catalog/product');
        $listItems = $this->getCollection()->cartFilter($order->getQuoteId());  
        $orderItems = $order->getAllVisibleItems();
        $this->_loadListAmount($listItems,$order->getId());                     //list amount x coefficient
    
        foreach ($listItems as $listItem)
        {
            $item = $oItem->load($listItem->getItemId());         
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                foreach ($orderItems as $orderItem)
                {
                    $product = $oProduct->load($orderItem->getProductId());
                    if($item->representProduct($product))
                    {
                        $amount[$listItem->getListId()] += $item->getPrice() * $orderItem->getQtyOrdered();
                    }
                }
             }
        }
       
        $resentList = array();
    
        foreach ($this->_listAmount as $listId=>$listAmount)
        {
            $oMultishipping->load($order->getQuoteId(),'quote_id');
            if ($oMultishipping->getStatus() == 'ok')
            {
                continue ;
            }
            $multiId = null;
            if($oMultishipping->getId())
            {
                $multiId = $oMultishipping->getId();
            }
            if ($amount[$listId]>=$listAmount)
            {
                if (!in_array($listId,$resentList))
                {
                    $resentList[] = $listId;
              
                    $oList->load($listId);
       
                    $oList->setPayQty($oList->getPayQty() + 1)->save();
                    
                    $oMultishipping
                        ->setQuoteId($order->getQuoteId())
                            ->setId($multiId)
                            ->setOrderId($order->getId())
                            ->setStatus('ok')
                            ->setTotal($oMultishipping->getTotal() + $amount[$listId])
                            ->save();
                    $existOrder = $listOrder->load($order->getId(),'order_id');
                    if($existOrder->getListId()!==$listId AND $existOrder->getOrderId()!==$order->getId())
                    {
                        $listOrder->setId(null)->setListId($listId)->setOrderId($order->getId())->save();
                    }
                }
            }
            else
            {
                $oMultishipping
                        ->setQuoteId($order->getQuoteId())
                            ->setId($multiId)
                            ->setOrderId($order->getId())
                            ->setStatus('waiting')
                            ->setTotal($oMultishipping->getTotal() + $amount[$listId])
                            ->save();
            }
        }
    }
    
    public function confirm($observer)
    {
        $quote = Mage::getModel('aitproductslists/quote')
                ->setStoreId($observer->getOrder()->getStoreId())
                        ->load($observer->getOrder()->getQuoteId());
        if ($quote->getIsMultiShipping())
        {
             
            return $this->confirmMultishipping($observer,$quote);
        }
        $amount = 0;
        $oList = Mage::getModel('aitproductslists/list');
        // $listItems = $this->load(,'cart_id');
        $purchaseItems = $this->getCollection()->cartFilter($observer->getOrder()->getQuoteId());  
       
        $this->_loadListAmount($purchaseItems,$observer->getOrder()->getId());
        $oItem = Mage::getModel("aitproductslists/quote_item");
        foreach ($purchaseItems as $purchaseItem)
        {
            $item = $oItem->load($purchaseItem->getItemId());
            $amount += $item->getRowTotal();
        }
        foreach ($this->_listAmount as $listId=>$listAmount)
        {
            if ($amount>=$listAmount)
            {
                
                $list = $oList->load($listId);
                $list->setData('pay_qty',$list->getPayQty() + 1)->save();
            }
        }
    }
    

    protected function _loadListAmount($purchaseItems,$orderId)
    {
        $oList = Mage::getModel('aitproductslists/list');
        $listOrder = Mage::getModel('aitproductslists/list_order');              
        $listAmountPercent = Mage::getStoreConfig("aitproductslists/discount/percent", Mage::app()->getStore()->getId());
        if(!$listAmountPercent)
        {
            return;
        }
        foreach ($purchaseItems as $item)
        {
            if (!isset($this->_listAmount[$item->getListId()]))
            {
                $list = $oList->load($item->getListId());
                $listOrder->setId(null)->setListId($list->getId())->setOrderId($orderId)->save();
            //    $list->setPayQty($list->getPayQty())->save();
                $this->_listAmount[$item->getListId()] = $list->getQuote()->getSubtotal() * ($listAmountPercent / 100);
            }
        }
    }

    public function update($observer = array())
    {
        foreach ($observer->getInfo() as $itemId=>$qtys)
        {
           $this->_update($itemId,$qtys['qty']);
        }
    }
    
    private function _update($itemId,$qty)
    {
       $collection = $this->getCollection()->cartFilter($this->_getCartId())->itemFilter($itemId);
       $oItem = Mage::getModel("aitproductslists/quote_item");
    
       if (count($collection)>0)
       {
           foreach($collection as $item)
           {
                if (in_array($item->getId(),$this->_updatedItems))
                {
                    continue;
                }
      //         echo "<pre>"; print_r($item->getData()); 
                $item->setId($item->getId())->setQtyInCart($qty)->save();
                $this->_updatedItems[] = $item->getId();
           }
        
       } 
//       else
//       {
//           echo 2;
//           $item = $oItem->load($itemId);
//           $collection = $this->getCollection()->cartFilter($this->_getCartId())->productFilter($item->getProductId());
//           $list = Mage::getModel('aitproductslists/list');
//           foreach ($collection as $_item)
//           {
//               $list = $list->load($_item->getListId());
//               $listItems = $list->getQuote()->getAllVisibleItems();
//               foreach ($listItems as $listItem)
//               {
//                   if($item->setQuote($list->getQuote())->representProduct($listItem->getProduct()))
//                   {
//                       echo 3;
//                       $_item->setQtyInCart($item->getQty())->setItemId($itemId)->save();
//                   }
//               }
//           }
//       }
//       exit;
      
    }
    
    public function changeQty($_item)
    {
        
        $item = array_shift($this->_collection->getItems());
        if (in_array($item->getId(),$this->_updatedItems))
        {
            return true;
        }
        
        if ($_item->getQtyToAdd())
        {
            $item->setQtyInCart($item->getQtyInCart()+$_item->getQtyToAdd());
        }
        else
        {
             $item->setQtyInCart($item->getQtyInCart()+$_item->getQty());
        }
        $item->save();
        return true;
    }    
    
    protected function _getListIdFromItem($_item,$listId=null)
    {
        if ($listId)
        {
            return $listId;
        }
        $listId = null;
        if (!$listId)
        {
            $listId = Mage::app()->getRequest()->getParam('list_id');
        }
        if (!$listId)
        {
            $collection = $this->getCollection()->cartFilter($this->_getCartId())->itemFilter($_item->getId());
            $listId = $collection->getFirstItem()->getListId();
        }
        
        return $listId;
    }
    
    protected function _exist($_item,$cartId=null,$listId=null)
    {
        if (!$cartId)
        {
            $cartId = $this->_getCartId();
        }
        $listId = $this->_getListIdFromItem($_item,$listId);
        $collection = $this->getCollection()
                ->productFilter((int) $_item->getProduct()->getId())
                ->cartFilter((int) $cartId)
                ->itemFilter((int) $_item->getItemId())
                ->listFilter((int) $listId);
   
        if (sizeof($collection)>0)
        {
            $this->_collection = $collection;
            return true;
        }
        return false;
    }
    
} } 