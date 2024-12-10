<?php
namespace Travash\Education\Block\Adminhtml\Educationcat\Edit;

/**
 * Class Tabs
 * @package Travash\Education\Block\Adminhtml\Educationcat\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('educationcat_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Information'));
    }
}
