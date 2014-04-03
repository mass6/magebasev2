<?php

class Insync_Rule_Block_Adminhtml_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('ruleGrid');
      $this->setDefaultSort('rule_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  
	 // $collection = Mage::getModel('rule/rule')
            // ->getResourceCollection();
         // // $collection->addFieldToFilter('website_ids','1');
         // // $collection->addFieldToFilter('website_ids','2');
        // $this->setCollection($collection);

        // parent::_prepareCollection();
        // return $this;
  
      $collection = Mage::getModel('rule/rule')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('rule_id', array(
          'header'    => Mage::helper('rule')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'rule_id',
      ));

     
		
		$this->addColumn('name', array(
          'header'    => Mage::helper('rule')->__('Rule Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));
	  
	  $this->addColumn('is_active', array(
            'header'    => Mage::helper('rule')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));
		
		// Mage::log('Mage::getSingletoadminhtml/system_store)->getWebsiteOptionHash()==');
		// Mage::log(Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash());
		
		 if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_ids', array(
                'header'    => Mage::helper('rule')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }
	  
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('web')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      // $this->addColumn('status', array(
          // 'header'    => Mage::helper('web')->__('Status'),
          // 'align'     => 'left',
          // 'width'     => '80px',
          // 'index'     => 'status',
          // 'type'      => 'options',
          // 'options'   => array(
              // 1 => 'Enabled',
              // 2 => 'Disabled',
          // ),
      // ));
	  
	  // if (!Mage::app()->isSingleStoreMode()) {
           // $this->addColumn('website_ids', array(
                // 'header'    => Mage::helper('rule')->__('Website'),
                // 'align'     =>'left',
                // 'index'     => 'website_ids',
                // 'type'      => 'options',
                // 'sortable'  => false,
                // 'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                // 'width'     => 200,
            // ));
        // }
		
        // $this->addColumn('action',
            // array(
                // 'header'    =>  Mage::helper('rule')->__('Action'),
                // 'width'     => '100',
                // 'type'      => 'action',
                // 'getter'    => 'getId',
                // 'actions'   => array(
                    // array(
                        // 'caption'   => Mage::helper('rule')->__('Edit'),
                        // 'url'       => array('base'=> '*/*/edit'),
                        // 'field'     => 'id'
                    // )
                // ),
                // 'filter'    => false,
                // 'sortable'  => false,
                // 'index'     => 'stores',
                // 'is_system' => true,
        // ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('rule')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('rule')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rule_id');
        $this->getMassactionBlock()->setFormFieldName('rule');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('rule')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('rule')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('rule/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('rule')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('rule')->__('Status'),
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