<?php

class SM_CustomWidget_IndexController extends Mage_Core_Controller_Front_Action {
    public function IndexAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("titlename", array(
            "label" => $this->__("Titlename"),
            "title" => $this->__("Titlename")
        ));

        $this->renderLayout();

    }

    public function checkProductAction() {
        $data = $this->getRequest()->getParams();
        $origin = $data['origin'];
        $des = $data['destination'];
        $from = $data['from'];
        $return = $data['return'];
        $rOa = $data['ra'];
        $sku = 'gnav' . $origin . $des . 'reg' . $rOa;
        $storeId = $data['storeId'];
        $product = Mage::getModel('catalog/product')->setStoreId($storeId)->loadByAttribute('sku', $sku);

        if (is_null($product) || $product == false) {
            $this->getResponse()->setBody(json_encode(array(
                'result' => false
            )));
            return;
        } else {
            $link = $product->getProductUrl();
            $this->getResponse()->setBody(json_encode(array(
                'result' => true,
                'link' => $link,
                'id' => $product->getId()
            )));
            return;
        }

    }
}
