<?php

require_once("Mage/Adminhtml/controllers/Sales/OrderController.php");

class Redstage_PrintOrder_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{

    public function pdfOrdersAction(){

        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $orders = Mage::getResourceModel('sales/order_collection')
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', $orderId)
                    ->load();
                if ($orders->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('printorder/sales_order_pdf_order')->getPdf($orders);
                    } else {
                        $pages = Mage::getModel('printorder/sales_order_pdf_order')->getPdf($orders);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'order'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    public function pdfdocsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $orders = Mage::getResourceModel('sales/order_collection')
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', $orderId)
                    ->load();
                if ($orders->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('printorder/sales_order_pdf_order')->getPdf($orders);
                    } else {
                        $pages = Mage::getModel('printorder/sales_order_pdf_order')->getPdf($orders);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
                
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($invoices->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'docs'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf',
                    $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

}
