<?php

class Insync_Supplier_Block_Adminhtml_Supplier_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

	public function websitelist(){
		$collection = Mage::getModel('core/website')->getCollection();
		$custId = array();
		$custName = array();
		foreach ($collection as $website) {
			$custId[] = $website->getWebsiteId();
			$custName[] = $website->getName();
			

		}
		unset($collection);
		$namea[0] = '';
		for ($i = 0; $i < count($custId); $i++) {
			$namea[$custId[$i]] = $custName[$i];
		}
		return $namea;
	}
	
	
	public function order(){
		$collection = Mage::getModel("sales/order")->getCollection()
                ->addAttributeToSelect('*');
		foreach($collection as $eachCollection){
		}

	}
	
	
	

  protected function _prepareForm()
  {
		$webste = $this->websitelist();
	  $url = Mage::helper('core/url')->getCurrentUrl();
	  $temp = explode('key/',$url);
	  $temp1 = explode('id/',$temp[0]);
		$st=str_replace('/','',$temp1[1]);
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('supplier_form', array('legend'=>Mage::helper('supplier')->__('Supplier Information')));
	   

		
		$fieldset->addField('name', 'text', array(
				  'label'     => Mage::helper('supplier')->__('Supplier Code'),
				  'required'  => true,
				  'name'      => 'name',
			  ));	
			  
		$fieldset->addField('sup_name', 'text', array(
				  'label'     => Mage::helper('supplier')->__('Supplier Name'),
				  'required'  => true,
				  'name'      => 'sup_name',
			  ));	
		$fieldset->addField('sup_email', 'text', array(
				  'label'     => Mage::helper('supplier')->__('Email'),
				  'required'  => true,
				  'name'      => 'sup_email',
			  ));	
		$fieldset->addField('sup_tel', 'text', array(
				  'label'     => Mage::helper('supplier')->__('Telephone Number'),
				  'required'  => true,
				  'name'      => 'sup_tel',
			  ));	
	   
      if ( Mage::getSingleton('adminhtml/session')->getWebData() )
	 
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSupplierData());
          Mage::getSingleton('adminhtml/session')->setSupplierData(null);
      } elseif ( Mage::registry('supplier_data') ) {
          $form->setValues(Mage::registry('supplier_data')->getData());
      }
	  
      return parent::_prepareForm();
  }
}