<?php
namespace MageArray\Faq\Block\Adminhtml;

/**
 * Class Import
 * @package MageArray\Faq\Block\Adminhtml
 */
class Import extends \Magento\Backend\Block\Template
{
    /**
     * @return mixed
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getImageMediaUrl()
    {
        return $this->getViewFileUrl('MageArray_Faq::faq.csv');
    }

    /**
     * @return mixed
     */
    public function getCategoryMediaUrl()
    {
        return $this->getViewFileUrl('MageArray_Faq::category.csv');
    }
}
