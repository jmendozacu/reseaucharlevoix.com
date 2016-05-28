// <?php
// $installer = $this;

// $installer->startSetup();
// $attr = Mage::getResourceModel('catalog/eav_attribute')
//     ->loadByCode('catalog_product','number_of_drawers');

// if (!$attr->getId()) {
//     $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'number_of_drawers', array(
//         'group'            => 'General',
//         'type'             => 'int',
//         'input'            => 'text',
//         'backend'          => '',
//         'frontend_class'   => 'validate-number',
//         'class'            => 'validate-number',
//         'label'            => 'Number of Drawers',
//         'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//         'visible'          => true,
//         'default'          => 4,
//         'required'         => false,
//         'user_defined'     => true,
//         'searchable'       => false,
//         'filterable'       => false,
//         'comparable'       => false,
//         'visible_on_front' => false,
//         'unique'           => false,
//         'apply_to'         => 'bundle',
//         'is_configurable'  => false,
//     ));
// }
// $attr = Mage::getResourceModel('catalog/eav_attribute')
//     ->loadByCode('catalog_product','size_per_drawer');
// if (!$attr->getId()) {
//     $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'size_per_drawer', array(
//         'group'            => 'General',
//         'type'             => 'int',
//         'input'            => 'text',
//         'backend'          => '',
//         'frontend_class'   => 'validate-number',
//         'class'            => 'validate-number',
//         'label'            => 'Size of Drawer',
//         'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//         'visible'          => true,
//         'default'          => '4',
//         'required'         => false,
//         'user_defined'     => true,
//         'searchable'       => false,
//         'filterable'       => false,
//         'comparable'       => false,
//         'visible_on_front' => false,
//         'unique'           => false,
//         'apply_to'         => 'bundle',
//         'is_configurable'  => false,
//     ));
// }
// $attr = Mage::getResourceModel('catalog/eav_attribute')
//     ->loadByCode('catalog_product','drawer_info');
// if (!$attr->getId()){
//     $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'drawer_info', array(
//         'group'            => 'Bundle',
//         'type'             => 'text',
//         'input'            => 'text',
//         'backend'          => '',
//         'label'            => 'Drawer Info',
//         'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//         'visible'          => true,
//         'default'          => '{}',
//         'required'         => false,
//         'user_defined'     => true,
//         'searchable'       => false,
//         'filterable'       => false,
//         'comparable'       => false,
//         'visible_on_front' => false,
//         'unique'           => false,
//         'apply_to'         => 'bundle',
//         'is_configurable'  => false,
//         'input_renderer'    => 'toolbox/render_info',//
//     ));

// }


// $installer->endSetup();