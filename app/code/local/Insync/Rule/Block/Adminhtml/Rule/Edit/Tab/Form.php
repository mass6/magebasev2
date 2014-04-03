<?php

class Insync_Rule_Block_Adminhtml_Rule_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
	
	
	

  protected function _prepareForm()
  {
  
	// $model = Mage::registry('rule/rule');

        // $form = new Varien_Data_Form();
        // $form->setHtmlIdPrefix('rule_');

        // $fieldset = $form->addFieldset('base_fieldset',
            // array('legend' => Mage::helper('salesrule')->__('General Information'))
        // );

        // if ($model->getId()) {
            // $fieldset->addField('rule_id', 'hidden', array(
                // 'name' => 'rule_id',
            // ));
        // }

        // $fieldset->addField('product_ids', 'hidden', array(
            // 'name' => 'product_ids',
        // ));
  
  
		$webste = $this->websitelist();

      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('rule_form', array('legend'=>Mage::helper('rule')->__('Rule Information')));
	   
	   
	   
	    $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('rule')->__('Rule Name'),
            'title' => Mage::helper('rule')->__('Rule Name'),
            'required' => true,
        ));
		
	   $fieldset->addField('orderamount', 'text', array(
            'name' => 'orderamount',
			'title' => 'orderamount',
            'note' => 'Please Use the symbol to represent the condition of Order Amount. For ex:-(for between) 100-200,(greater than the amount) >200,(less than the amount) <300,(less than and equal to the amount)500<=,(Greater tahn and equal to amount)500>=, for the exact amount please dont use any symbol ' ,
            'label' => 'Order Total Amount',
            
        ));
	   
	    $fieldset->addField('l1', 'text', array(
            'name' => 'l1',
            'label' => Mage::helper('rule')->__('No. Approvals Required-Level 1'),
            'title' => Mage::helper('rule')->__('No. Approvals Required-Level 1'),
			'note' => 'Please Use Integer Value ex:- 4' ,
            
        ));
		
		$fieldset->addField('l2', 'text', array(
            'name' => 'l2',
            'label' => Mage::helper('rule')->__('No. Approvals Required-Level 2'),
            'title' => Mage::helper('rule')->__('No. Approvals Required-Level 2'),
			'note' => 'Please Use Integer Value ex:- 4' ,
            
        ));
		
		 $fieldset->addField('l3', 'text', array(
            'name' => 'l3',
            'label' => Mage::helper('rule')->__('No. Approvals Required-Level 3'),
            'title' => Mage::helper('rule')->__('No. Approvals Required-Level 3'),
			'note' => 'Please Use Integer Value ex:- 4' ,
            
        ));
		
		 $fieldset->addField('l4', 'text', array(
            'name' => 'l4',
            'label' => Mage::helper('rule')->__('No. Approvals Required-Level 4'),
            'title' => Mage::helper('rule')->__('No. Approvals Required-Level 4'),
			'note' => 'Please Use Integer Value ex:- 4' ,
            
        ));
		
		 $fieldset->addField('l5', 'text', array(
            'name' => 'l5',
            'label' => Mage::helper('rule')->__('No. Approvals Required-Level 5'),
            'title' => Mage::helper('rule')->__('No. Approvals Required-Level 5'),
			'note' => 'Please Use Integer Value ex:- 4' ,
            
        ));

   $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('rule')->__('Status'),
            'title'     => Mage::helper('rule')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('rule')->__('Active'),
                '0' => Mage::helper('rule')->__('Inactive'),
            ),
        ));
		
		
		 // if (!$model->getId()) {
            // $model->setData('is_active', '1');
        // }

		if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField('website_ids', 'select', array(
                'name'     => 'website_ids[]',
                'label'     => Mage::helper('rule')->__('Websites'),
                'title'     => Mage::helper('rule')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
     
		$customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        $found = false;

        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array(
                'value' => 0,
                'label' => Mage::helper('rule')->__('NOT LOGGED IN'))
            );
        }

        $fieldset->addField('customer_group_ids', 'select', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('rule')->__('Customer Groups'),
            'title'     => Mage::helper('rule')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray(),
        ));
		
	 
      // $fieldset->addField('code', 'text', array(
          // 'label'     => Mage::helper('rule')->__('Contract Code'),
          // 'class'     => 'required-entry',
          // 'required'  => true,
          // 'name'      => 'code',
      // ));

      // $fieldset->addField('name', 'text', array(
          // 'label'     => Mage::helper('rule')->__('Contract Name'),
          // 'class'     => 'required-entry',
          // 'required'  => true,
          // 'name'      => 'name',
	  // ));
		
		
		  // $fieldset->addField('contract_group', 'select',array(
          // 'label'     => Mage::helper('rule')->__('Contract Customer Group'),
          // 'required'  => false,
          // 'name'      => 'contract_group',
		   // //'onchange' => 'jsFunction()',
		    // 'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
	  // ));
		
	  
	 // $fieldset->addField('contract_website', 'select', array(
          // 'label'     => Mage::helper('rule')->__('Contract Website'),
          // 'required'  => false,
          // 'name'      => 'contract_website',
		  // //'onchange' => 'webFunction()',
		  // 'values'    => $webste,
		  
	  // ));	
	  
	   // $fieldset->addField('contract_store', 'select', array(
          // 'label'     => Mage::helper('rule')->__('Contract Store'),
          // 'required'  => false,
          // 'name'      => 'contract_store',
		  // 'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
             
		  
	  // ));	
	  // $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
           
	// $fieldset->addField('checkbox', 'checkbox', array(
          // 'label'     => Mage::helper('web')->__('Checkbox'),
          // 'name'      => 'Checkbox',
          // 'checked' => false,
          // 'onclick' => "",
          // 'onchange' => "",
          // 'value'  => '1',
          // 'disabled' => false,
          // 'after_element_html' => '<small>Comments</small>',
          // 'tabindex' => 1
        // ));
	  
	  
	  
	  
	   // $fieldset->addField('contract_group', 'select',array(
          // 'label'     => Mage::helper('web')->__('Contract Customer Group'),
          // 'required'  => false,
          // 'name'      => 'contract_group',
		   // 'onchange' => 'jsFunction()',
		    // 'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
	  // ));
	  
	  
	  
	  // $fieldset->addField('submit', 'submit', array(
          // 'label'     => Mage::helper('web')->__('Submit'),
          // 'required'  => true,
          // 'value'  => 'Submit',
          // 'after_element_html' => '<small>After click</small>',
          // 'tabindex' => 1
        // ));
		
      // $fieldset->addField('status', 'select', array(
          // 'label'     => Mage::helper('web')->__('Status'),
          // 'name'      => 'status',
          // 'values'    => array(
              // array(
                  // 'value'     => 1,
                  // 'label'     => Mage::helper('web')->__('Enabled'),
              // ),

              // array(
                  // 'value'     => 2,
                  // 'label'     => Mage::helper('web')->__('Disabled'),
              // ),
          // ),
      // ));
     // $suffixElement = $form->getElement('group');
	 // Mage::log('$suffixElement===========');
	 // Mage::log($suffixElement);
      if ( Mage::getSingleton('adminhtml/session')->getWebData() )
	  // Mage::log('enter====');
		// Mage::log(Mage::getSingleton('adminhtml/session')->getWebData());
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getWebData());
          Mage::getSingleton('adminhtml/session')->setWebData(null);
      } elseif ( Mage::registry('rule_data') ) {
          $form->setValues(Mage::registry('rule_data')->getData());
      }
	  
      return parent::_prepareForm();
  }
}