<?php

class Insync_Web_Block_Adminhtml_Web_Edit_Tab_User2 extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  
	$country_list = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
	$temp = array();
	$temp[]= '------------Please select--------------';
	foreach($country_list as $country){ 
			// $temp[]= $country['Please Select'] ;
			$temp[$country['value']]= $country['label'] ;

}
  // Mage::log('$temp====');
  // Mage::log($temp);
  
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('web_form', array('legend'=>Mage::helper('web')->__('Second Contract Shipping Address')));
     
      $fieldset->addField('name_ship2', 'text', array(
          'label'     => Mage::helper('web')->__('Shipping Address Name'),
          // 'class'     => 'required-entry',
          // 'required'  => true,
          'name'      => 'name_ship2',
      ));
	  $fieldset->addField('street_ship2', 'text', array(
          'label'     => Mage::helper('web')->__('Street Address'),
          // 'class'     => 'required-entry',
          // 'required'  => true,
          'name'      => 'street_ship2',
      ));
	   $fieldset->addField('street1_ship2', 'text', array(
          'label'     => Mage::helper('web')->__(''),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'street1_ship2',
      ));
	  
	
      $fieldset->addField('city_ship2', 'text', array(
          'label'     => Mage::helper('web')->__('City'),
          // 'class'     => 'required-entry',
          // 'required'  => true,
          'name'      => 'city_ship2',
      ));
	  $fieldset->addField('country_ship2', 'select', array(
          'label'     => Mage::helper('web')->__('Country'),
         // 'class'     => 'required-entry',
          // 'required'  => true,
          'name'      => 'country_ship2',
		   'options'    => $temp ,
      ));
	  $fieldset->addField('state_ship2', 'text', array(
          'label'     => Mage::helper('web')->__('State/Province'),
         // 'class'     => 'required-entry',
          // 'required'  => true,
          'name'      => 'state_ship2',
      ));
	  $fieldset->addField('zip_ship2', 'text', array(
          'label'     => Mage::helper('web')->__('Zip/Postal Code'),
          // 'class'     => 'required-entry',
          // 'required'  => true,
          'name'      => 'zip_ship2',
      ));
	  // $fieldset->addField('contract_shipping5', 'text', array(
          // 'label'     => Mage::helper('web')->__('Contract Shipping Address5'),
         // // 'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'contract_shipping5',
      // ));
     
      if ( Mage::getSingleton('adminhtml/session')->getWebData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getWebData());
          Mage::getSingleton('adminhtml/session')->setWebData(null);
      } elseif ( Mage::registry('web_data') ) {
          $form->setValues(Mage::registry('web_data')->getData());
      }
      return parent::_prepareForm();
  }
}