<?php

class MageWare_SalesGrids_Model_Resource_Order_Invoice
    extends Mage_Sales_Model_Resource_Order_Invoice
{
    public function getUpdateGridRecordsSelect($ids, &$flatColumnsToSelect, $gridColumns = null)
    {
        $select = parent::getUpdateGridRecordsSelect($ids, $flatColumnsToSelect, $gridColumns);

        $select->group('main_table.' . $this->getIdFieldName());

        return $select;
    }

    public function updateOnRelatedRecordChanged($field, $entityId)
    {
        $adapter = $this->_getWriteAdapter();
        $column = array();

        $select = $adapter->select()
            ->from(array('main_table' => $this->getMainTable()), $column)
            ->where('main_table.' . $field .' = ?', $entityId)
            ->group('main_table.' . $this->getIdFieldName());

        $this->joinVirtualGridColumnsToSelect('main_table', $select, $column);
        $fieldsToUpdate = $adapter->fetchRow($select);

        if ($fieldsToUpdate) {
            return $adapter->update(
                $this->getGridTable(),
                $fieldsToUpdate,
                $adapter->quoteInto($this->getGridTable() . '.' . $field . ' = ?', $entityId)
            );
        }

        return 0;
    }
}
