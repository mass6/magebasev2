<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List/Order/Collection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('dacba0cd10135deb69a367d90449fd96'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list_order');
    }
    
    public function joinOrders()
    {
          $this->getSelect()
          ->join(array('order'=>$this->getTable('sales/order')),"order.entity_id=main_table.order_id"); 
          return $this; 
    }
    
    public function listFilter($listId)
    {
        $this->getSelect()
            ->where("main_table.list_id =?",$listId);
        return $this;
    }
   
} } 