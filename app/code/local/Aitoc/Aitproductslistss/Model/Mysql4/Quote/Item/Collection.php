<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/Quote/Item/Collection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ amejBgrekphghUcr('b1ba93f5b2a04373865a50b77705fbfe'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_Quote_Item_Collection extends Mage_Sales_Model_Mysql4_Quote_Item_Collection
{
    protected function _construct()
    {
        $this->_init('aitproductslists/quote_item');
    }
      
    public function noParrent($quote)
    {
       $this->setQuote($quote);
       $this->getSelect()
            ->where('quote_id =?',$quote->getid())
            ->where('parent_item_id IS NULL');
        return $this;
    }
    
    public function getItemsToDisable($quote,$productId)
    {
        $this->setQuote($quote);
        $this->getSelect()
            ->joinInner(array('quote'=>$this->getTable('sales/quote')), 'quote.entity_id=main_table.quote_id',array())
            ->joinInner(array('list'=>$this->getTable('aitproductslists/list')), 'list.quote_id=quote.entity_id',array('id'))
            //->joinInner(array('customer'=>$this->getTable('customer/entity')), 'list.customer_id=customer.entity_id',array('*'))
            ->where("main_table.product_id=?",$productId)        
            ->where("list.product_change_notify_status=?",0);
        return $this;
    }
    
    public function addNote()
    {
        $this->getSelect()
            ->joinLeft(array('note'=>$this->getTable('aitproductslists/list_item')), 'note.item_id=main_table.item_id',array('*'))
        ;
        return $this;
    }
    public function loadByProductId($productId)
    {
        $this->getSelect()
                    ->where('product_id=?',$productId);
        return $this;
    }
} } 