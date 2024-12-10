<?php
namespace Travash\Customization\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class Data
 * @package Travash\Customization\Helper
 */
class Data extends AbstractHelper
{
        /**
         * @var GetStockItemDataInterface
         */
    private $getStockItemData;

    /**
     * @var GetReservationsQuantityInterface
     */
    private $getReservationsQuantity;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var StockRegistryInterface
     */
    private $stockRepository;

    /**
     * @param Context $context
     * @param GetStockItemDataInterface $getStockItemData
     * @param GetReservationsQuantityInterface $getReservationsQuantity
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param StockRegistryInterface $stockRepository
     */
    public function __construct(
        Context $context,
        GetStockItemDataInterface $getStockItemData,
        GetReservationsQuantityInterface $getReservationsQuantity,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        StockRegistryInterface $stockRepository
    ) {
        $this->getStockItemData = $getStockItemData;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->stockRepository = $stockRepository;
        parent::__construct($context);
    }

    /**
     * Check If Product is In Stock
     *
     * @param $product
     * @return bool
     */
    public function isInStock($product)
    {
        try {
            $sku = $product->getSku();
            $stockId = $this->getStockId($product);
            $stockItemData = $this->getStockItemData->execute($sku, $stockId);
            if (null === $stockItemData) {
                return false;
            }

            $stockItemConfig = $this->getStockItemConfiguration->execute($sku, $stockId);
            $reservationQty = $stockItemData[GetStockItemDataInterface::QUANTITY] +
                $this->getReservationsQuantity->execute($sku, $stockId);
            $qtyLeftInStock = $reservationQty - $stockItemConfig->getMinQty();
            $isInStock = bccomp((string) $qtyLeftInStock, "1", 4) >= 0;
            $isEnoughQty = (bool) $stockItemData[GetStockItemDataInterface::IS_SALABLE] && $isInStock;
            if (!$isEnoughQty) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get Product Stock ID
     *
     * @param $product
     * @return int
     */
    public function getStockId($product)
    {
        try {
            $productId = $product->getId();
            $productStock = $this->stockRepository->getStockItem($productId);
            return $productStock->getStockId();
        } catch (Exception $e) {
            return 1;
        }
    }
}
