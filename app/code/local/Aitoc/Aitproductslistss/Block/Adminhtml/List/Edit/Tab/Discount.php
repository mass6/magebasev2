<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Discount.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ ZyBegMaBroqMqICa('70d02c42cb1ed0cff77149013bdb1828'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Discount extends Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Abstract
{
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('aitppl_discount_');
        $form->setFieldNameSuffix('aitppl_discount');

        $fieldset = $form->addFieldset('discount_fieldset', array(
            'legend'    => Mage::helper('aitproductslists')->__("Discount"),
        ));
            if($this->_getList()->getDiscount()->getId())
            {
                $fieldset->addField('id', 'hidden', array(
                    'name'   => 'id',
                ));
            }
            
            $fieldset->addField('is_approved', 'hidden', array(
                'name'   => 'is_approved',
            ));
            
//            $fieldset->addField('status', 'select', array(
//                'name'   => 'status',
//                'label'  => Mage::helper('aitproductslists')->__('Enable Accumulative Discount'),
//                'title'  => Mage::helper('aitproductslists')->__('Enable Accumulative Discount'),
//                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
//            ));

            $fieldset->addField('min_qty', 'text', array(
                'name'   => 'min_qty',
                'label'  => Mage::helper('aitproductslists')->__('Number of Purchases'),
                'title'  => Mage::helper('aitproductslists')->__('Number of Purchases'),
                'class'  => 'validate-number',
                'note'   => Mage::helper('aitproductslists')->__('To apply permanent discount set this to 0'),
            ));
            
            $fieldset->addField('price', 'text', array(
                'name'   => 'price',
                'label'  => Mage::helper('aitproductslists')->__('Discount Amount, %'),
                'title'  => Mage::helper('aitproductslists')->__('Discount Amount, %'),
                'class'  => 'validate-number',
            ));
            
            $fieldset->addField('from_date', 'date', array(
                'name'     => 'from_date',
                'label'    => Mage::helper('aitproductslists')->__('From Date'),
                'title'    => Mage::helper('aitproductslists')->__('From Date'),
                'format'   => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'image'    => $this->getSkinUrl('images/grid-cal.gif'),
                'readonly' => true,
            ));
            
            $fieldset->addField('to_date', 'date', array(
                'name'     => 'to_date',
                'label'    => Mage::helper('aitproductslists')->__('To Date'),
                'title'    => Mage::helper('aitproductslists')->__('To Date'),
                'format'   => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'image'    => $this->getSkinUrl('images/grid-cal.gif'),
                'readonly' => true,
            ));
            
        $form->setValues($this->_getList()->getDiscount()->getData());
        $this->setForm($form);
        return $this;
    }
} } 