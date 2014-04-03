<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Rewrite/CatalogProduct.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('d1cb1fe824073052a0884f739dfae368'); ?><?php
class Aitoc_Aitproductslists_Model_Rewrite_CatalogProduct extends Mage_Catalog_Model_Product
{

    protected function _afterSave()
    {
        if ($this->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
        {
            Mage::getModel('aitproductslists/list_notifier')->changeProductStatus($this);
        }
        return parent::_afterSave();
    }

} } 