<?php

class MageWare_SalesGrids_Block_Adminhtml_Sales_Creditmemo_Grid
    extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid
{
    const XML_PATH_ENABLED = 'admin/creditmemo/enabled';
    const XML_PATH_COLUMNS = 'admin/creditmemo/columns';

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

        if (in_array('created_at', $columns)) {
            $collection->addFieldToSelect('created_at');
        }

        if (in_array('order_increment_id', $columns)) {
            $collection->addFieldToSelect('order_increment_id');
        }

        if (in_array('order_created_at', $columns)) {
            $collection->addFieldToSelect('order_created_at');
        }

        if (in_array('billing_name', $columns)) {
            $collection->addFieldToSelect('billing_name');
        }

        if (in_array('customer_email', $columns)) {
            $collection->addFieldToSelect('customer_email');
        }

        if (in_array('product_sku', $columns)) {
            $collection->addFieldToSelect('product_sku');
        }

        if (in_array('product_name', $columns)) {
            $collection->addFieldToSelect('product_name');
        }

        if (in_array('state', $columns)) {
            $collection->addFieldToSelect('state');
        }

        if (in_array('grand_total', $columns)) {
            $collection->addFieldToSelect('grand_total');
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
            $this->addColumn('increment_id', array(
                'header'    => Mage::helper('sales')->__('Credit Memo #'),
                'index'     => 'increment_id',
                'type'      => 'text',
            ));
        }

        if (in_array('created_at', $columns)) {
            $this->addColumn('created_at', array(
                'header'    => Mage::helper('sales')->__('Created At'),
                'index'     => 'created_at',
                'type'      => 'datetime',
            ));
        }

        if (in_array('order_increment_id', $columns)) {
            $this->addColumn('order_increment_id', array(
                'header'    => Mage::helper('sales')->__('Order #'),
                'index'     => 'order_increment_id',
                'type'      => 'text',
            ));
        }

        if (in_array('order_created_at', $columns)) {
            $this->addColumn('order_created_at', array(
                'header'    => Mage::helper('sales')->__('Order Date'),
                'index'     => 'order_created_at',
                'type'      => 'datetime',
            ));
        }

        if (in_array('billing_name', $columns)) {
            $this->addColumn('billing_name', array(
                'header' => Mage::helper('sales')->__('Bill to Name'),
                'index'  => 'billing_name',
            ));
        }

        if (in_array('customer_email', $columns)) {
            $this->addColumn('customer_email', array(
                'header' => Mage::helper('customer')->__('Email'),
                'index'  => 'customer_email',
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

        if (in_array('state', $columns)) {
            $this->addColumn('state', array(
                'header'    => Mage::helper('sales')->__('Status'),
                'index'     => 'state',
                'type'      => 'options',
                'options'   => Mage::getModel('sales/order_creditmemo')->getStates(),
            ));
        }

        if (in_array('grand_total', $columns)) {
            $this->addColumn('grand_total', array(
                'header'    => Mage::helper('customer')->__('Refunded'),
                'index'     => 'grand_total',
                'type'      => 'currency',
                'align'     => 'right',
                'currency'  => 'order_currency_code',
            ));
        }

        if (in_array('action', $columns)) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'    => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_creditmemo/view'),
                            'field'   => 'creditmemo_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'is_system' => true
            ));
        }

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));


        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
