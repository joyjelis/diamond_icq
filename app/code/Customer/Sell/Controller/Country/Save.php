<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_Quote
 */

namespace Customer\Sell\Controller\Country;

use Customer\Sell\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Framework\App\Action\Action
{

    protected $resultJsonFactory;

    protected $regionColFactory;

    /**
     * Constructor
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $result = $this->resultJsonFactory->create();
        if (isset($data['id'])) {
            $sellId = $data['id'];
            unset($data['id']);
            $success = $this->helper->storeAddress($sellId, $data);
            if ($success) {
                return $result->setData([
                    'success' => true,
                    'address_html' => $this->helper->getAddress($sellId),
                ]);
            }
        }

        return $result->setData(['success' => false]);
    }
}
