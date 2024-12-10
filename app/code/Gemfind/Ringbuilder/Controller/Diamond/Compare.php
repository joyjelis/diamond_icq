<?php



namespace Gemfind\Ringbuilder\Controller\Diamond;



use Gemfind\Ringbuilder\Helper\Data as Helper;



class Compare extends \Magento\Framework\App\Action\Action

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

     * Index Constructor

     *

     * @param \Magento\Framework\App\Action\Context  $context

     * @param \Gemfind\Ringbuilder\Helper\Data  $helper

     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory

     */

    public function __construct(

        \Magento\Framework\App\Action\Context $context,

        Helper $helper,

        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    ) {

        $this->helper = $helper;

        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);

    }



    /**

     * Execute view action

     *

     * @return \Magento\Framework\Controller\ResultInterface

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

        

        if (isset($_COOKIE['diamondProduct'])) {

            $resultPage = $this->resultPageFactory->create();

            return $resultPage;

        } else {

            $this->messageManager->addError(__("No Diamond's found for comparison"));

            $this->_redirect('ringbuilder/diamond');

        }

        

    }

}

