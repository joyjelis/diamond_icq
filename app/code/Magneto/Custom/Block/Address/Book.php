<?php
namespace Magneto\Custom\Block\Address;

class Book extends \Magento\Customer\Block\Address\Book
{
    /**
     * Prepare the Address Book section layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {        
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Addresses'));

        return $this;
    }
}