<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('bcp_item_option')} 
  ADD override_price varchar(255);
");

$installer->endSetup();