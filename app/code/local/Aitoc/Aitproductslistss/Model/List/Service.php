<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Service.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('f9a2301518a2e42dcd247af9857778c8'); ?><?php
class Aitoc_Aitproductslists_Model_List_Service extends Mage_Core_Model_Abstract
{
    private function _getList()
    {
        return Mage::getModel('aitproductslists/list');
    }
    public function merge($params,$type)
    {
        switch ($type)
        {
            case Aitoc_Aitproductslists_Model_List::AITPPL_MERGE_OUT :
                $fromList = $this->_getList()->load($params['list_id']);
                $toList = $this->_getList()->load($params['merge_list']);
                break;
            case Aitoc_Aitproductslists_Model_List::AITPPL_MERGE_IN :
                $fromList = $this->_getList()->load($params['merge_list']);
                $toList = $this->_getList()->load($params['list_id']);
                break;
            case Aitoc_Aitproductslists_Model_List::AITPPL_MERGE_NEW :
               $fromList = $this->_getList()->load($params['list_id']);
               $fromList->clearPrivateData()->save();  
               $toList = $fromList->duplicate()->save();
               $toList = $this->_getList()->load($toList->getId());  
               $fromList = $this->_getList()->load($params['merge_list']);
        //        
               break;
        }
        if ($params['merge_list_delete'])
        {
            $this->_getList()->load($params['list_id'])->delete();
        }
        /**
         * clear private data
         */
         $toList->clearPrivateData()->save();
         $fromList->clearPrivateData()->save();
         
         $fromQuote = $fromList->getQuote();
         $toQuote = $toList->getQuote();
         $toQuote = $this->_merge($fromQuote, $toQuote);
         $toQuote->collectTotals()->save();
//         if (Mage::getStoreConfig('aitproductslists/customer/reset'))
//         {
             $toList->clearPrivateData()->save();
//         }
    }
    
    protected function _merge($fromQuote,$toQuote)
    {
         $toQuote->getBillingAddress();
         $toQuote->getShippingAddress()->setCollectShippingRates(true);
//         $parentItems = array();
//         $parentItems = $fromQuote->getAllVisibleItems();
//         foreach ($parentItems as $item)
//        {
//            Mage::helper('aitproductslists')->prepareItemRequest($item->getId());
//            $product = Mage::helper('aitproductslists')->initProduct();
//            $request = Mage::helper('aitproductslists')->getProductRequest(Mage::app()->getRequest()->getParams());
//            $newItem = $toQuote->addProduct($product, $request);
//            Mage::getModel('aitproductslists/list_item')->addFromController($newItem->getId());
//        }
         $toQuote->merge($fromQuote);  
         $toQuote->collectTotals()->save();
         foreach ($toQuote->getAllItems() as $_item)
         {
             Mage::getModel('aitproductslists/list_item')->addFromController($_item->getId());
         }
         return $toQuote;
         
    }
    public function saveFrom($params,$parent)
    {
       // echo "<pre>"; print_r($params); exit;
        $qtyFlag = null;
        $list = $this->_getList()->setData($params['list'])->save();
        $toQuote = $list->getQuote();
        $toQuote->getBillingAddress();
        $toQuote->getShippingAddress()->setCollectShippingRates(true);
        switch ($parent)
        {
            case "cart":

                $qtyFlag = null;
                $fromQuote = Mage::getSingleton('checkout/cart')->getQuote();
                $toQuote->collectTotals()->save();
                $parentItems = array();
                $parentItems = $fromQuote->getAllVisibleItems();
                
                foreach($parentItems as $item)
                {
                    $quote = Mage::getModel('aitproductslists/quote')->load($list->getQuote()->getId()); // this fix to bundle and cinf products (2 products with different options)
                    $product = $item->getProduct();
                    $product->setCustomOptions($item->getProduct()->getCustomOptions());
                    $info = $item->getOptionByCode('info_buyRequest');
                    $info = new Varien_Object(unserialize($info['value']));
                    $info->setQty($item->getQty());
            //        echo "<pre>"; print_r($info->getData()); 
                    $newList = $quote->addProduct($product,$info);
                    $quote->collectTotals()->save();
                    foreach ($quote->getAllItems() as $_item)
                    {
                         Mage::getModel('aitproductslists/list_item')->addFromController($_item->getId());
                    }
               }
               
           //    $list->getQuote()->collectTotals()->save();
               
        //       exit;
                break;
            case "order":
                $order = Mage::getModel('sales/order')->load($params['order_id']);
                
                $toQuote->save();
                $orderItems = $order->getItemsCollection();
                
                foreach($orderItems as $orderItem)
                {
                    
                   //     $this->addOrderItem($orderItem,NULL,$list);
                    try {
                       $this->addOrderItem($orderItem,NULL,$list);
                    } catch (Mage_Core_Exception $e){
                        echo 1;
                            Mage::getSingleton('aitproductslists/session')->addError($e->getMessage());
            //            $this->_redirect('*/*/history');
                    } catch (Exception $e) {
                        echo 3;
                        Mage::getSingleton('aitproductslists/session')->addException($e,
                        Mage::helper('aitproductslists')->__('Cannot add the item to shopping cart.')
                        );
                        $this->_redirect('aitproductslists/list/view');
                    }
                }
            //    exit;
                $list->getQuote()->collectTotals()->save();
                foreach ($list->getQuote()->getAllItems() as $item)
                {
                    Mage::getModel('aitproductslists/list_item')->addFromController($item->getId());
                }
                break;
        }
       return $list;
    }
    
    public function addOrderItem($orderItem, $qtyFlag=null, $list)
    {
        /* @var $orderItem Mage_Sales_Model_Order_Item */
        if (is_null($orderItem->getParentItem())) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($orderItem->getProductId());
            if (!$product->getId()) {
                 $product = $orderItem->getProduct();
            }
            if (!$product->getId()) {
                 return $this;
            }
 
            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new Varien_Object($info);
            if (is_null($qtyFlag)) {
                $info->setQty($orderItem->getQtyOrdered());
            } else {
                $info->setQty(1);
            }
            try {
               $result = $list->getQuote()->addProduct($product,$info);
            } catch(Mage_Core_Exception $e)
            {
                 $result = $e->getMessage();
            }
             if (is_string($result)) {
                Mage::throwException($result);
            }
        }
        return $this;
    }
    
    public function saveItemNotes()
    {
        $items = array();
        if (Mage::app()->getRequest()->getParam('item'))
        {
            $items = Mage::app()->getRequest()->getParam('item');
        }
        $oItem = Mage::getModel('aitproductslists/list_item');
        
        if (count($items)<1)
        {
            return ;
        }
        
        foreach ($items as $itemId=>$value)
        {
            $item = $oItem->load($itemId,"item_id");
            if (isset($value['note']))
            {
                $item->setNotice($value['note'])->save();
            }
        }
    }
   
} } 