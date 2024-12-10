<?php
namespace Travash\Education\Block\Adminhtml;

/**
 * Class Education
 * @package Travash\Education\Block\Adminhtml
 */
class Education extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_education';
        $this->_blockGroup = 'Travash_Education';
        $this->_headerText = __('Manage Education');
        $this->_addButtonLabel = __('Add New Education');
        parent::_construct();
    }

     /**
      * @return mixed
      */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
