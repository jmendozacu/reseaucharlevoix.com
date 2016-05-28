<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Package_Item_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('package_selection_search_grid');
        $this->setRowClickCallback('packageSelection.productGridRowClick.bind(packageSelection)');
        $this->setCheckboxCheckCallback('packageSelection.productGridCheckboxCheck.bind(packageSelection)');
        $this->setRowInitCallback('packageSelection.productGridRowInit.bind(packageSelection)');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    protected function _beforeToHtml() {
        $this->setId($this->getId() . '_' . $this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName() . '.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName() . '.doFilter()');

        return parent::_beforeToHtml();
    }

    protected function _prepareCollection() {
        if ($this->getComponent()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->setStore($this->getStore())
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('manufacturer')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('special_price')
                ->addAttributeToSelect('attribute_set_id')
                ->addAttributeToSelect('status')
                ->addAttributeToFilter('type_id', array('in' => $this->getAllowedSelectionTypes()))
                ->addStoreFilter();
        $this->setCollection($collection);

        if ($products = $this->_getProducts()) {
            $collection->addIdFilter($this->_getProducts(), true);
        }


        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText($this->__('Please enter search conditions to view products.'));
        }

        //Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('sales')->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('sales')->__('Product Name'),
            'index' => 'name',
            'column_css_class' => 'name'
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();

        $this->addColumn('set_name', array(
            'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width' => '100px',
            'index' => 'attribute_set_id',
            'type' => 'options',
            'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('sales')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
            'column_css_class' => 'sku'
        ));

        $this->addColumn('category_list', array(
            'header' => Mage::helper('bundlecreatorplus')->__('Category'),
            'index' => 'category_list',
            'sortable' => false,
            'width' => '250px',
            'type' => 'options',
            'options' => Mage::getSingleton('bundlecreatorplus/system_config_source_category')->toOptionArray(),
            'renderer' => 'bundlecreatorplus/adminhtml_product_edit_tab_package_item_search_render_category',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('sales')->__('Price'),
            'align' => 'center',
            'type' => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate' => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index' => 'price',
            'column_css_class' => 'price'
        ));

        $this->addColumn('special_price', array(
            'header' => Mage::helper('sales')->__('Special Price'),
            'align' => 'center',
            'type' => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate' => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index' => 'special_price',
            'column_css_class' => 'special_price'
        ));

        $this->addColumn('is_selected', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_selected',
            'align' => 'center',
            'values' => $this->_getSelectedProducts(),
            'index' => 'entity_id',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('adminhtml/package_selection/grid', array('index' => $this->getIndex(), 'productss' => implode(',', $this->_getProducts())));
    }

    public function _getSelectedProducts() {
        if ($component = $this->getComponent()) {
            return $component->getProductIds();
        } else {
            return false;
        }
    }

    protected function _getProducts() {
        if ($products = $this->getRequest()->getPost('products', null)) {
            return $products;
        } else if ($productss = $this->getRequest()->getParam('productss', null)) {
            return explode(',', $productss);
        } else {
            return array();
        }
    }

    public function getStore() {
        return Mage::app()->getStore();
    }

    public function getAllowedSelectionTypes() {
        return Mage::helper('bundlecreatorplus')->getAllowedSelectionTypes();
    }

    public function filterCallback($collection, $column) {
        $value = $column->getFilter()->getValue();
        $_category = Mage::getModel('catalog/category')->load($value);
        $collection->addCategoryFilter($_category);

        return $collection;
    }

}
