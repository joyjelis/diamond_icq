<?php
namespace Travash\Categories\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;
    
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        $categorySetup->removeAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_video'
        );
        $categorySetup->removeAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_short_description'
        );
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_video',
            [
                'type' => 'varchar',
                'label' => 'Upload Video(mp4)',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 333,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
            ]
        );
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_short_description',
            [
                'type' => 'text',
                'label' => 'Short Description',
                'input' => 'textarea',
                'sort_order' => 333,
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
                'backend' => ''
            ]
        );
        $installer->endSetup();
    }
}
