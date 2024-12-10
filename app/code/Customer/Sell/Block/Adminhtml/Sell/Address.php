<?php
namespace Customer\Sell\Block\Adminhtml\Sell;

class Address extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'address.phtml';

    protected $request;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Customer\Sell\Helper\Data $helper,
        \Customer\Sell\Model\SellFactory $sellFactory,
        array $data = []
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->sellFactory = $sellFactory;
        parent::__construct($context, $data);
    }

    public function getAddress()
    {
        $params = $this->request->getParams();
        return $this->helper->getAddress($params['sell_id']);
    }

    public function getSellitemData()
    {
        $params = $this->request->getParams();
        $sellItems = $this->sellFactory->create()->load($params['sell_id']);
        return $sellItems->getData();
    }
}
