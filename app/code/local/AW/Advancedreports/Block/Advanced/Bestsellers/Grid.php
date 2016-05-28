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

/**
 * Bestsellers Report Grid
 */
class AW_Advancedreports_Block_Advanced_Bestsellers_Grid extends AW_Advancedreports_Block_Advanced_Grid
{
    const OPTION_BESTSELLER_GROUPED_SKU = 'advancedreports_bestsellers_options_skutype';

    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_ADVANCED_BESTSELLERS;
    protected $_bestsellerVarData;

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridBestsellers');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->prepareReportCollection();
        $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
        $this->_prepareData();

        return $this;
    }

    public function prepareReportCollection()
    {
        /** @var AW_Advancedreports_Model_Mysql4_Collection_Bestsellers $collection */
        $collection = Mage::getResourceModel('advancedreports/collection_bestsellers');

        $this->setCollection($collection);

        $dateFrom = $this->_getMysqlFromFormat($this->getFilter('report_from'));
        $dateTo = $this->_getMysqlToFormat($this->getFilter('report_to'));
        $this->getCollection()->setDateFilter($dateFrom, $dateTo)->setState();

        $storeIds = $this->getStoreIds();
        if (count($storeIds)) {
            $this->setStoreFilter($storeIds);
        }

        $this->addOrderItems($this->getCustomOption('advancedreports_bestsellers_options_bestsellers_count'), $dateFrom, $dateTo);

        $key = $this->getFilter('reload_key');
        if ($key === 'qty') {
            $this->setDefaultPercentField('sum_qty');
            $this->getCollection()->orderByQty();
        } elseif ($key === 'total') {
            $this->setDefaultPercentField('sum_total');
            $this->getCollection()->orderByTotal();
        }
        $this->getCollection()->addProfitInfo($dateFrom, $dateTo);

        return $this;
    }

    /**
     * Retrieves initialization array for custom report option
     *
     * @return array
     */
    public function getCustomOptionsRequired()
    {
        $array = parent::getCustomOptionsRequired();
        $skutypes = Mage::getSingleton('advancedreports/system_config_source_skutype')->toOptionArray();
        $addArray = array(
            array(
                'id'      => 'advancedreports_bestsellers_options_bestsellers_count',
                'type'    => 'text',
                'args'    => array(
                    'label'    => $this->__('Products to show'),
                    'title'    => $this->__('Products to show'),
                    'name'     => 'advancedreports_bestsellers_options_bestsellers_count',
                    'class'    => '',
                    'required' => true,
                ),
                'default' => '10',
            ),
            array(
                'id'      => self::OPTION_BESTSELLER_GROUPED_SKU,
                'type'    => 'select',
                'args'    => array(
                    'label'    => $this->__('SKU usage'),
                    'title'    => $this->__('SKU usage'),
                    'name'     => self::OPTION_BESTSELLER_GROUPED_SKU,
                    'class'    => '',
                    'required' => true,
                    'values'   => $skutypes,
                ),
                'default' => AW_Advancedreports_Model_System_Config_Source_Skutype::SKUTYPE_SIMPLE,
            ),
        );
        return array_merge($array, $addArray);
    }

    /**
     * Filter collection by Store Ids
     *
     * @param array $storeIds
     *
     * @return AW_Advancedreports_Block_Advanced_Bestsellers_Grid
     */
    public function setStoreFilter($storeIds)
    {
        $this->getCollection()->setStoreFilter($storeIds);
        return $this;
    }

    public function addOrderItems($limit = 10, $dateFrom, $dateTo)
    {
        $skuType = $this->getCustomOption(self::OPTION_BESTSELLER_GROUPED_SKU);

        $this->getCollection()->addOrderItems($limit, $dateFrom, $dateTo, $skuType);
        return $this;
    }

    public function getNeedReload()
    {
        return Mage::helper('advancedreports')->getNeedReload($this->_routeOption);
    }

    protected function _addBestsellerData($row)
    {
        $this->_customData[] = $row;
        return $this;
    }

    /*
     * Need to sort bestsellers array
     */
    protected function _compareTotalElements($a, $b)
    {
        if ($a['sum_total'] == $b['sum_total']) {
            return 0;
        }
        return ($a['sum_total'] > $b['sum_total']) ? -1 : 1;
    }

    /*
    * Need to sort bestsellers array
    */
    protected function _compareQtyElements($a, $b)
    {
        if ($a['sum_qty'] == $b['sum_qty']) {
            return 0;
        }
        return ($a['sum_qty'] > $b['sum_qty']) ? -1 : 1;
    }

    /*
     * Prepare data array for Pie and Grid
     */
    protected function _prepareData()
    {
        # Extract data from collection
        $select = $this->getCollection()->getSelect();
        $col = $this->getCollection()->getConnection()->fetchAll($select);

        if ($col && count($col)) {
            foreach ($col as $_subItem) {
                $row = $_subItem;
                # Get all colummns values
                if (isset($row['entity_id'])) {
                    $_product = Mage::getModel('catalog/product')->load($row['entity_id']);
                    if ($_product->getTypeId() == 'bundle'
                        && $_product->getPrice() == 0
                        && $this->getCustomOption(self::OPTION_BESTSELLER_GROUPED_SKU) == AW_Advancedreports_Model_System_Config_Source_Skutype::SKUTYPE_SIMPLE
                    ) {
                        continue;
                    }
                    if ($_product->getData()) {
                        $row['name'] = $_product->getName();
                    }

                    unset($_product);
                }

                $this->_addBestsellerData($row);
            }
        }

        if (!count($this->_customData)) {
            return $this;
        }

        $key = $this->getFilter('reload_key');
        if ($key === 'qty') {
            # Sort data
            usort($this->_customData, array(&$this, "_compareQtyElements"));
            # Splice array
            array_splice(
                $this->_customData, $this->getCustomOption('advancedreports_bestsellers_options_bestsellers_count')
            );

            # All qty
            $qty = 0;
            foreach ($this->_customData as $d) {
                $qty += $d['sum_qty'];
            }
            foreach ($this->_customData as $i => &$d) {
                $d['order'] = $i + 1;
            }
        } elseif ($key === 'total') {
            //Sort data
            usort($this->_customData, array(&$this, "_compareTotalElements"));
            //Splice array
            array_splice(
                $this->_customData, $this->getCustomOption('advancedreports_bestsellers_options_bestsellers_count')
            );

            //All qty
            $total = 0;
            foreach ($this->_customData as $d) {
                $total += $d['sum_total'];
            }
            foreach ($this->_customData as $i => &$d) {
                $d['order'] = $i + 1;
            }
        } else {
            return $this;
        }
        $this->_preparePage();
        $this->getCollection()->setSize(count($this->_customData));
        Mage::helper('advancedreports')->setChartData(null, Mage::helper('advancedreports')->getDataKey($this->_routeOption));
        parent::_prepareData();
        return $this;
    }

    public function getBestsellerData()
    {
        return $this->_customData;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'order',
            array(
                 'header'   => Mage::helper('reports')->__('N'),
                 'width'    => '60px',
                 'align'    => 'right',
                 'index'    => 'order',
                 'type'     => 'number',
            )
        );

        $this->addColumn(
            'sku',
            array(
                 'header'   => Mage::helper('reports')->__('SKU'),
                 'index'    => 'sku',
                 'type'     => 'text',
            )
        );

        $this->addColumn(
            'name',
            array(
                'header'   => Mage::helper('reports')->__('Product Name'),
                'index'    => 'name',
                'type'     => 'text',
            )
        );

        $this->addColumn(
            'sum_qty',
            array(
                'header'   => $this->__('Quantity'),
                'width'    => '120px',
                'align'    => 'right',
                'index'    => 'sum_qty',
                'renderer' => 'advancedreports/widget_grid_column_renderer_percent',
                'total'    => 'sum',
                'type'     => 'number',
            )
        );

        $this->addColumn(
            'sum_total',
            array(
                'header'        => Mage::helper('reports')->__('Total'),
                'width'         => '120px',
                'type'          => 'currency',
                'currency_code' => $this->getCurrentCurrencyCode(),
                'renderer'      => 'advancedreports/widget_grid_column_renderer_percent',
                'total'         => 'sum',
                'index'         => 'sum_total',
            )
        );

        $this->addProfitColumns();

        $this->addColumn(
            'action',
            array(
                'header'   => Mage::helper('catalog')->__('Action'),
                'width'    => '50px',
                'type'     => 'action',
                'align'    => 'right',
                'getter'   => 'getProductId',
                'actions'  => array(
                    array(
                        'caption' => $this->__('View'),
                        'url'     => array(
                            'base'   => 'adminhtml/catalog_product/edit',
                            'params' => array(),
                        ),
                        'field'   => 'id',
                    )
                ),
                'filter'   => false,
                'sortable' => false,
                'index'    => 'stores',
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

    public function getRowUrl($row)
    {
        //return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId() ));
    }
}