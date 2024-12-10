<?php

namespace Magneto\CurrencyChangeTab\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // Dropdown Field
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'preferred_currency', [
            'label' => 'Preferred Currency',
            'system' => 0,
            'position' => 710,
            'sort_order' => 710,
            'required' => false,
            'visible' => true,
            'note' => '',
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'Magneto\CurrencyChangeTab\Model\Source\CurrencyOption',
            ]);

        $this->getEavConfig()->getAttribute('customer', 'preferred_currency')->setData('is_user_defined', 1)
            ->setData('default_value', '')->setData('used_in_forms', ['adminhtml_customer','customer_account_edit'])
            ->save();
    }
    
    public function getEavConfig()
    {
        return $this->eavConfig;
    }
}
