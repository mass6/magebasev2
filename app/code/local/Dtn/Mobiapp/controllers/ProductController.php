<?php

class Dtn_Mobiapp_ProductController extends Mage_Core_Controller_Front_Action {

    public function listbycatAction() {
        $session = Mage::getSingleton('core/session');
        $cat_id = $this->getRequest()->getParam('id');
        $limit = $this->getRequest()->getParam('limit');
        $offset = $this->getRequest()->getParam('offset');
        $child = $this->getRequest()->getParam('itemid');
        $child = (int) $child;
        $cat_id = (int) $cat_id;
        $limit = (int) $limit;
        $offset = (int) $offset;
        if ($limit > 0)
            $numpage = $offset / $limit + 1;
        else {
            $limit = 9999999;
            $numpage = 1;
        }
        $category = Mage::getModel('catalog/category')->load($cat_id);
        $p = array();
        $r = array();
        $h = array();
        if ($child > 0) {
            $product = Mage::getModel('catalog/product')->load($child);
            if ($product->getStatus() == 1) {
                $id = $product->getId();
                $title = $product->getName();
                $thumb = $product->getThumbnailUrl();
                $image = $product->getImageUrl();
                $thumb = (string) Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200, 200);
                $image = (string) Mage::helper('catalog/image')->init($product, 'image')->resize(300, 300);
                $price = $product->getFinalPrice();
                //$price = number_format($price, 2);
                $shortdesc = $product->getShortDescription();
                $shortdesc = strip_tags($shortdesc);
                $description = $product->getDescription();
                $description = strip_tags($description);
                $qrcode = $product->getSku();
                $nappUpdate = $product->getUpdatedAt();
                $pArray = array("id" => $id, "categoryid" => $cat_id, "title" => $title, "thumb" => $thumb, "image" => $image, "shortdesc" => $shortdesc, "description" => $description, "qrcode" => $qrcode, "price" => $price, "nappUpdate" => $nappUpdate);
                $h[] = $pArray;
            }
            $p['result_code'] = '1';
            $r['total_result'] = count($h);
            $r['product_detail'] = $h;
            $p['result'] = $r;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
            return;
        }
        $products = $category->getProductCollection()
                //->addAttributeToSelect('*')
                ->addAttributeToSelect('status')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('description')
                ->addAttributeToSelect('short_description')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('thumbnail')
                ->setPage($numpage, $limit);
        $size = $products->getSize();
        if ($offset > $size)
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
        foreach ($products as $product) {
            if ($product->getStatus() == 1) {
                $id = $product->getId();
                $title = $product->getName();
                $thumb = $product->getThumbnailUrl();
                $image = $product->getImageUrl();
                $thumb = (string) Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200, 200);
                $image = (string) Mage::helper('catalog/image')->init($product, 'image')->resize(300, 300);
                $price = $product->getFinalPrice();
                //$price = number_format($price, 2);
                $shortdesc = $product->getShortDescription();
                $shortdesc = strip_tags($shortdesc);
                $description = $product->getDescription();
                $description = strip_tags($description);
                $qrcode = $product->getSku();
                $nappUpdate = $product->getUpdatedAt();
                $pArray = array("id" => $id, "categoryid" => $cat_id, "title" => $title, "thumb" => $thumb, "image" => $image, "shortdesc" => $shortdesc, "description" => $description, "qrcode" => $qrcode, "price" => $price, "nappUpdate" => $nappUpdate);
                $h[] = $pArray;
            }
        }
        $p['result_code'] = '1';
        $p['SID'] = $session->getSessionId();
        $r['total_result'] = count($h);
        $r['product_list'] = $h;
        $p['result'] = $r;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

    public function searchAction() {
        $session = Mage::getSingleton('core/session');
        $q = $this->getRequest()->getParam('q');
        $limit = $this->getRequest()->getParam('limit');
        $offset = $this->getRequest()->getParam('offset');
        $limit = (int) $limit;
        $offset = (int) $offset;
        if ($limit > 0)
            $numpage = $offset / $limit + 1;
        else {
            $limit = 9999999;
            $numpage = 1;
        }
        $filter_a = array('like' => "%$q%");
        $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(array(
                    array('attribute' => 'name', 'like' => $filter_a),
                    array('attribute' => 'description', 'like' => $filter_a),
                    array('attribute' => 'short_description', 'like' => $filter_a)))
                ->setPage($numpage, $limit);
        $p = array();
        $r = array();
        $h = array();
        if (count($products) > 0) {
            foreach ($products as $product) {
                if ($product->getStatus() == 1) {
                    $id = $product->getId();
                    $title = $product->getName();
                    $thumb = $product->getThumbnailUrl();
                    $image = $product->getImageUrl();
                    $thumb = (string) Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200, 200);
                    $image = (string) Mage::helper('catalog/image')->init($product, 'image')->resize(300, 300);
                    $price = $product->getFinalPrice();
                    //$price = number_format($price, 2);
                    $shortdesc = $product->getShortDescription();
                    $shortdesc = strip_tags($shortdesc);
                    $description = $product->getDescription();
                    $description = strip_tags($description);
                    $qrcode = $product->getSku();
                    $nappUpdate = $product->getUpdatedAt();
                    $pArray = array("id" => $id, "title" => $title, "thumb" => $thumb, "image" => $image, "shortdesc" => $shortdesc, "description" => $description, "qrcode" => $qrcode, "price" => $price, "nappUpdate" => $nappUpdate);
                    $h[] = $pArray;
                }
            }
            $p['result_code'] = '1';
            $p['SID'] = $session->getSessionId();
            $r['total_result'] = count($h);
            $r['product_list'] = $h;
            $p['result'] = $r;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
        } else {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0','SID'=>$session->getSessionId(), 'message' => 'No product matches search criteria')));
        }
    }

    function qrAction() {
        $productId = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($productId);
        $sku = $product->getSku();
        $p = array("qr" => $sku);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

    function ajaxAction() {
        $categoryId = (int) $this->getRequest()->getParam('catid');
        $q = $this->getRequest()->getParam('q');
        $curPage = $this->getRequest()->getParam('p', 1);
        $limit = (int) $this->getRequest()->getParam('limit', 16);

        if ($categoryId) {
            $category = Mage::getModel('catalog/category');
            $category->load($categoryId);
            $products = $category->getProductCollection();
        } else {
            $products = Mage::getModel('catalog/product')->getCollection();
        }
        $products->addAttributeToSelect('status')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('short_description')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image');
        if ($q)
            $products->addAttributeToFilter(
                array(
                    array('attribute' => 'name', 'like' => array('like' => "%$q%"))
                )
            );
        $products->setPage($curPage, $limit);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);

//        $block = Mage::getSingleton('core/layout')->createBlock('mobiapp/product_list')->setTemplate('mobiapp/product/list.phtml');
//        $block->setProductCollection($products);
//        $this->getResponse()->setBody($block->toHtml());
        if ($products->count()) {
            $response = array();
            $response['code'] = 0;
            $response['product_count'] = $products->getSize();;
            $productsArr = array();
            $coreHelper = Mage::helper('core');
            foreach ($products as $product) {
                $id = $product->getId();
                $name = $product->getName();
                $shortDesc = $product->getShortDescription();
                $price = $coreHelper->currency($product->getFinalPrice(), true, false);
                $url = $product->getProductUrl();
                $image = (string) Mage::helper('catalog/image')->init($product, 'small_image')->resize(220, 220);
                $productsArr[] = array(
                    'id' => $id,
                    'name' => $name,
                    'short_desc' => $shortDesc,
                    'price' => $price,
                    'url' => $url,
                    'image' => $image
                );
            }
            $response['products'] = $productsArr;
            $this->getResponse()->setBody($coreHelper->jsonEncode($response));
        } else {
            $this->getResponse()->setBody(json_encode(array('code' => '101', 'message' => $this->__('There are no products matching the selection.'))));
        }
    }
    
    // return product information in json
    function infoAction() {
        $productId = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($productId);
        $coreHelper = Mage::helper('core');
        if ($product->getId()) {
            $info = array(
                'error' => '',
                'id' => $product->getId(),
                'name' => $product->getName(),
                'short_desc' => $product->getShortDescription(),
                //'price' => $coreHelper->currency($product->getFinalPrice(), true, false),
                'price' => $product->getFinalPrice(),
                'url' => $product->getProductUrl(),
                'image' => (string) Mage::helper('catalog/image')->init($product, 'small_image')->resize(220, 220)
            );
            $this->getResponse()->setBody($coreHelper->jsonEncode($info));
        } else {
            $this->getResponse()->setBody(json_encode(array('error' => 'Could not get product information')));
        }
    }
}
