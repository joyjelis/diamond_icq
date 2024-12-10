<?php
namespace MageArray\Faq\Block\Links;

/**
 * Class Link
 * @package MageArray\Faq\Block\Links
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{

    /**
     * @var \MageArray\Faq\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Link constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageArray\Faq\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\Faq\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @return mixed
     */
    public function getHref()
    {
        if ($this->_dataHelper->isEnabled() == 1) {
            $pageUrl = $this->_dataHelper->getPageUrl();
            return $this->getUrl($pageUrl);
        }
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->_dataHelper->getLinkTitle();
    }
}
