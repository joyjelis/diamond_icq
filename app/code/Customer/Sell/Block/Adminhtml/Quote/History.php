<?php
namespace Customer\Sell\Block\Adminhtml\Quote;

use Customer\Sell\Helper\Data as QuoteHelper;

class History extends \Magento\Backend\Block\Template
{

    protected $_template = 'history.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        QuoteHelper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->helper = $helper;
    }

    public function getHistory()
    {
        $sellId = $this->request->getParam('sell_id');
        if ($sellId) {
            return $this->helper->getHistory($sellId);
        }

        return [];
    }
}
