<?php

class Insync_Web_Block_Adminhtml_Web_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('web_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('web')->__('Contract Information'));
  }

  protected function _beforeToHtml()
  {
	
	  
      $this->addTab('form_section', array(
          'label'     => Mage::helper('web')->__('Contract Information'),
          'title'     => Mage::helper('web')->__('Contract Information'),
          'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_form')->toHtml(),
		  
      ));
	  
	  // $this->addTab('form_section', array(
                // 'label'     => Mage::helper('web')->__('Contract Information'),
                // 'url'       => $this->getUrl('*/*/web', array('_current' => true)),
                // 'class'     => 'ajax',
        // ));
	  
	 $this->addTab('form_section1', array(
            'label'     => Mage::helper('web')->__('Contract User'),
            'title'     => Mage::helper('web')->__('Contract User'),
            'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_form2')->toHtml(),
            'active'	=> false
        ));
		
	   $this->addTab('form_section2', array(
				'label'     => Mage::helper('web')->__('Contract Billing Address'),
				'title'     => Mage::helper('web')->__('Contract Billing Address'),
				'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_user')->toHtml(),
				'active'    => false
			));
			
		 $this->addTab('form_section3', array(
				'label'     => Mage::helper('web')->__('Contract Shipping Address - 1'),
				'title'     => Mage::helper('web')->__('First Contract Shipping Address'),
				'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_user1')->toHtml(),
				'active'    => false
			));
			
		 $this->addTab('form_section4', array(
				'label'     => Mage::helper('web')->__('Contract Shipping Address - 2'),
				'title'     => Mage::helper('web')->__('Second Contract Shipping Address'),
				'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_user2')->toHtml(),
				'active'    => false
			));
		 $this->addTab('form_section5', array(
				'label'     => Mage::helper('web')->__('Contract Shipping Address - 3'),
				'title'     => Mage::helper('web')->__('Third Contract Shipping Address'),
				'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_user3')->toHtml(),
				'active'    => false
			));
		 $this->addTab('form_section6', array(
				'label'     => Mage::helper('web')->__('Contract Shipping Address - 4'),
				'title'     => Mage::helper('web')->__('Fourth Contract Shipping Address'),
				'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_user4')->toHtml(),
				'active'    => false
			));
		 $this->addTab('form_section7', array(
				'label'     => Mage::helper('web')->__('Contract Shipping Address - 5'),
				'title'     => Mage::helper('web')->__('Fifth Contract Shipping Address'),
				'content'   => $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_user5')->toHtml(),
				'active'    => false
			));

      return parent::_beforeToHtml();
  }
}
