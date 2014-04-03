<?php

class Dtn_Mobiapp_CatController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                //->addFieldToFilter('is_active',1)
                //->addFieldToFilter('include_in_menu',1)
                ->addFieldToFilter('parent_id', 2);
        $cat = array();
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                //if($category->getProductCount()>0)
                //{
                $cat_id = $category->getId();
                $name = $category->getName();
                $image = $category->getImageUrl();
                $parentId = $category->getParentId();
                $nappUpdate = $category->getUpdatedAt();
                $catArr = array("id" => $cat_id, "parentId" => $parentId, "title" => $name, "image" => $image, "nappUpdate" => $nappUpdate);
                $cat[] = $catArr;
                //}
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($cat));
    }

    public function listAction() {
        $session = Mage::getSingleton('core/session');
        $cat_id = $this->getRequest()->getParam('id');
        $cat_id = (int) $cat_id;
        $child = $this->getRequest()->getParam('itemid');
        $child = (int) $child;
        $category = Mage::getModel('catalog/category')->load($cat_id);
        $p = array();
        $r = array();
        $p['result_code'] = '0';
        if ($category->getId() == $cat_id) {
            $categories = $category->getChildren();
            $cat = array();
            if (strlen($categories) > 0) {
                $categories = explode(',', $categories);
                //array_shift($categories);
                if ($child > 0) {
                    if (in_array($child, $categories)) {
                        $category = Mage::getModel('catalog/category')->load($child);
                        $cat_id = $category->getId();
                        $name = $category->getName();
                        $image = $category->getImageUrl();
                        if ($image == false)
                            $image = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/cache/1/image/265x/9df78eab33525d08d6e5fb8d27136e95/placeholder/default/noimage.png';
                        $parentId = $category->getParentId();
                        $nappUpdate = $category->getUpdatedAt();
                        $catArr = array("id" => $cat_id, "parentId" => $parentId, "title" => $name, "image" => $image, "nappUpdate" => $nappUpdate);
                        $cat[] = $catArr;
                        $p['result_code'] = '1';
                        $r['total_result'] = count($cat);
                        $r['category_detail'] = $cat;
                        $p['result'] = $r;
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
                        return;
                    }
                    else {
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($cat));
                        return;
                    }
                }
                foreach ($categories as $category) {
                    $category = Mage::getModel('catalog/category')->load($category);
                    //if($category->getIsActive() == 1 && $category->getIncludeInMenu() == 1 && $category->getProductCount() > 0)
                    //{
                    $cat_id = $category->getId();
                    $name = $category->getName();
                    $image = $category->getImageUrl();
                    if ($image == false)
                        $image = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/cache/1/image/265x/9df78eab33525d08d6e5fb8d27136e95/placeholder/default/noimage.png';
                    $parentId = $category->getParentId();
                    $nappUpdate = $category->getUpdatedAt();
                    $catArr = array("id" => $cat_id, "parentId" => $parentId, "title" => $name, "image" => $image, "nappUpdate" => $nappUpdate);
                    $cat[] = $catArr;
                    //}
                }

                $p['result_code'] = '1';
            }
            $p['SID'] = $session->getSessionId();
            $r['total_result'] = count($cat);
            $r['category_list'] = $cat;            
            $p['result'] = $r;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
        }else{
            $p['result_code'] = '0';
            $p['SID'] = $session->getSessionId();
            $r['message'] = 'Invalid category id';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
        }
    }

}
