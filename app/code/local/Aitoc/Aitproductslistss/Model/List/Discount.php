<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Discount.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ yBZaDjmZMwojoQTP('b3a9351bf262add2eedba4854be0a885'); ?><?php
class Aitoc_Aitproductslists_Model_List_Discount extends Aitoc_Aitproductslists_Model_List_Abstract
{
    protected $_canDiscountList;
    
    protected function _construct()
    {
        $this->_init('aitproductslists/list_discount');
        
        $collection = Mage::getModel('aitproductslists/list_purchase')->getCollection()->cartFilter($this->_getCartId());
        foreach ($collection as $item)
        {
            if (!isset($this->_itemsCount[$item->getListId()]))
            {
                $this->_itemsCount[$item->getListId()] = 0;
            }
            $this->_itemsCount[$item->getListId()] += 1; 
        }
        
    }
    
    
    protected function _beforeSave()
    {
        $approve = false;
       
        if ($this->getPrice()>0 AND $this->getIsApproved()!==1)
        {
            $approve = true;
        }
        if ($this->getStatus(1) AND $this->getIsApproved()!==1)
        {
            $approve = true;
        }
        
        if ($approve AND $this->canDiscount())
        {
            $this->setData('is_approved',1);
            $this->getList()->setDiscountListStatus(Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_APPROVE)->save();
        }
        return parent::_beforeSave();
    }
    
