<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Form.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ ZyBegMaBroqMqICa('27bd0017b195784bbc84c69cec72b9a1'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
        $form->setHtmlIdPrefix('aitppl_info_');
        $form->setFieldNameSuffix('aitppl_info');

        $list = Mage::registry('current_list');
        $customer = Mage::registry('current_customer');
        
        if(!$list->getCustomerId() && $customer && $customer->getId())
        {
            $list->setCustomerId($customer->getId());
        }

        if ($list->getId()) {
            $form->addField('id', 'hidden', array(
                'name' => 'id',
            ));

        }
        
        $form->addField('customer_id', 'hidden', array(
            'name' => 'customer_id',
        ));
        $form->setValues($list->getData());
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
} } 