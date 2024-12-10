<?php
namespace Magneto\IncludePriceReceipt\Block\Adminhtml\Order\View;

use Magneto\IncludePriceReceipt\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\View\Element\Template;

class customOrderEmail extends Template
{
    protected $coreRegistry = null;

    public function __construct(
        TemplateContext $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->_isScopePrivate = true;
        $this->_template = 'order/view/customOrderEmail.phtml';
        parent::__construct($context, $data);
    }
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }


}
