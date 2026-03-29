<?php

namespace Iovista\CustomCanonical\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class AddCanonicalUrlCategoryAttribute implements DataPatchInterface
{
    protected $moduleDataSetup;
    protected $eavSetupFactory;
    protected $eavConfig;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        EavConfig $eavConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(Category::ENTITY, 'canonical_tag', [
            'type' => 'text',
            'label' => 'Canonical Tag',
            'input' => 'text',
            'required' => false,
            'sort_order' => 60,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Search Engine Optimization',
            'visible' => true,
            'user_defined' => true,
            'default' => '',
            'note' => 'Custom canonical URL for this category',
        ]);

        $attribute = $this->eavConfig->getAttribute(Category::ENTITY, 'canonical_tag');
        $attribute->setData('used_in_forms', ['adminhtml_category']);
        $attribute->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies() { return []; }
    public function getAliases() { return []; }
}
