<?php

class Insync_Web_Block_Adminhtml_Web_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function websitelist() {
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

    protected function _prepareForm() {
        $webste = $this->websitelist();
        $url = Mage::helper('core/url')->getCurrentUrl();
        $temp = explode('key/', $url);
        $temp1 = explode('id/', $temp[0]);
        $st = str_replace('/', '', $temp1[1]);
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('web_form', array('legend' => Mage::helper('web')->__('Contract Information')));
        $alo = array();
        $alo[0] = 'Yes';
        $alo[1] = 'No';

        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('web')->__('Contract Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'code',
        ));

        $fieldset->addField('cname', 'text', array(
            'label' => Mage::helper('web')->__('Contract Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'cname',
        ));


        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('web')->__('Display Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));

        $fieldset->addField('alo', 'select', array(
            'label' => Mage::helper('web')->__('Allow Level Override'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'alo',
            'values' => $alo,
        ));       

        $test = $fieldset->addField('contract_website', 'select', array(
            'label' => Mage::helper('web')->__('Contract Website'),
            'required' => true,
            'name' => 'contract_website',
            'onchange' => 'getstate(this)',
            'values' => $webste,
        ));

       
        /*
         * Add Ajax to the Country select box html output
         */
        $test->setAfterElementHtml("<tbody id=\"state\"></tbody><script type=\"text/javascript\">getstate($('contract_website'));
            function getstate(selectElement){ 
			var reloadurl = '" . $this->getUrl('*/*/country/id/' . $st . '/') . "contract_website/' + selectElement.value+'" . $querySTring . "';
			new Ajax.Request(reloadurl, {
                    method: 'get',
                     onLoading: function (stateform) {
                        $('state').update('Searching...');
                    },
					 onComplete: function(stateform) {
                        $('state').update(stateform.responseText);
                    }
                });
            }
        </script>");
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');

        if (Mage::getSingleton('adminhtml/session')->getWebData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getWebData());
            Mage::getSingleton('adminhtml/session')->setWebData(null);
        } elseif (Mage::registry('web_data')) {
            $form->setValues(Mage::registry('web_data')->getData());
        }
        return parent::_prepareForm();
    }

}
