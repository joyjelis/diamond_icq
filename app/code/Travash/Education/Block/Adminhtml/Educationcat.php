<?php
namespace Travash\Education\Block\Adminhtml;

/**
 * Class Educationcat
 * @package Travash\Education\Block\Adminhtml
 */
class Educationcat extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_educationcat';
        $this->_blockGroup = 'Travash_Education';
        $this->_headerText = __('Manage Education Category');
        $this->_addButtonLabel = __('Add New Category');
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
