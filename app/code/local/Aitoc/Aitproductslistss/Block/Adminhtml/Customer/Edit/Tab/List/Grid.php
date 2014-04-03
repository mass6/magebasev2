<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/Customer/Edit/Tab/List/Grid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('5bd09e3a192f7db5be51e4177a94ef23'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_Customer_Edit_Tab_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_aitlists_grid');
        $this->setDefaultSort('create_date', 'desc');
        $this->setUseAjax(true);
        $this->setMessageBlockVisibility();
        $this->setMassactionBlockName('aitproductslists/adminhtml_customer_edit_tab_list_grid_massaction');
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('add_list_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('aitproductslists')->__('Add New List'),
                    'onclick'   => 'setLocation(\'' . $this->getCreateListUrl() . '\')',
                    'class'   => 'add'
                ))
        );
    
        return parent::_prepareLayout();
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('aitproductslists/list_collection')
            ->joinQuote()
            ->addFieldToFilter('main_table.customer_id', $this->getCustomerId())
        ;

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('aitproductslists')->__('List #'),
            'width'     => '20',
            'index'     => 'id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('aitproductslists')->__('List Name'),
            'index'     => 'name',
        ));
        
        $this->addColumn('create_date', array(
            'header'    => Mage::helper('aitproductslists')->__('Created At'),
            'index'     => 'create_date',
            'type'      => 'datetime',
            'width'     => '150'
        ));
        
        $this->addColumn('pay_qty', array(
            'header'    => Mage::helper('aitproductslists')->__('Purchase Count'),
            'index'     => 'pay_qty',
            'type'      => 'number',
        ));
        
        $this->addColumn('items_qty', array(
            'header'    => Mage::helper('aitproductslists')->__('Item Qty'),
            'index'     => 'items_qty',
            'type'      => 'number',
        ));
        
        $this->addColumn('subtotal', array(
            'header'    => Mage::helper('aitproductslists')->__('List Total'),
            'index'     => 'subtotal',
            'type'      => 'currency',
            'currency'  => 'quote_currency_code',
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit'
                        ),
                        'field'   => 'list_id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'id',
        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('list_ids');
        $this->getMassactionBlock()->setUseAjax('true');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete', array('_current' => true, '_query' => array('ajax' => true))),
             'confirm' => Mage::helper('catalog')->__('Are you sure?'),
             'complete' => 'aitpplGridUpdater'
        ));

//        $this->getMassactionBlock()->addItem('merge', array(
//             'label'=> Mage::helper('aitproductslists')->__('Merge'),
//             'url'  => $this->getUrl('*/*/massMerge', array('_current' => true, '_query' => array('ajax' => true))),
//             'confirm' => Mage::helper('catalog')->__('Are you sure?'),
//             'complete' => 'aitpplGridUpdater'
//        ));

        $this->getMassactionBlock()->addItem('order', array(
             'label'=> Mage::helper('sales')->__('Order'),
             'url'  => $this->getUrl('*/*/massOrder', array('_current' => true, '_query' => array('ajax' => true))),
             'complete'  => 'aitpplRedirect', 
             
        ));
        
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('list_id' => $row->getId(), 'customer_id' => $row->getCustomerId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/lists', array('_current' => true));
    }
    
    public function getMainButtonsHtml()
    {
        $html = $this->getAddListButtonHtml();
        
        return $html . parent::getMainButtonsHtml();
    }
    
    public function getAddListButtonHtml()
    {
        return $this->getChildHtml('add_list_button');
    }
    
    public function getCreateListUrl()
    {
        return $this->getUrl('*/*/add', array('customer_id' => $this->getCustomerId()));
    }
    
    public function getCustomerId()
    {
        return Mage::registry('current_customer')->getId();
    }
} } 