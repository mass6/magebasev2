<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Item.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('4891073e245949436d07a90f4b03bd01'); ?><?php
class Aitoc_Aitproductslists_Model_List_Item extends Aitoc_Aitproductslists_Model_List_Abstract
{
    protected function _construct()
    {
        $this->_init('aitproductslists/list_item');
    }
    
    private function ifExists($itemId)
    {
        $item = $this->load($itemId,"item_id");
        $result = $item->getId();
        return $result;
    }


    public function add($observer)
    {
        $data = array();  
        $itemData = Mage::app()->getRequest()->getParam('item'); 
        $item = $observer->getDataObject();
       //   return ;
        $itemId = $item->getItemId();
        if( $this->ifExists($itemId))
        {
            return ;
        } 
        $note = isset($itemData['notes']) ? $itemData['notes'] : "";
        $data = array('item_id'=>$itemId,
                      'notice' =>$note
                      );
        $this->setData($data)->save();
    }
    
    public function addFromController($itemId)
    {
        if (!$itemId && $itemId==0)
        {
            return ;
        }
        if( $this->ifExists($itemId))
        {
            return ;
        }
        $data = array();
        $id = null;
        if ($existsItem = $this->load($itemId,'item_id'))
        {
            $id = $existsItem->getNoteId();
        }
        $data = array('item_id'=>$itemId,
                      'notice' =>''
                      );
        $this->setData($data)->setId($id)->save();
    }
} } 