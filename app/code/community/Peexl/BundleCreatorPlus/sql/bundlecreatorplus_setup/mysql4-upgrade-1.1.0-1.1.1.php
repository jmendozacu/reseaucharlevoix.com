<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('bcp_item')} 
  ADD optional SMALLINT(1) default 0 AFTER sort_order;
");

$installer->endSetup();
