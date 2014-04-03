<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Share/Preview.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('1f203472e4014b07a5a9bf2ba0fdd916'); ?><?php
class Aitoc_Aitproductslists_Block_Share_Preview extends Aitoc_Aitproductslists_Block_Share_View
{
    public function getList()
    {
        return Mage::getModel('aitproductslists/list')->load($this->getRequest()->getParam('list_id'));
    }
} } 