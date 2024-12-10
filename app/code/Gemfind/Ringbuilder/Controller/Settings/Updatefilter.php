<?php

namespace Gemfind\Ringbuilder\Controller\Settings;


use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\Controller\Result\JsonFactory;

class Updatefilter extends \Magento\Framework\App\Action\Action
{
    /**
        * @var \Magento\Framework\View\Result\PageFactory
        */
    protected $resultPageFactory;

    /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
    * Loadfilter constructor.
    * @param \Magento\Framework\App\Action\Context $context
    * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
    * @param \Gemfind\Ringbuilder\Helper\Data $helper
    * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Helper $helper,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
    * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
    */
    public function execute()
    {
        
        if (!$this->helper->isGemfindEnabled()) {
            $this->messageManager->addError(__("Please enable this Extension, go to configuration."));
            $this->_redirect('/');
        }

        if (!$this->helper->getUsername()) {
            $this->messageManager->addError(__("Please add Gemfind UserID in Extension Configuration"));
            $this->_redirect('/');
        }

        $hiddenmetaltype = $hiddencollection = $hiddenshape = [];

        $ringFilters = [$this->helper->getRingFilters()];

        foreach ($ringFilters as $value) {
            $value = (array) $value;
            foreach ($value['collections'] as $collection) {
                $collection = (array) $collection;
                if ($collection['isActive'] == 0) {
                    $hiddencollection[] = '#' . strtolower(str_replace(' ', '', $collection['collectionName']));
                }
            }

            foreach ($value['shapes'] as $shape) {
                $shape = (array) $shape;
                if ($shape['isActive'] == 0) {
                    $hiddenshape[] = '#' . strtolower(str_replace(' ', '', $shape['shapeName']));
                }
            }

            foreach ($value['metalType'] as $metaltype) {
                $metaltype = (array) $metaltype;
                if ($metaltype['isActive'] == 0) {
                    $hiddenmetaltype[] = '#ring_metal_' . strtolower(str_replace(' ', '', $metaltype['metalType']));
                }
            }
        }

        $hiddenshape = implode(',', $hiddenshape);
        $hiddencollection = implode(',', $hiddencollection);
        $hiddenmetaltype = implode(',', $hiddenmetaltype);
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();
        $result->setData(['hiddenshape' => $hiddenshape,'hiddencollection' => $hiddencollection,'hiddenmetaltype' => $hiddenmetaltype]);
        return $result;
    }
}
