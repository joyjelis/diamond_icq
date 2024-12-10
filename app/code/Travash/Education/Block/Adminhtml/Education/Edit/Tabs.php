<?php
namespace Travash\Education\Block\Adminhtml\Education\Edit;

/**
 * Class Tabs
 * @package Travash\Education\Block\Adminhtml\Education\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('education_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Education Information'));
    }
}
