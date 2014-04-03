<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List/Discount.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('36c4c5da516bf0bc6843b882f0e8eb99'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List_Discount extends Aitoc_Aitproductslists_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list_discount', 'id');
        
        $this->_dateFields = array(
            'from_date',
            'to_date'
        );
    }
} } 