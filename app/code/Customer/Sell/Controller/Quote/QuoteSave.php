<?php
declare (strict_types = 1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Customer\Sell\Controller\Quote;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Framework\Message\ManagerInterface;
use Customer\Sell\Model\SellFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class QuoteSave extends Action\Action
{
    /**
     * @var JsonFactory
     */
    protected $_jsonFactory;

    /**
     * @var UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var Filesystem
     */
    protected $_fileSystem;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var SellFactory
     */
    protected $_sellFactory;

    /**
     * Quote Save Constructor
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $fileSystem
     * @param CustomerSession $customerSession
     * @param UrlInterface $urlInterface
     * @param ManagerInterface $messageManager
     * @param SellFactory $sellFactory
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        UploaderFactory $uploaderFactory,
        Filesystem $fileSystem,
        CustomerSession $customerSession,
        UrlInterface $urlInterface,
        ManagerInterface $messageManager,
        SellFactory $sellFactory
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_fileSystem = $fileSystem;
        $this->_customerSession = $customerSession;
        $this->_urlInterface = $urlInterface;
        $this->_messageManager = $messageManager;
        $this->_sellFactory = $sellFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /* @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->_jsonFactory->create();
        $resultJson = [];

        $params = $this->getRequest()->getParams();
        if ($params) {
            $title = null;
            if (isset($params['title'])) {
                $title = $params['title'];
            }

            $name = null;
            if (isset($params['name'])) {
                $name = $params['name'];
            }

            $jewelleryType = null;
            if (isset($params['jewellery_type'])) {
                $jewelleryType = $params['jewellery_type'];
            }

            $phoneNumber = null;
            if (isset($params['phone_number'])) {
                $phoneNumber = $params['phone_number'];
            }

            $remarks = null;
            if (isset($params['remarks'])) {
                $remarks = $params['remarks'];
            }

            try {
                $i = 0;
                $data = [];
                $images = $this->getRequest()->getFiles('image');
                if (!empty($images) && count($images)) {
                    foreach ($images as $files) {
                        if (isset($files['tmp_name']) && strlen($files['tmp_name']) > 0) {
                            try {
                                $uploaderFactory = $this->_uploaderFactory->create(['fileId' => $images[$i]]);
                                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                                $uploaderFactory->setAllowRenameFiles(true);
                                $uploaderFactory->setFilesDispersion(false);
                                $mediaDirectory = $this->_fileSystem->getDirectoryRead(DirectoryList::ROOT);
                                $destinationPath = $mediaDirectory->getAbsolutePath('sell/dimages');
                                $fileResult = $uploaderFactory->save($destinationPath);
                                if ($fileResult['error'] == 0) {
                                    $data[] = preg_replace('/\s+/', '_', basename($uploaderFactory->getUploadedFileName()));
                                }
                            } catch (\Exception $e) {
                                $this->_messageManager->addErrorMessage(__($e->getMessage()));
                            }
                        }
                        $i++;
                    }
                }
				
                $model = $this->_sellFactory->create();
                $name = $title.' '.$name;
                $model->setName($name);
                $model->setJewelleryType($jewelleryType);
                if ($data) {
                    $model->setImage(implode(',', $data));
                    $model->setCertificate(1);
                }
                $model->setMobile($phoneNumber);
                $model->setCertificateRemark($remarks);
                $model->save();

                $quoteId = $model->getQuote();
                $this->_customerSession->setQuote($quoteId);

                $redirectUrl = $this->_urlInterface->getUrl('sell-your-jewellery/quote/complete');
                $this->_messageManager->addSuccessMessage(__('Your quote submitted successfully!'));
                $resultJson = [
                    'status' => 'success',
                    'url' => $redirectUrl
                ];
            } catch (\Exception $e) {
                $this->_messageManager->addErrorMessage(__($e->getMessage()));
                $message = __('Something went wrong while Create An Quote.');
                $resultJson = [
                    'status' => 'error',
                    'message' => $message
                ];
            }
        }
        return $result->setData($resultJson);
    }
}
