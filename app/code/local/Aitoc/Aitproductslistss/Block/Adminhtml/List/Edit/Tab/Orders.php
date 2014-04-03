<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Orders.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('ae992f7c03b28ecd4c624e057b936044'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Orders extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aitppl_order_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * Retirve currently edited list model
     *
     * @return Aitoc_Aitproductslists_Model_List
     */
    protected function _getList()
    {
        return Mage::registry('current_list');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('aitproductslists/list_order')->getCollection()->joinOrders()->listFilter($this->_getList()->getId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('Order ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'increment_id'
        ));

        /*
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => 150,
            'index'     => 'sku'
        ));
*/
        $this->addColumn('subtotal', array(
            'header'        => Mage::helper('sales')->__('Subtotal'),
            'type'          => 'currency',
            'currency' => 'order_currency_code',
            'index'         => 'subtotal'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/orders', array('_current' => true));
    }
    
        public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array('order_id' =>$row->getEntityId()));
    }
} } 