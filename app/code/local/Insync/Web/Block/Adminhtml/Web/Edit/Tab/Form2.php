<?php

class Insync_Web_Block_Adminhtml_Web_Edit_Tab_Form2 extends Mage_Adminhtml_Block_Widget_Form
{

// @$cat=$_GET['cat'];
// Mage::log('carrrr');
// Mage::log($cat);



	public function glo()
	{
	 global $ids;
	 $customer=Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
	$custId = array();
	$finalCustName = array();
		$custName = array();
		$custName[0]= ' ';
		foreach ($customer as $cust) {
		
		$gid=$cust->getData('group_id');
		$collection = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_id',$gid);
		$gropu='';
		$result = array();
		foreach ($collection as $group) {
			
			$gropu=$group->getData('customer_group_code');
			
			}

			unset($collection);
		
			
			$custName[$cust->getId()] = $gropu.' - '.$cust->getData('firstname').' '.$cust->getData('lastname');
			
			

		}
		
		unset($customer);
		
		asort($custName);
		
	 return $custName;
	 
	 
	
	}
  public function _prepareForm()
  {
 //global $ids;

	  $url = Mage::helper('core/url')->getCurrentUrl();
	  $temp = explode('key/',$url);
	  $temp1 = explode('id/',$temp[0]);
		
		$st=str_replace('/','',$temp1[1]);
 

 // Mage::log($ids);
  
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('web_form', array('legend'=>Mage::helper('web')->__('Contract User')));
	   $custlist=$this->glo();
	   Mage::log('$custlist-=-=-');
	   Mage::log($custlist);
	   
	$testGroup =$fieldset->addField('contract_group', 'select',array(
          'label'     => Mage::helper('web')->__('Contract Customer Group'),
          'required'  => false,
          'name'      => 'contract_group',
		  'onchange' => 'getgroup(this)',
		    'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
	  ));
	  
	  
	 
	  $testGroup->setAfterElementHtml("<tbody id=\"group\"></tbody><script type=\"text/javascript\">getgroup($('contract_group'));
            function getgroup(selectElement){ 
			var reloadurl = '". $this->getUrl('*/*/group/id/'.$st.'/')."contract_group/' + selectElement.value+'".$querySTring."';
			new Ajax.Request(reloadurl, {
                    method: 'get',
                     onLoading: function (stateform) {
                        $('group').update('Searching...');
                    },
					 onComplete: function(stateform) {
                        $('group').update(stateform.responseText);
                    }
                });
            }
        </script>");
	 
	
	  // $fieldset->addField('user1', 'select', array(
          // 'label'     => Mage::helper('web')->__('User1'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user1',
		    // 'options'    => $custlist,
      // ));

		// $fieldset->addField('user2', 'select', array(
          // 'label'     => Mage::helper('web')->__('User2'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user2',
		 // 'options'    => $custlist,
		  
      // ));
	  // $fieldset->addField('user3', 'select', array(
          // 'label'     => Mage::helper('web')->__('User3'),
          // //'class'     => 'required-entry',
         // //'required'  => true,
          // 'name'      => 'user3',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user4', 'select', array(
          // 'label'     => Mage::helper('web')->__('User4'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user4',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user5', 'select', array(
          // 'label'     => Mage::helper('web')->__('User5'),
         // // 'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user5',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user6', 'select', array(
          // 'label'     => Mage::helper('web')->__('User6'),
         // // 'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user6',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user7', 'select', array(
          // 'label'     => Mage::helper('web')->__('User7'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user7',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user8', 'select', array(
          // 'label'     => Mage::helper('web')->__('User8'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user8',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user9', 'select', array(
          // 'label'     => Mage::helper('web')->__('User9'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user9',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user10', 'select', array(
          // 'label'     => Mage::helper('web')->__('User10'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user10',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user11', 'select', array(
          // 'label'     => Mage::helper('web')->__('User11'),
        // //  'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user11',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user12', 'select', array(
          // 'label'     => Mage::helper('web')->__('User12'),
        // //  'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user12',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user13', 'select', array(
          // 'label'     => Mage::helper('web')->__('User13'),
         // // 'class'     => 'required-entry',
         // //'required'  => true,
          // 'name'      => 'user13',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user14', 'select', array(
          // 'label'     => Mage::helper('web')->__('User14'),
         // // 'class'     => 'required-entry',
         // //'required'  => true,
          // 'name'      => 'user14',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user15', 'select', array(
          // 'label'     => Mage::helper('web')->__('User15'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user15',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user16', 'select', array(
          // 'label'     => Mage::helper('web')->__('User16'),
         // // 'class'     => 'required-entry',
         // //'required'  => true,
          // 'name'      => 'user16',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user17', 'select', array(
          // 'label'     => Mage::helper('web')->__('User17'),
          // //'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user17',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user18', 'select', array(
          // 'label'     => Mage::helper('web')->__('User18'),
         // // 'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user18',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user19', 'select', array(
          // 'label'     => Mage::helper('web')->__('User19'),
         // // 'class'     => 'required-entry',
          // //'required'  => true,
          // 'name'      => 'user19',
		  // 'options'    => $custlist,
      // ));
	  // $fieldset->addField('user20', 'select', array(
          // 'label'     => Mage::helper('web')->__('User20'),
         // // 'class'     => 'required-entry',
         // //'required'  => true,
          // 'name'      => 'user20',
		  // 'options'    => $custlist,
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