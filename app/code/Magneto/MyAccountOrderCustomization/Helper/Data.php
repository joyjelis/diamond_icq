<?php
namespace Magneto\MyAccountOrderCustomization\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $session;

    /**
     * @var UrlInterface|null
     */
    private $url;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        UrlInterface $url = null
    ) {
        $this->timezoneInterface = $timezoneInterface;
        $this->productModel = $productModel;
        $this->imageHelper = $imageHelper;
        $this->orderRepository = $orderRepository;

        $this->url = $url ?: ObjectManager::getInstance()->get(UrlInterface::class);

        parent::__construct($context);
    }

    public function showOrderCreatedAtDateFormat($currentDate)
    {
        $finalDateFormat = 'd M Y';
        /*
        $orderCreatedAtDate = $this->timezoneInterface->formatDate(
            $currentDate,
            \IntlDateFormatter::FULL,
            false
        );
        */
        $orderCreatedAtDate = $this->timezoneInterface->date(new \DateTime($currentDate))->format($finalDateFormat);
        return $orderCreatedAtDate;
    }
    public function getProductImage($productId)
    {
        $productObj = $this->productModel->load($productId);
        $imageUrl = $this->imageHelper->init($productObj, 'small_image', ['type'=>'small_image'])
                    ->keepAspectRatio(true)->resize('155', '155')->getUrl();
        return $imageUrl;
    }
    public function getOrderDetails($orderId)
    {
        $currentOrder = $this->orderRepository->get($orderId);
         return $currentOrder;
    }

    /**
     * Retrieve tracking url with params
     *
     * @param  string $key
     * @param  \Magento\Sales\Model\Order
     * |\Magento\Sales\Model\Order\Shipment|\Magento\Sales\Model\Order\Shipment\Track $model
     * @param  string $method Optional - method of a model to get id
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {
        $urlPart = "{$key}:{$model->{$method}()}:{$model->getProtectCode()}";
        $params = [
            '_scope' => $model->getStoreId(),
            '_nosid' => true,
            '_direct' => 'shipping/tracking/popup',
            '_query' => ['hash' => $this->urlEncoder->encode($urlPart)]
        ];

        return $this->url->getUrl('', $params);
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($model)
    {
        if ($model instanceof \Magento\Sales\Model\Order) {
            return $this->_getTrackingUrl('order_id', $model);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment) {
            return $this->_getTrackingUrl('ship_id', $model);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment\Track) {
            return $this->_getTrackingUrl('track_id', $model, 'getEntityId');
        }
        return '';
    }
}
