<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Controller/Adminhtml/Abstract.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('05122c3560ad977e9ddde91859d9f610'); ?><?php
class Aitoc_Aitproductslists_Controller_Adminhtml_Abstract extends Mage_Adminhtml_Controller_Action
{
    protected function _initModel($model, $suffix, $idFieldName = 'id')
    {
        $id = (int) $this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel($model);

        if ($id) {
            $model->load($id);
        }

        Mage::register('current_'.$suffix, $model);
        return $this;
    }

    protected function _initCustomer($idFieldName = 'id')
    {
        return $this->_initModel('customer/customer', 'customer', $idFieldName);
    }
    
    protected function _initList($idFieldName = 'list_id')
    {
        return $this->_initModel('aitproductslists/list', 'list', $idFieldName);
    }

    public function saveItemsNotes($items)
    {
        $model = Mage::getModel('aitproductslists/list_item');
        foreach ($items as $key=>$value)
        {
            $model->load($key,'item_id')->setNotice($value['note'])->setItemId($key)->save();
        }
        return true;
    }
    
    /**
     * Retirve list ids from request (for massactions)
     * 
     * @return array()
     */
    protected function _getListIds()
    {
        return $this->getRequest()->getPost('list_ids', array());
    }
    
    protected function _getListId()
    {
        return $this->getRequest()->getParam('list_id');
    }
} } 