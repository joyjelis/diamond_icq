<?php

namespace DiamondIcq\Tinymce\Plugin;

class Config
{
    protected $activeEditor;

    public function __construct(\Magento\Ui\Block\Wysiwyg\ActiveEditor $activeEditor)
    {
        $this->activeEditor = $activeEditor;
    }

    public function afterGetConfig(
        \Magento\Ui\Component\Wysiwyg\ConfigInterface $configInterface,
        \Magento\Framework\DataObject $result
    ) {
        $editor = $this->activeEditor->getWysiwygAdapterPath();

        if (strpos($editor, 'tinymce4Adapter') !== false) {
            if (($result->getDataByPath('settings/menubar'))
                || ($result->getDataByPath('settings/toolbar'))
                || ($result->getDataByPath('settings/plugins'))
                ) {
                return $result;
            }

            $settings = $result->getData('settings');

            if (!is_array($settings)) {
                $settings = [];
            }

            $settings['menubar'] = true;

            $result->setData('settings', $settings);
            return $result;
        } else {
            return $result;
        }
    }
}
