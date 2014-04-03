<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Abstract.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('8fa46257c03d8f9a4cc9f9929eebbe3a'); ?><?php
class Aitoc_Aitproductslists_Model_List_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * @var Aitoc_Aitproductslists_Model_List
     */
    protected $_list;
    
    public function setList(Aitoc_Aitproductslists_Model_List $list)
    {
        $this->_list = $list;
        return $this;
    }
    
    public function getList()
    {
        return $this->_list;
    }

    protected function _beforeSave()
    {
        if(!$this->getListId() && $this->getList())
        {
            $this->setListId($this->getList()->getId());
        }
    }
    
    protected function _getCartId()
    {
        return Mage::getSingleton("checkout/cart")->getQuote()->getId();
    }
} } 