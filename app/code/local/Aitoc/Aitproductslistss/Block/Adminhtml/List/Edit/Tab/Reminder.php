<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Reminder.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('7087e29ecc2275d2cb474679108da5e1'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Reminder extends Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Abstract
{
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('aitppl_reminder_');
        $form->setFieldNameSuffix('aitppl_reminder');

        $fieldset = $form->addFieldset('reminder_fieldset', array(
            'legend'    => Mage::helper('aitproductslists')->__("Reminder Information"),
        ));
            if($this->_getList()->getReminder()->getId())
            {
                $fieldset->addField('id', 'hidden', array(
                    'name'   => 'id',
                ));
            }
        
            $fieldset->addField('status', 'select', array(
                'name'   => 'status',
                'label'  => Mage::helper('aitproductslists')->__('Enable Reminder'),
                'title'  => Mage::helper('aitproductslists')->__('Enable Reminder'),
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            ));
            
            $fieldset->addField('start_date', 'date', array(
                'name'     => 'start_date',
                'label'    => Mage::helper('aitproductslists')->__('Start From date'),
                'title'    => Mage::helper('aitproductslists')->__('Start From date'),
                'format'   => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'image'    => $this->getSkinUrl('images/grid-cal.gif'),
                'readonly' => true,
            ));
            
            $fieldset->addField('period', 'select', array(
                'name'   => 'period',
                'label'  => Mage::helper('aitproductslists')->__('Reminder Period'),
                'title'  => Mage::helper('aitproductslists')->__('Reminder Period'),
                'values' => Mage::getModel('aitproductslists/options_period')->toOptionArray(),
            ));
            
            $fieldset->addField('frequency', 'text', array(
                'name'   => 'frequency',
                'label'  => Mage::helper('aitproductslists')->__('How often'),
                'title'  => Mage::helper('aitproductslists')->__('How often'),
                'class'  => 'validate-number',

            ));
            
            $fieldset->addField('max_notify_qty', 'text', array(
                'name'   => 'max_notify_qty',
                'label'  => Mage::helper('aitproductslists')->__('Reminder Quantity'),
                'title'  => Mage::helper('aitproductslists')->__('Reminder Quantity'),
                'class'  => 'validate-number',
            ));
        
        $form->setValues($this->_getList()->getReminder()->getData());
        $this->setForm($form);
        return $this;
    }
} } 