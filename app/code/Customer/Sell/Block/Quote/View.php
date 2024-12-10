<?php

namespace Customer\Sell\Block\Quote;

use Customer\Sell\Helper\Data as QuoteHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template\Context;

class View extends \Magento\Framework\View\Element\Template
{

    protected $_sellItem;

    public function __construct(
        Context $context,
        RequestInterface $request,
        QuoteHelper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->helper = $helper;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Quote Details | DiamondICQ'));
        return $this;
    }

    public function getNextStep()
    {
        $item = $this->getSellitem();
        $status = $item->getStatus();
        return $this->helper->getNextStep($status);
    }

    public function getCountries($defValue = "HK", $name = 'country_id', $id = 'country', $title = 'Country')
    {
        return $this->helper->getCountries($defValue, $name, $id, $title);
    }

    public function getAddress()
    {
        $sellId = $this->request->getParam('id');
        return $this->helper->getAddress($sellId);
    }

    public function getRegion()
    {
        return $this->helper->getRegion();
    }

    public function getSellId()
    {
        return $this->request->getParam('id');
    }

    public function getSellitem()
    {
        if (!$this->_sellItem) {
            $sellId = $this->request->getParam('id');
            $this->_sellItem = $this->helper->getSellitem($sellId);
        }

        return $this->_sellItem;
    }

    public function getItemStatus()
    {
        $item = $this->getSellitem();
        $status = $item->getStatus();
        return $this->helper->getItemStatus($status);
    }

    public function getStatusId()
    {
        $item = $this->getSellitem();
        return $item->getStatus();
    }

    public function getFAQHtml()
    {
        $url = "<a href='" . $this->getFAQLink() . "'>" . __('Help') . "</a>";
        return __("If you have any questions, please see our %1 section.", $url);
    }

    public function getFAQLink()
    {
        return $this->getUrl('faq', ['_secure' => true]);
    }

    public function getFormAction()
    {
        return $this->getUrl('*/*/save', ['_secure' => true]);
    }

    public function getHistoryUrl()
    {
        $param = ['id' => $this->getSellId()];
        $url = $this->getUrl('*/*/history', $param);
        return $url;
    }

    public function getQuoteUrl()
    {
        $param = ['id' => $this->getSellId()];
        $url = $this->getUrl('*/*/view', $param);
        return $url;
    }

    public function getCountryUrl()
    {
        return $this->getUrl('*/country/index', ['_secure' => true]);
    }

    public function getCancel($id)
    {
        return $this->getUrl('*/*/cancel', ['_secure' => true, 'id' => $id]);
    }

    public function getAddressSave()
    {
        return $this->getUrl('*/country/save', ['_secure' => true]);
    }

    public function getSaveQuote()
    {
        return $this->getUrl('*/quote/save', ['_secure' => true]);
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', ['_secure' => true]);
    }

    public function getGalleryImages()
    {
        $item = $this->getSellitem();
        $image = $item->getImage();
        $images = [];
        if ($image) {
            $image = explode(',', $image);
            if (count($image) > 0) {
                $maxcount = count($image);
                foreach ($image as $key => $img) {
                    $imagecount = $key + 1;
                    $imgurl = $this->generateImageUrl($img, $item->getData('sell_id'));
                    $images[] = [
                        'thumb' => $imgurl,
                        'img' => $imgurl,
                        'full' => $imgurl,
                        'caption' => $imagecount . '/' . $maxcount,
                    ];
                }
            }
        } else {
            $imgurl = $this->generateImageUrl(rand(), $item->getData('sell_id'));
            $images[] = [
                'thumb' => $imgurl,
                'img' => $imgurl,
                'full' => $imgurl,
            ];
        }

        return json_encode($images);
    }

    public function generateImageUrl($img, $sellId)
    {
        return $this->getUrl('*/*/loadimage', ['_secure' => true, 'load' => base64_encode(json_encode([$img, $sellId]))]);
    }

    public function getHistory()
    {
        return $this->helper->getHistory($this->getSellId());
    }

    public function formatSellPrice($price)
    {
        return $this->helper->getCurrencyWithFormat($price);
    }

    public function getJsConfig()
    {
        $jsconfig = $this->getSellitem()->getData();
        return json_encode($jsconfig);
    }
}
