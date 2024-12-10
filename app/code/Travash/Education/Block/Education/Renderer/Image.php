<?php
namespace Travash\Education\Block\Education\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Shopbrand factory.
     *
     * @var \Travash\Education\Model\EducationFactory
     */
    protected $_modelEducationFactory;
 

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Context              $context
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param \Travash\Education\Model\EducationFactory $modelEducationFactory
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Travash\Education\Model\EducationFactory $modelEducationFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_modelEducationFactory  = $modelEducationFactory;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        echo ';;;';
        $storeViewId = $this->getRequest()->getParam('store');
        $itm = $this->_modelEducationFactory->create()->setStoreViewId($storeViewId)->load($row->getId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . $itm->getImg();

        return '<image width="150" height="50" src ="'.$srcImage.'" alt="'.$itm->getTitle().'" >';
    }
}
