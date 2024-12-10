<?php
namespace Magneto\Custom\Block\Customer;

class Wishlist extends \Magento\Wishlist\Block\Customer\Wishlist
{
    
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Wish List'));
        
        return $this;
    }

}
