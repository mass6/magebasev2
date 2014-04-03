<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List/Purchase/Collection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('690c3c0d07b3c3c9262fe9b4bee06936'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List_Purchase_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list_purchase');
    }
    
    public function productFilter($productId)
    {
        $this->getSelect()
                ->where("product_id = ?", $productId);
        return $this;
    }
    public function cartFilter($cartId)
    {
        $this->getSelect()
                ->where("cart_id = ?", $cartId);
        return $this;
    }
    public function listFilter($listId)
    {
        $this->getSelect()
                ->where("list_id = ?", $listId);
        return $this;
    }
    public function itemFilter($itemId)
    {
        $this->getSelect()
                ->where("item_id = ?", $itemId);
        return $this;
    }
    
    public function joinQuoteItem()
    {
        $this->getSelect()
        ->join(array('quote_item'=>$this->getTable('sales/quote_item')),"quote_item.item_id=main_table.item_id");
        return $this;
    }
   
} } 