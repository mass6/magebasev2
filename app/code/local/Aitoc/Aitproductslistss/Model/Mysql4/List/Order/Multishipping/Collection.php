<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List/Order/Multishipping/Collection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('d7c5905cd8486879b53d5be2fdfbe789'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List_Order_Multishipping_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list_order_multishipping');
    }
    
    public function selectByQuote($quoteId)
    {
        $this->getSelect()
                ->where('quote_id=?',(int) $quoteId);
        return $this;
    }
} } 