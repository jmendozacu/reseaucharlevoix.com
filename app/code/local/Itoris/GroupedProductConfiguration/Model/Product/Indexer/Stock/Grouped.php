<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_GROUPEDPRODUCTS
 * @copyright  Copyright (c) 2013 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */



class Itoris_GroupedProductConfiguration_Model_Product_Indexer_Stock_Grouped extends Mage_CatalogInventory_Model_Resource_Indexer_Stock_Grouped {

    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false) {
        if ($this->getDataHelper()->getSettings()->getEnabled() && $this->getDataHelper()->isAdminRegistered()) {
            $adapter  = $this->_getWriteAdapter();
            $idxTable = $usePrimaryTable ? $this->getMainTable() : $this->getIdxTable();
            $select   = $adapter->select()
                ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'));
            $this->_addWebsiteJoinToSelect($select, true);
            $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
            $select->columns('cw.website_id')
                ->join(
                    array('cis' => $this->getTable('cataloginventory/stock')),
                    '',
                    array('stock_id'))
                ->joinLeft(
                    array('cisi' => $this->getTable('cataloginventory/stock_item')),
                    'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id',
                    array())
                ->joinLeft(
                    array('l' => $this->getTable('catalog/product_link')),
                    'e.entity_id = l.product_id AND l.link_type_id=' . Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
                    array())
                ->joinLeft(
                    array('le' => $this->getTable('catalog/product')),
                    'le.entity_id = l.linked_product_id',
                    array())
                ->joinLeft(
                    array('i' => $idxTable),
                    'i.product_id = l.linked_product_id AND cw.website_id = i.website_id AND cis.stock_id = i.stock_id',
                    array())
                ->columns(array('qty' => new Zend_Db_Expr('0')))
                ->where('cw.website_id != 0')
                ->where('e.type_id = ?', $this->getTypeId())
                ->group(array('e.entity_id', 'cw.website_id', 'cis.stock_id'));

            // add limitation of status
            $psExpr = $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id');
            $psCond = $adapter->quoteInto($psExpr . '=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

            if ($this->_isManageStock()) {
                $statusExpr = $adapter->getCheckSql('cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 0',
                    1, 'cisi.is_in_stock');
            } else {
                $statusExpr = $adapter->getCheckSql('cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 1',
                    'cisi.is_in_stock', 1);
            }

            $optExpr = $adapter->getCheckSql("{$psCond}", 'i.stock_status', 0);
            $stockStatusExpr = $adapter->getLeastSql(array("MAX({$optExpr})", "MIN({$statusExpr})"));

            $select->columns(array(
                'status' => $statusExpr
            ));

            if (!is_null($entityIds)) {
                $select->where('e.entity_id IN(?)', $entityIds);
            }

            return $select;
        } else {
            return parent::_getStockStatusSelect($entityIds = null);
        }
    }

    /**
     * @return Itoris_GroupedProductConfiguration_Helper_Data
     */
    public function getDataHelper() {
        return Mage::helper('itoris_groupedproductconfiguration');
    }
}
?>