<?php
 
namespace Customer\Sell\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Customer\Sell\Model\Sell;
use Magento\Framework\HTTP\Client\Curl;

class Whatsapp extends AbstractHelper
{

    const WHATSAPP_API = 'https://live-server-1790.wati.io/api/v1/sendTemplateMessage?whatsappNumber=';

    protected $curl;
    protected $deploymentConfig;
    private $sell;
    private $notificationTemplate;
    private $messageData;

    const WATI_APIKEY = 'wati/key';

    public function __construct(
        Curl $curl,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig
    ) {
        $this->curl = $curl;
        $this->deploymentConfig = $deploymentConfig;
    }

    public function setData($data)
    {
        $this-> messageData = $data;
    }

    private function getData()
    {
        return $this->messageData;
    }

    private function isNewOfferLabel()
    {
        return $this->sell->getStatus() == Sell::NEW_OFFER_LABEL;
    }

    private function getPrice()
    {
        return $this->isNewOfferLabel() ? $this->sell->getOfferPrice() : $this->sell->getPrice();
    }

    private function removeSymbol($data)
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '', $data);
    }

    public function setNotificationTemplate($data)
    {
        return $this->notificationTemplate = $data;
    }
    private function getNotificationTemplate()
    {
        return $this->notificationTemplate;
    }

    public function sendMessage(Sell $sell)
    {
        $this->sell = $sell;
        if ($this->sell->getMobile()) {
            if ($this->isNewTradeRequest()) {
                $this->sendNewTradeRequestNotification();
            }
            if ($this->isSendPriceUpdateStatus()) {
                $this->sendPriceUpdateNotification();
            }
        }
        return true;
    }

    private function sendNewTradeRequestNotification()
    {
        $this->setNotificationTemplate('sell_diamond_normal_notification');
        $this->setData([
            "parameters" => [
                [
                    'name' => 'name',
                    'value' => $this->sell->getName()
                ],
                [
                    'name' => 'jewellery_type',
                    'value' => $this->sell->getJewelleryType()
                ],
                [
                    'name' => 'quote',
                    'value' => $this->sell->getQuote()
                ]
            ],
            'broadcast_name' => 'Diamond ICQ',
            'template_name' => $this->getNotificationTemplate()
        ]);
        $this->process();
    }

    private function sendPriceUpdateNotification()
    {
        $this->setNotificationTemplate('notification_common_price_update');
        $this->setData([
            "parameters" => [
                [
                    'name' => 'name',
                    'value' => $this->sell->getName()
                ],
                [
                    'name' => 'jewellery_type',
                    'value' => $this->sell->getJewelleryType()
                ],
                [
                    'name' => 'quote',
                    'value' => $this->sell->getQuote()
                ],
                [
                    'name' => 'price',
                    'value' => strip_tags($this->getPrice())
                ]
            ],
            'broadcast_name' => 'Diamond ICQ',
            'template_name' => $this->getNotificationTemplate()
        ]);
        $this->process();
    }

    private function isNewTradeRequest()
    {
        return $this->sell->getStatus() == Sell::NEW_TRADE_REQUEST_LABEL;
    }

    private function isSendPriceUpdateStatus()
    {
        return $this->sell->getStatus() == Sell::QUALIFY_LABEL || $this->sell->getStatus() == Sell::NEW_OFFER_LABEL;
    }

    public function process()
    {
        $url = self::WHATSAPP_API . $this->removeSymbol($this->sell->getMobile());
        $params = $this->getData();
        $this->curl->addHeader("Authorization", $this->deploymentConfig->get(self::WATI_APIKEY));
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->post($url, json_encode($params));
    }
}
