<?php

class Insync_Supplier_Block_Adminhtml_Supplier_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('supplierGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('supplier/supplier')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('supplier')->__('Supplier ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('supplier')->__('Supplier Code'),
          'align'     =>'left',
          'index'     => 'name',
      ));
		
		$this->addColumn('sup_name', array(
          'header'    => Mage::helper('supplier')->__('Supplier Name'),
          'align'     =>'left',
          'index'     => 'sup_name',
      ));
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('web')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */
		
		$this->addExportType('*/*/exportCsv', Mage::helper('supplier')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('supplier')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('supplier');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('supplier')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('supplier')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('supplier/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('supplier')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('supplier')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}