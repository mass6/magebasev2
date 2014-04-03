<?php

class Insync_Web_Adminhtml_WebController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('web/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function formAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('web/adminhtml_web_edit_tab_form')
                        ->toHtml()
        );
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('web/web')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('web_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('web/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('web/adminhtml_web_edit'))
                    ->_addLeft($this->getLayout()->createBlock('web/adminhtml_web_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('web')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function duplicateAction() {
        $url = Mage::helper('core/url')->getCurrentUrl();
        $id = explode('id/', $url);
        $try = explode('/key/', $id[1]);
        $contractId = $try[0];
        $userId = array();
        $contractDetails1 = Mage::getModel('web/web');
        $contract = $contractDetails1->load($contractId);
        $check = $contract->toArray();
        foreach ($check as $key => $value) {
            if ($key == 'web_id' || $key == 'created_time' || $key == 'update_time' || $key == 'code' || $key == 'name') {
                continue;
            } else {
                $userId[$key] = $value;
            }
        }
        $newContract = Mage::getModel('web/web');
        $newContract->setData($userId);
        $newContract->setCode(null)
                ->setName(null)
                ->save();
        $this->_getSession()->addSuccess($this->__('The contract has been duplicated.'));
        $this->_redirect('*/*/edit', array('_current' => true, 'id' => $newContract->getId()));
    }

    public function countryAction() {
        $websiteId = $this->getRequest()->getParam('contract_website');
        $rules = Mage::getResourceModel('salesrule/rule_collection')->load();
        $ruleId = array();
        foreach ($rules as $rule) {
            $TempruleId = $rule['rule_id'];
            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $sql = "SELECT `website_id` FROM `salesrule_website` WHERE `rule_id` = " . $TempruleId . " ";
            $rul = $read->fetchAll($sql);
            $temprul = '';
            foreach ($rul as $key) {
                foreach ($key as $key1 => $value) {
                    if ($key1 = 'website_id') {
                        $temprul = $value;
                    }
                }
            }
            unset($read);
            if ($temprul == $websiteId) {
                if ($rule->getIsActive()) {
                    $ruleId[$rule->getId()] = $rule->getName();
                }
            }
        }

        $s = 1;
        $id = $this->getRequest()->getParam('id');
        $contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $id);
        foreach ($contractDetails as $each) {
            $storeid = $each['contract_store'];
        }
        $state = '';
        if ($websiteId != '') {
            $store_model = Mage::getModel('core/store');
            $statearray = $store_model->getCollection()->addWebsiteFilter($websiteId);
            $state = '<tr><td class="label"><label for="contract_store">Contract Store</label></td>';
            if (sizeof($statearray) > 0):
                $state .= "<td class = 'value'><select id='contract_store' name='contract_store' class='select'>";
                $state .= "<option value=''>-----Please Select-----</option>";
                foreach ($statearray as $_state) {
                    $state .= "<option value='" . $_state->getId() . "'" . (($storeid == $_state->getId()) ? 'selected="selected"' : '') . ">" . $_state->getName() . "</option>";
                }
                $state .="</select></td></tr>";
            else:
                $state .='<td class = "value"><input type="text" name="region" class=" input-text" value="' . $region . '"/></td></tr>';
            endif;
            // for ($i = 0; $i < 5; $i++) {

                // if ($websiteId == 3 && sizeof($statearray) > 0):
                    // $state .= '<tr><td class="label"><label for="business_rule">Business Rule' . $s . '</label></td>';
                    // $state .= "<td class = 'value'><select id='business_rule" . $s . "' name='business_rule" . $s . "' class='select'>";
                    // $state .= "<option value=''>-----Please Select-----</option>";
                    // foreach ($ruleId as $key => $value) {

                        // $state .= "<option value='" . $key . "'" . (($user == $key) ? 'selected="selected"' : '') . ">" . $value . "</option>";
                    // }
                    // $state .="</select></td></tr>";
                    // $s++;
                // else:
                    // $state .= '<tr><td class="label" hidden="hidden"><label for="business_rule">Business Rule' . $s . '</label></td>';
                    // $state .='<td class = "value"><input type="text" hidden="hidden" name="business_rule" class=" input-text" value="' . $value . '"/></td></tr>';
                // endif;
            // }
        }
        echo $state;
    }

    public function groupAction() {
        $group = $this->getRequest()->getParam('contract_group');
        $id = $this->getRequest()->getParam('id');
        $contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $id);
        foreach ($contractDetails as $each) {

            $user1 = $each['user1'];
            $user2 = $each['user2'];
            $user3 = $each['user3'];
            $user4 = $each['user4'];
            $user5 = $each['user5'];
            $user6 = $each['user6'];
            $user7 = $each['user7'];
            $user8 = $each['user8'];
            $user9 = $each['user9'];
            $user10 = $each['user10'];
            $user11 = $each['user11'];
            $user12 = $each['user12'];
            $user13 = $each['user13'];
            $user14 = $each['user14'];
            $user15 = $each['user15'];
            $user16 = $each['user16'];
            $user17 = $each['user17'];
            $user18 = $each['user18'];
            $user19 = $each['user19'];
            $user20 = $each['user20'];
        }
        // $region_id = $this->getRequest()->getParam('region_id');
        // $region = $this->getRequest()->getParam('region');
        $state = '';
        $j = 1;
        for ($i = 0; $i < 20; $i++) {
            if ($group != '') {
                Mage::log('$countrycode====');
                Mage::log($group);
                $customer = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('group_id', $group);
                $custId = array();
                $finalCustName = array();
                $custName = array();
                if ($j == 1)
                    $user = $user1;
                if ($j == 2)
                    $user = $user2;
                if ($j == 3)
                    $user = $user3;
                if ($j == 4)
                    $user = $user4;
                if ($j == 5)
                    $user = $user5;
                if ($j == 6)
                    $user = $user6;
                if ($j == 7)
                    $user = $user7;
                if ($j == 8)
                    $user = $user8;
                if ($j == 9)
                    $user = $user9;
                if ($j == 10)
                    $user = $user10;
                if ($j == 11)
                    $user = $user11;
                if ($j == 12)
                    $user = $user12;
                if ($j == 13)
                    $user = $user13;
                if ($j == 14)
                    $user = $user14;
                if ($j == 15)
                    $user = $user15;
                if ($j == 16)
                    $user = $user16;
                if ($j == 17)
                    $user = $user17;
                if ($j == 18)
                    $user = $user18;
                if ($j == 19)
                    $user = $user19;
                if ($j == 20)
                    $user = $user20;
                foreach ($customer as $cust) {
                    $custName[$cust->getId()] = $cust->getData('firstname') . ' ' . $cust->getData('lastname');
                }
                asort($custName);
                Mage::log($custName);
                unset($customer);

                $state = '<tr><td class="label"><label for="User">User' . $j . '</label></td>';
                if (sizeof($custName) > 0):
                    $state .= "<td class = 'value'><select id='user" . $j . "' name='user" . $j . "' class='select'>";
                    $state .= "<option value=''>-----Please Select-----</option>";
                    foreach ($custName as $key => $value) {
                        // Mage::log('$_state=pkp');
                        // Mage::log($_state);
                        $state .= "<option value='" . $key . "'" . (($user == $key) ? 'selected="selected"' : '') . ">" . $value . "</option>";
                    }
                    $state .="</select></td></tr>";
                else:
                    $state .='<td class = "value"><input type="text" name="region" class=" input-text" value="' . $region . '"/></td></tr>';
                endif;
            }

            $j++;
            echo $state;
        }
    }

    // public function countryAction() {  
    // Mage::log('hit it');
    // $countrycode = $this->getRequest()->getParam('contract_website');
    // $region_id = $this->getRequest()->getParam('region_id');
    // $region = $this->getRequest()->getParam('region');
    // $state = '';
    // if ($countrycode != '') {
    // Mage::log('$countrycode========');
    // Mage::log($countrycode);
    // $store_model = Mage::getModel('core/store');
    // $statearray = $store_model->getCollection()->addWebsiteFilter($countrycode);
    // $state = '<label>state</label>';
    // if(sizeof($statearray)>0):
    // $state .= "<select name='region_id' class='required-entry'>";
    // $state .= "<option value=''>Please Select</option>";
    // foreach ($statearray as $_state) {
    // $state .= "<option value='" . $_state->getCode() . "'". (($region_id == $_state->getCode())?'selected="selected"':'') .">" . $_state->getName() . "</option>";
    // }
    // $state .="</select>"; 
    // else:
    // $state .='<input type="text" name="region" class=" input-text" value="'.$region.'"/>';
    // endif;
    // }
    // echo $state;
    // }
    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $uploader->save($path, $_FILES['filename']['name']);
                } catch (Exception $e) {
                    
                }

                //this way the name is saved in DB
                $data['filename'] = $_FILES['filename']['name'];
            }


            $model = Mage::getModel('web/web');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('web')->__('Contract was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('web')->__('Unable to find Contract to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('web/web');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Contract was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $webIds = $this->getRequest()->getParam('web');
        if (!is_array($webIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($webIds as $webId) {
                    $web = Mage::getModel('web/web')->load($webId);
                    $web->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($webIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $webIds = $this->getRequest()->getParam('web');
        if (!is_array($webIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Contract(s)'));
        } else {
            try {
                foreach ($webIds as $webId) {
                    $web = Mage::getSingleton('web/web')
                            ->load($webId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($webIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}
