<?php

class MageWare_SalesGrids_Block_Adminhtml_Sales_Order_Grid
    extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    const XML_PATH_ENABLED = 'admin/order/enabled';
    const XML_PATH_COLUMNS = 'admin/order/columns';

    protected function _prepareCollection()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            return parent::_prepareCollection();
        }

        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('base_currency_code')
            ->addFieldToSelect('order_currency_code')
        ;

        $columns = explode(',', Mage::getStoreConfig(self::XML_PATH_COLUMNS));

        if (in_array('increment_id', $columns)) {
            $collection->addFieldToSelect('increment_id');
        }

        if (in_array('store_id', $columns)) {
            if (!Mage::app()->isSingleStoreMode()) {
                $collection->addFieldToSelect('store_id');
            }
        }

        if (in_array('created_at', $columns)) {
            $collection->addFieldToSelect('created_at');
        }

        if (in_array('billing_name', $columns)) {
            $collection->addFieldToSelect('billing_name');
        }

        if (in_array('shipping_name', $columns)) {
            $collection->addFieldToSelect('shipping_name');
        }

        if (in_array('customer_email', $columns)) {
            $collection->addFieldToSelect('customer_email');
        }

        if (in_array('customer_is_guest', $columns)) {
            $collection->addFieldToSelect('customer_is_guest');
        }

        if (in_array('customer_group_id', $columns)) {
            $collection->addFieldToSelect('customer_group_id');
        }

        if (in_array('product_sku', $columns)) {
            $collection->addFieldToSelect('product_sku');
        }

        if (in_array('product_name', $columns)) {
            $collection->addFieldToSelect('product_name');
        }

        if (in_array('payment_method', $columns)) {
            $collection->addFieldToSelect('payment_method');
        }

        if (in_array('base_grand_total', $columns)) {
            $collection->addFieldToSelect('base_grand_total');
        }

        if (in_array('grand_total', $columns)) {
            $collection->addFieldToSelect('grand_total');
        }

        if (in_array('status', $columns)) {
            $collection->addFieldToSelect('status');
        }

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            return parent::_prepareColumns();
        }

        $columns = explode(',', Mage::getStoreConfig(self::XML_PATH_COLUMNS));

        if (in_array('increment_id', $columns)) {
            $this->addColumn('real_order_id', array(
                'header'=> Mage::helper('sales')->__('Order #'),
                'width' => '80px',
                'type'  => 'text',
                'index' => 'increment_id',
            ));
        }

        if (in_array('store_id', $columns)) {
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addColumn('store_id', array(
                    'header'          => Mage::helper('sales')->__('Purchased From (Store)'),
                    'index'           => 'store_id',
                    'type'            => 'store',
                    'store_view'      => true,
                    'display_deleted' => true,
                ));
            }
        }

        if (in_array('created_at', $columns)) {
            $this->addColumn('created_at', array(
                'header' => Mage::helper('sales')->__('Purchased On'),
                'index'  => 'created_at',
                'type'   => 'datetime',
                'width'  => '100px',
            ));
        }

        if (in_array('billing_name', $columns)) {
            $this->addColumn('billing_name', array(
                'header' => Mage::helper('sales')->__('Bill to Name'),
                'index'  => 'billing_name',
            ));
        }

        if (in_array('shipping_name', $columns)) {
            $this->addColumn('shipping_name', array(
                'header' => Mage::helper('sales')->__('Ship to Name'),
                'index'  => 'shipping_name',
            ));
        }

        if (in_array('customer_email', $columns)) {
            $this->addColumn('customer_email', array(
                'header' => Mage::helper('customer')->__('Email'),
                'index'  => 'customer_email',
            ));
        }

        if (in_array('customer_is_guest', $columns)) {
            $this->addColumn('customer_is_guest', array(
                'header'  => Mage::helper('customer')->__('Guest'),
                'index'   => 'customer_is_guest',
                'type'    => 'options',
                'options' => array(
                    0 => Mage::helper('adminhtml')->__('No'),
                    1 => Mage::helper('adminhtml')->__('Yes')
                )
            ));
        }

        if (in_array('customer_group_id', $columns)) {
            $groups = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionHash();

            $this->addColumn('customer_group_id', array(
                'header'    =>  Mage::helper('customer')->__('Customer Group'),
                'width'     =>  '100px',
                'index'     =>  'customer_group_id',
                'type'      =>  'options',
                'options'   =>  $groups,
            ));
        }

        if (in_array('product_sku', $columns)) {
            $this->addColumn('product_sku', array(
                'header'   => Mage::helper('catalog')->__('SKU'),
                'index'    => 'product_sku',
                'renderer' => 'salesgrids/adminhtml_widget_grid_column_renderer_list'
            ));
        }

        if (in_array('product_name', $columns)) {
            $this->addColumn('product_name', array(
                'header'   => Mage::helper('catalog')->__('Product'),
                'index'    => 'product_name',
                'renderer' => 'salesgrids/adminhtml_widget_grid_column_renderer_list'
            ));
        }

        if (in_array('payment_method', $columns)) {
            $this->addColumn('payment_method', array(
                'header'  => Mage::helper('payment')->__('Payment Method'),
                'index'   => 'payment_method',
                'type'    => 'options',
                'options' => Mage::helper('payment')->getPaymentMethodList()
            ));
        }

        if (in_array('base_grand_total', $columns)) {
            $this->addColumn('base_grand_total', array(
                'header'   => Mage::helper('sales')->__('G.T. (Base)'),
                'index'    => 'base_grand_total',
                'type'     => 'currency',
                'currency' => 'base_currency_code',
            ));
        }

        if (in_array('grand_total', $columns)) {
            $this->addColumn('grand_total', array(
                'header'   => Mage::helper('sales')->__('G.T. (Purchased)'),
                'index'    => 'grand_total',
                'type'     => 'currency',
                'currency' => 'order_currency_code',
            ));
        }

        if (in_array('status', $columns)) {
            $this->addColumn('status', array(
                'header'  => Mage::helper('sales')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'width'   => '70px',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            ));
        }

        if (in_array('action', $columns)) {
            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
                $this->addColumn('action',
                    array(
                        'header'    => Mage::helper('sales')->__('Action'),
                        'width'     => '50px',
                        'type'      => 'action',
                        'getter'    => 'getId',
                        'actions'   => array(
                            array(
                                'caption' => Mage::helper('sales')->__('View'),
                                'url'     => array('base'=>'*/sales_order/view'),
                                'field'   => 'order_id'
                            )
                        ),
                        'filter'    => false,
                        'sortable'  => false,
                        'index'     => 'stores',
                        'is_system' => true,
                ));
            }
        }

        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
