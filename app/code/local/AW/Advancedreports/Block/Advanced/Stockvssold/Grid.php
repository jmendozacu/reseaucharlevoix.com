<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedreports
 * @version    2.6.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Advancedreports_Block_Advanced_Stockvssold_Grid extends AW_Advancedreports_Block_Advanced_Grid
{
    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_ADVANCED_STOCKVSSOLD;

    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(true);
        $this->setId('gridAdvancedStockvssold');
        $this->_setUpFilters();
    }

    /**
     * Retrieves initialization array for custom report option
     *
     * @return array
     */
    public function getCustomOptionsRequired()
    {
        $array = parent::getCustomOptionsRequired();

        $addArray = array(
            array(
                'id'      => 'advancedreports_stockvssold_options_estimation_threshold',
                'type'    => 'text',
                'args'    => array(
                    'label'    => $this->__('Out of Stock Estimation Threshold'),
                    'title'    => $this->__('Out of Stock Estimation Threshold'),
                    'name'     => 'advancedreports_stockvssold_options_estimation_threshold',
                    'class'    => 'validate-greater-than-zero',
                    'required' => true,
                ),
                'default' => '90',
            ),
        );
        return array_merge($array, $addArray);
    }

    protected function _addCustomData($row)
    {
        $this->_customData[] = $row;
        return $this;
    }

    public function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->prepareReportCollection();
        $this->_prepareData();

        return $this;
    }

    public function prepareReportCollection()
    {
        /** @var AW_Advancedreports_Model_Mysql4_Collection_Sales $collection */
        $collection = Mage::getResourceModel('advancedreports/collection_stockvssold');
        $estimationDays = $this->getCustomOption('advancedreports_stockvssold_options_estimation_threshold');

        $collection
            ->reInitSelect()
            ->addOrderItems()
            ->addProductInfo()
            ->addEstimationThreshold($estimationDays)
        ;

        $this->_setUpFilters();
        $this->setCollection($collection);
        $this->_prepareAbstractCollection();

        if ($sort = $this->_getSort()) {
            $this->getColumn($sort)->setDir($this->_getDir());
        }

        $this->_saveFilters();
        $this->_setColumnFilters();
        Mage::helper('advancedreports')->setNeedMainTableAlias(true);

        return $this;
    }

    protected function _prepareData()
    {
        foreach ($this->getCollection() as $item) {
            if ($item->getProductType() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
                continue;
            }
            if ($item->getProductType() == 'bundle' && $item->getPrice() == 0) {
                continue;
            }

            $date = new Zend_Date(null, null, Mage::app()->getLocale()->getLocaleCode());
            $timeZone = Mage::app()->getStore()->getConfig('general/locale/timezone');
            $date->setTimezone($timeZone);
            $estimateData = $date->toString('yyyy-MM-dd');

            if ($item->getEstimateDays() > 0) {
                $days = round($item->getItemQty()/$item->getEstimateDays());
                $date->add($days, Zend_Date::DAY);
                $estimateData = $date->toString('yyyy-MM-dd');
            }
            $item->setEsitmationData($estimateData);
            $row = $item->getData();
            $this->_addCustomData($row);
        }
        $this->_preparePage();
        $this->getCollection()->setSize(count($this->_customData));
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            array(
                'header'       => $this->__('Product Name'),
                'index'        => 'name',
                'filter_index' => 'item.name',
                'type'         => 'text'
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header'        => $this->__('SKU'),
                'index'         => 'sku',
                'filter_index'  => 'item.sku',
                'type'          => 'text'
            )
        );

        $this->addColumn(
            'price',
            array(
                'header'        => $this->__('Price'),
                'index'         => 'price',
                'filter_index'  => 'COALESCE(priceTable.value, 0)',
                'type'          => 'currency',
                'disable_total' => true,
                'currency_code' => $this->getCurrentCurrencyCode(),
            )
        );

        $this->addColumn(
            'sum_qty',
            array(
                'header'       => $this->__('Items Ordered'),
                'index'        => 'sum_qty',
                'type'         => 'number',
                'total'        => 'sum',
                'renderer'     => 'advancedreports/widget_grid_column_renderer_percent',
                'width'        => '100px',
                'filter_index' => $this->getSumFieldFilterIndex('qty_ordered'),
            )
        );

        $this->addColumn(
            'sum_total',
            array(
                'header'        => $this->__('Total'),
                'width'         => '120px',
                'type'          => 'currency',
                'filter_index'  => $this->getSumFieldFilterIndex('base_row_total'),
                'total'         => 'sum',
                'currency_code' => $this->getCurrentCurrencyCode(),
                'renderer'      => 'advancedreports/widget_grid_column_renderer_percent',
                'index'         => 'sum_total'
            )
        );

        $this->addColumn(
            'sum_invoiced',
            array(
                'header'        => $this->__('Invoiced'),
                'width'         => '120px',
                'type'          => 'currency',
                'filter_index'  => 'COALESCE(item.base_row_invoiced, 0)',
                'total'         => 'sum',
                'currency_code' => $this->getCurrentCurrencyCode(),
                'renderer'      => 'advancedreports/widget_grid_column_renderer_percent',
                'index'         => 'sum_invoiced'
            )
        );

        $this->addColumn(
            'sum_refunded',
            array(
                'header'        => $this->__('Refunded'),
                'width'         => '120px',
                'type'          => 'currency',
                'filter_index'  => 'COALESCE(refund.base_row_total, 0)',
                'total'         => 'sum',
                'currency_code' => $this->getCurrentCurrencyCode(),
                'renderer'      => 'advancedreports/widget_grid_column_renderer_percent',
                'index'         => 'sum_refunded'
            )
        );

        $this->addColumn(
            'cost',
            array(
                'header'        => $this->__('Product Cost'),
                'width'         => '120px',
                'type'          => 'currency',
                'filter_index'  => 'COALESCE(costTable.value, 0)',
                'total'         => 'sum',
                'currency_code' => $this->getCurrentCurrencyCode(),
                'index'         => 'cost'
            )
        );

        $this->addColumn(
            'item_qty',
            array(
                'header'        => $this->__('Stock Qty'),
                'index'         => 'item_qty',
                'filter_index'  => 'COALESCE(stock.qty, 0)',
                'disable_total' => true,
                'type'          => 'number',
                'width'         => '100px'
            )
        );

        $this->addColumn(
            'esitmation_data',
            array(
                'header'        => $this->__('Out of Stock Estimate'),
                'index'         => 'esitmation_data',
                'disable_total' => true,
                'filter'        => false,
                'type'          => 'datetime',
                'align'         => 'right',
            )
        );

        $this->addExportType('*/*/exportOrderedCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', $this->__('Excel'));
        return $this;
    }

    public function getChartType()
    {
        return 'none';
    }

    public function hasRecords()
    {
        return false;
    }

    public function hasAggregation()
    {
        return false;
    }

    public function getSumFieldFilterIndex($columnIndex)
    {
        $orderTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order');
        $itemTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order_item');

        $orderStatus = explode(",", Mage::helper('advancedreports')->confProcessOrders());
        $orderStatus = "'" . implode("','", $orderStatus) . "'";
        $filterField = Mage::helper('advancedreports')->confOrderDateFilter();

        $dateFrom = $this->_getMysqlFromFormat($this->getFilter('report_from'));
        $dateTo = $this->_getMysqlToFormat($this->getFilter('report_to'));

        $storeCondition = "1=1";
        if ($this->getStoreIds()) {
            $storeIds = "'" . implode("','", $this->getStoreIds()) . "'";
            $storeCondition = "order_sf.store_id IN ({$storeIds})";
        }

        return "(SELECT COALESCE(SUM({$columnIndex}), 0) AS {$columnIndex}
                    FROM {$orderTable} AS `order_sf`
                    LEFT JOIN {$itemTable} AS `item_sf` ON order_sf.entity_id = item_sf.order_id
                    WHERE item.product_id = item_sf.product_id
                    AND order_sf.{$filterField} >= '{$dateFrom}' AND order_sf.{$filterField} <= '{$dateTo}' AND {$storeCondition} AND order_sf.status IN ({$orderStatus})
                    GROUP BY item_sf.product_id)"
            ;
    }
}
