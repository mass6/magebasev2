<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Options/List.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('f9bb674a97883280373425b4ecbebddb'); ?><?php
class Aitoc_Aitproductslists_Model_Options_List
{
    protected $_list;
    
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value' => 0, 'label' => Mage::helper('adminhtml')->__('-- Please select --'));
        
        $collection = $this->getList()->getCollection();
        if($this->getList()->getId())
        {
            $collection->addFieldToFilter('id', array('neq' => $this->getList()->getId()));
        }
        if($this->getList()->getCustomerId())
        {
            $collection->addFieldToFilter('customer_id', array('eq' => $this->getList()->getCustomerId()));
        }
        
        foreach ($collection->getItems() as $item)
        {
            $options[] = array('value' => $item->getId(), 'label' => $item->getName());
        }
        
        return $options;
    }
    
    public function setList(Aitoc_Aitproductslists_Model_List $list)
    {
        $this->_list = $list;
        return $this;
    }
    
    public function getList()
    {
        if(!$this->_list)
        {
            $this->_list = Mage::getModel('aitproductslists/list');
        }
        return $this->_list;
    }
} } 