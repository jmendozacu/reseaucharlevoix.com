<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('bcp_item')} 
  ADD description text AFTER title;
");

$installer->endSetup();