    public function canDiscount()
    {
        if (!$this->getPrice())
        {
            return false;
        }
        if ($this->getPrice()==0)
        {
            return false;
        }
        if ( $this->getList()->getPayQty() < $this->getMinQty())
        {
            return false;
        }
        if ($this->getToDate())
        {
            $dateFrom = Mage::getModel('core/date')->timestamp($this->getFromDate());
            $dateTo = Mage::getModel('core/date')->timestamp($this->getToDate());
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
    
    public function onSalesruleValidatorProcess($observer)
    {                                                                                                             //
        $rule   = $observer->getEvent()->getRule();
        $item   = $observer->getEvent()->getItem();
        if ($rule->getName() != 'All products list rule') // rule with such name must be created (with empty actions and conditions)
        {
            return;
        }
        $rule = $this->_getRule($item);
        
        // discount not exists
        if (!$rule)
        {
            return $this->_maybeCart($observer);
        }
        
        // this is discount
        $this->_applyDiscount($rule,$observer); 
        
    }
    
    protected function _getItem($item)
    {
        if ($item->getQuoteItem())
        {
            $item = $item->getQuoteItem();
        }
        $model = Mage::getModel('aitproductslists/list_purchase');
        $collection = $model->getCollection()->cartFilter($this->_getCartId())->itemFilter($item->getId());
        $purchaseItem = $collection->getFirstItem();
        $item->setData('list_id',$purchaseItem->getListId());
        if ($purchaseItem->getQtyInList()>=$purchaseItem->getQtyInCart())
        {
            $item->setData('purchase_qty',$purchaseItem->getQtyInCart());
        }
        else
        {
            $item->setData('purchase_qty',$purchaseItem->getQtyInList());
        }
       // echo "<pre>"; print_r($item->getPurchaseQty()); exit;
        return $item;
    }
    
    protected function _applyDiscount($rule,$observer)
    {
        if (!$rule->getId())
        {
            return true;
        }
        
        
        $item = null;
        $canDiscount = false;
        $item = $observer->getEvent()->getItem();
        $item = $this->_getItem($item);  
        if ($item->isDeleted() || $item->getParentItemId() || $item->getProduct()->getVisibility()=="1") {
            return true;
        }
        $result = $observer->getEvent()->getResult(); 
        $quote  = $item->getQuote();
        $address = $observer->getEvent()->getAddress();
        $baseDiscountAmount = 0;
        $discountAmount     = 0;
        $discountPercent    = 0;
        
        $discountPercent = $rule->getPrice();
        $discount = (int) $discountPercent/100;
          
        $basePrice = $item->getBasePrice();
        $price = $item->getPrice();

        if ($rule->getToDate())
        {
            $dateFrom = Mage::getModel('core/date')->timestamp($rule->getFromDate());
            $dateTo = Mage::getModel('core/date')->timestamp($rule->getToDate());
            $dateNow = Mage::getModel('core/date')->timestamp(time());
            if ($dateFrom <= $dateNow AND $dateNow <= $dateTo)
            {
                $canDiscount = true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $canDiscount = true;
        }
        $canDiscount = $this->_canDiscountList($item);
        $itemQty = $item->getPurchaseQty();
        $s1 = $item->getBaseDiscountAmount()*$itemQty;
        $s2 = $basePrice * $discount * $itemQty;
        $baseDiscountAmount = (float) $quote->getStore()->roundPrice($s1 + $s2);
        $discountAmount     = (float) $quote->getStore()->roundPrice($item->getDiscountAmount()*$itemQty + $price * $discount * $itemQty);
        $discountPercent    = $item->getDiscountPercent() + $discount;
      
        if (!$canDiscount)
        {
            return true;
        }
        
        $discount = (float) $item->getRowTotal() - $discountAmount;
        $subtotal = (float) $address->getSubtotal();// - $discountAmount;
        
        /**
         * if multishipping checkout - discount were applied. return default values
         */
        if(Mage::app()->getRequest()->getControllerName() == "multishipping")
        {
            $result->setDiscountAmount($quote->getStore()->roundPrice($item->getDiscountAmount()));
            $result->setBaseDiscountAmount($quote->getStore()->roundPrice($item->getBaseDiscountAmount()));
//
            $address->setBaseSubtotal($subtotal); 
            $address->setSubtotal($subtotal);

            $item->setBaseSubtotal($item->getRowTotal()); 
            $item->setSubtotal($item->getRowTotal());
            return true;
        }
        
        
        $result->setDiscountAmount($discountAmount);
        $result->setBaseDiscountAmount($discountAmount);
        
        $address->setBaseSubtotal($subtotal); 
        $address->setSubtotal($subtotal);
   
        $item->setBaseSubtotal($discount); 
        $item->setSubtotal($discount);
    }
    
    protected function _canDiscountList($_item)
    {
      //  var_dump($this->_canDiscountList); exit;
        if ($this->_canDiscountList !== null)
        {
            return $this->_canDiscountList;
        }
      //  echo "<pre>"; print_r($this->_itemsCount); echo "</pre>"; 
        $list = Mage::getModel('aitproductslists/list')->load($_item->getListId());
        if (!$list->checkDiscount())
        {
            $this->_canDiscountList = false;
            return false;
        }
        
       // echo "<pre>"; print_r($list->getQuote()->getSubtotal()); echo "</pre>"; 
    
        $collection = Mage::getModel('aitproductslists/list_purchase')->getCollection()->joinQuoteItem()->cartFilter($this->_getCartId());
        $listAmountInCart = 0;
        $listAmountPercent = Mage::getStoreConfig("aitproductslists/discount/percent", Mage::app()->getStore()->getId())/100;
        foreach ($collection as $row)
        {
            $listAmountInCart += $row->getRowTotal();
        }
        if ($listAmountInCart<$list->getQuote()->getSubtotal()*$listAmountPercent)
        {
            $this->_canDiscountList = false;
            return false;
        }
        $this->_canDiscountList = true;
        return true;
     //   print_r($this->_getList($_item)->getData()); exit;
        //$this->getList()->getPayQty() < $this->getMinQty()
    }


    protected function _maybeCart($observer)
    {
        $rule   = $observer->getEvent()->getRule();
        $item   = $observer->getEvent()->getItem();
        if ($rule->getName() != 'All products list rule') // rule with such name must be created (with empty actions and conditions)
        {
            return;
        }
        $purchase = Mage::getModel('aitproductslists/list_purchase')->load($item->getId(),'item_id');
        if (!$purchase)
        {
            return false;
        }
        $list = Mage::getModel('aitproductslists/list')->load($purchase->getListId());
        $rule = $list->getDiscount();
        if (!$rule)
        {
            return false;
        }
        $listItems = Mage::getModel('aitproductslists/list')->load($rule->getListId())->getQuote()->getAllVisibleItems();
        
        if ( in_array(Mage::app()->getRequest()->getModuleName(), array('checkout','aitcheckout')))
        {
            foreach ($listItems as $listItem)
            {
                
                if($item->representProduct($listItem->getProduct()))
                {
                    return $this->_applyDiscount($rule,$observer); 
                }
                
            }
            return false;
        }     
        
    }


    protected function _getRule($item)
    {
        $list = $this->_getList($item);
        if (!$list)
        {
            return false;
        }
        if (!$list->getDiscount())
        {
            return false;
        }
        return $list->getDiscount();
    }
    
    protected function _getList($item)
    {
        $quoteId = $item->getQuote()->getId();
        if ($item->getQuoteItem())
        {
            $item = $item->getQuoteItem();
        }
       // echo "<pre>"; print_r($item->getId()); exit;
        $purchase = Mage::getModel('aitproductslists/list_purchase');
        $purchaseItem = $purchase->getCollection()
                ->cartFilter($quoteId)->itemFilter($item->getId())->getFirstItem();

        $list = Mage::getModel('aitproductslists/list')->load($purchaseItem->getListId());
        if ($list->getId())
        {
            return $list;
        }
        // if in cart
        $purchaseItem = $purchase->load($item->getId(),'item_id');
        if ($purchaseItem->getId())
        {
            $list = Mage::getModel('aitproductslists/list')->load($purchaseItem->getListId());
            return $list;
        }
        return false;
    }  
} } 