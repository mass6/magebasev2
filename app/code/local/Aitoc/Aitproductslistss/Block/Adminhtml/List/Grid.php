<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Grid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('88b3468a465dbec02ab5b6d2936797cf'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aitlists_grid');
        $this->setDefaultSort('create_date', 'desc');
        $this->setUseAjax(true);
        $this->setMessageBlockVisibility();
     //   $this->setMassactionBlockName('aitproductslists/adminhtml_customer_edit_tab_list_grid_massaction');
    }

//    protected function _prepareLayout()
//    {
//        return parent::_prepareLayout();
//    }
//    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getResourceModel('aitproductslists/list_collection')
            ->joinCustomer()
            ->joinQuote()
            ->addStoreToGrid()   
            ;
 //       echo $collection->getSelect()->__toString();
        if ($store->getId())
        {
            $collection->addStoreFilter($store);
        }
   //         ->addFieldToFilter('main_table.customer_id', $this->getCustomerId())
        ;
//echo "<pre>"; print_r($collection->getData()); exit;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('aitproductslists')->__('List #'),
            'width'     => '10',
            'index'     => 'id',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('aitproductslists')->__('List Name'),
            'index'     => 'name',
        ));
        
        $this->addColumn('customer_firstname', array(
            'header'    => Mage::helper('aitproductslists')->__('Customer First Name'),
            'index'     => 'customer_firstname',
      //      'filter_index' => 'customer.customer_firstname',
        ));
        
        $this->addColumn('customer_lastname', array(
            'header'    => Mage::helper('aitproductslists')->__('Customer Last Name'),
            'index'     => 'customer_lastname',
      //      'filter_index' => 'customer.customer_lastname',
        ));
        

//        $this->addColumn('full_name', array(
//            'header'    => Mage::helper('aitproductslists')->__('Customer Name'),
//            'index'     => 'full_name',
//            'filter_index' => 'quote.full_name',
//        ));
        

        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('aitproductslists')->__('Customer Email'),
            'index'     => 'customer_email',
     //       'filter_index' => 'customer.customer_email',
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
        
      
        $this->addColumn('discount_list_status', array(
            'header'    => Mage::helper('aitproductslists')->__('Discount Status'),
            'index'     => 'discount_list_status',
            'type'      => 'options',
            'width'     => '50',
      //      'filter_index' => 'main_table.discount_list_status',
            'options'   => array(
                        Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_NO => 'No discount',
                        Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_WAITING => 'Waiting approve',
                        Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_DECLINE => 'Decline',
                        Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_APPROVE => 'Approve',
                ),
        ));
        
        $this->addColumn('store_id', array(
            'header'    => Mage::helper('aitproductslists')->__('Store'),
            'index'     => 'store_id',
            'filter_index' => 'quote.store_id',
            'type'      => 'store',
            'width'     => '120',
            'store_view'=> true,
            'display_deleted' => false,
        ));

        $this->addColumn('subtotal', array(
            'header'    => Mage::helper('aitproductslists')->__('List Total'),
            'index'     => 'subtotal',
            'type'      => 'currency',
            'currency'  => 'quote_currency_code',
        ));
          
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('list_ids');
 //       $this->getMassactionBlock()->setUseAjax('true');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDeleteGrid', array('_current' => true)),
             'confirm' => Mage::helper('catalog')->__('Are you sure?'),
        ));
//        $this->getMassactionBlock()->addItem('merge', array(
//             'label'=> Mage::helper('aitproductslists')->__('Merge'),
//             'url'  => $this->getUrl('*/*/massMerge', array('_current' => true, '_query' => array('ajax' => true))),
//             'confirm' => Mage::helper('catalog')->__('Are you sure?'),
//             'complete' => 'aitpplGridUpdater'
//        ));

//        $this->getMassactionBlock()->addItem('order', array(
//             'label'=> Mage::helper('sales')->__('Order'),
//             'url'  => $this->getUrl('*/*/massOrder', array('_current' => true, '_query' => array('ajax' => true))),
//             'complete'  => 'aitpplRedirect', 
//             
//        ));
        
        return $this;
    }
    
    public function getRowUrl($row)
    {
     //   echo "<pre>"; print_r($row->getData()); exit;
        return $this->getUrl('*/*/edit', array('list_id' => $row->getId(), 'customer_id' => $row->getData('customer_id')));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
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
   
} } 