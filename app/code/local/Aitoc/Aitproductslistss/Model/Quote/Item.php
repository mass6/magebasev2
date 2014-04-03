<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Quote/Item.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('a722be7ea9c124a2161667f4e95ee5e1'); ?><?php
class Aitoc_Aitproductslists_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
     /**
     * Array of errors associated with this quote item
     *
     * @var Mage_Sales_Model_Status_List
     */
    protected $_errorInfos = null;
   
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'aitproductslists_quote_item';
    
    protected function _construct()
    {
        $this->_init('aitproductslists/quote_item');
        if(Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion('>=1.6'))
        {
            $this->_errorInfos = Mage::getModel('sales/status_list');
        }
    }
} } 