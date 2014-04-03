<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Info.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('08a1d720759ed2e8d6991dee00ed908d'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Info extends Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Abstract
{
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('aitppl_info_');
        $form->setFieldNameSuffix('aitppl_info');

        
        //if($this->_getList()->getId())
//        {
            // Merge fieldset
//            $fieldset = $form->addFieldset('merge_fieldset', array(
//                'legend'    => Mage::helper('aitproductslists')->__("Merge Personal Products Lists"),
//            ));
//            
//                $fieldset->addField('merge_select', 'select', array(
//                    'name'               => 'merge',
//                    'label'              => Mage::helper('aitproductslists')->__('List to merge with'),
//                    'title'              => Mage::helper('aitproductslists')->__('List to merge with'),
//                    'values'             => Mage::getModel('aitproductslists/options_list')->setList($this->_getList())->toOptionArray(),
//                    'after_element_html' => '</td><td style="padding:5px 0;">' . $this->_getMergeButtonHtml() . $this->_getMergeButtonJS()
//                ));
//        }
//        
        // Desription fieldset
        $fieldset = $form->addFieldset('descr_fieldset', array(
            'legend'    => Mage::helper('aitproductslists')->__("Personal Products List Information"),
        ));

            if(!$this->_getList()->getId())
            {
                $fieldset->addField('store_id', 'select', array(
                    'name'       => 'store_id',
                    'label'      => Mage::helper('adminhtml')->__('Store'),
                    'title'      => Mage::helper('adminhtml')->__('Store'),
                    'values'     => Mage::getModel('aitproductslists/options_store')->setCustomer(Mage::registry('current_customer'))->toOptionArray(),
                    'required'   => true,
                ));
            }
    
            $fieldset->addField('name', 'text', array(
                'name'       => 'name',
                'label'      => Mage::helper('aitproductslists')->__('Name'),
                'title'      => Mage::helper('aitproductslists')->__('Name'),
                'required'   => true,
            ));
            
            $fieldset->addField('notes', 'textarea', array(
                'name'       => 'notes',
                'label'      => Mage::helper('aitproductslists')->__('Notes'),
                'title'      => Mage::helper('aitproductslists')->__('Notes'),
            ));
            
            $fieldset->addField('product_change_notify_status', 'select', array(
                'name'       => 'product_change_notify_status',
                'label'      => Mage::helper('aitproductslists')->__('Product Change Notifications'),
                'title'      => Mage::helper('aitproductslists')->__('Product Change Notifications'),
                'values'     => Mage::getModel('adminhtml/system_config_source_enabledisable')->toOptionArray(),
                'note'       => Mage::helper('aitproductslists')->__('Magento Product Alerts'),
            ));
        
        $form->setValues($this->_getList()->setStoreId($this->_getlist()->getQuote()->getStoreId())->getData());
        $this->setForm($form);
        return $this;
    }
    
    protected function _getMergeButtonHtml()
    {
         return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('aitproductslists')->__('Merge'),
                'onclick'   => 'aitpplMerge()',
            ))->toHtml();
    }
    
    protected function _getMergeButtonJS()
    {
        return "
        <script type=\"text/javascript\">
        //<![CDATA[
            function aitpplMerge()
            {
                var listId = $('aitppl_info_merge_select').value;
                if(listId > 0)
                {
                    if(confirm('" . Mage::helper('aitproductslists')->__('Are you sure?') . "'))
                    {
                        $('edit_form').action = '" . $this->getUrl('*/*/merge',array('_current' => true)) . "';
                        editForm.submit();
                    }
                }else{
                    alert('" . Mage::helper('aitproductslists')->__('Please select the list to merge with!') . "');
                }
            }
        //]]>
        </script>
        ";
    }
} } 