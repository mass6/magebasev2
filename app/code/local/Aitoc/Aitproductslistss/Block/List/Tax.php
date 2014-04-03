<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/List/Tax.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('2efcd64f97e3f54e6069baecbea3de92'); ?><?php
class Aitoc_Aitproductslists_Block_List_Tax extends Mage_Tax_Block_Sales_Order_Tax
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order   = $parent->getList();
        $this->_source  = $parent->getSource();

        $store = $this->getStore();
        $allowTax = ($this->_source->getTaxAmount() > 0) || ($this->_config->displaySalesZeroTax($store));
        $grandTotal = (float) $this->_source->getGrandTotal();
        if (!$grandTotal || ($allowTax && !$this->_config->displaySalesTaxWithGrandTotal($store))) {
            $this->_addTax();
        }

        $this->_initSubtotal();
        $this->_initShipping();
        $this->_initDiscount();
        $this->_initGrandTotal();
        return $this;
    }
} } 