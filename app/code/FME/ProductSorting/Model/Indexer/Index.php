<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_ProductSorting
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ProductSorting\Model\Indexer;

class Index
{
     /**
      * @var \Magento\Framework\DB\Adapter\AdapterInterface
      */
    protected $connection;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    private $action;

    protected $_date;
    protected $_helper;
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;
    /**
     * @var \Magento\Framework\Indexer\ConfigInterface
     */
    protected $config;
   

    /**
     * Index constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\ResourceModel\Product\Action $action
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \FME\ProductSorting\Helper\Data $helper,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Framework\Indexer\ConfigInterface $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ResourceModel\Product\Action $action
    ) {
        $this->resource = $resource;
        $this->_helper = $helper;
        $this->indexerFactory = $indexerFactory;
        $this->config = $config;
        $this->_date = $date;
        $this->action = $action;
    }

    /*
     * Used by mview, allows process indexer in the "Update on schedule" mode
     */


    /*
     * Will take all of the data and reindex
     * Will run when reindex via command line
     */
    public function execute()
    {
        $aa='hellooooo';

        $result = json_encode($aa, true);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customCron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($result);
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($result);

        $connection = $this->_getConnection();
        $orderExclude = null;
        $orderStatus = $this->_helper->getConfig('best_seller/orderStatus');
        if (!empty($orderStatus)) {
            $orderExclude =explode(',', $orderStatus);
        }

        $select = $connection->select()
            ->from(
                array('salestb' => $this->resource->getTableName('sales_order_item')),
                array(
                    'product_id' => 'product_id',
                    'status_order' => 'orderTB.status',
                    'best_seller' => 'SUM(qty_ordered)'
                    
                )
            )
            ->join(
                array('orderTB' => $this->resource->getTableName('sales_order')),
                'orderTB.entity_id = salestb.order_id'
            )
            ->group('salestb.product_id');
       
        $dataDB = $connection->fetchAll($select);
        
        foreach ($dataDB as $item) {
            if ($orderExclude != null) {
                if (in_array($item['status_order'], $orderExclude)) {
                    $this->action->updateAttributes(
                        array($item['product_id']),
                        array('status_order' => ''),
                        0
                    );
                } else {
                    $status = $item['status_order'];
                        $this->action->updateAttributes(
                            array($item['product_id']),
                            array('status_order' => $item['best_seller']),
                            0
                        );
                }
            } else {
                if ($item['status_order'] == null) {
                     $this->action->updateAttributes(
                         array($item['product_id']),
                         array('status_order' => ''),
                         0
                     );
                } else {
                    $status = $item['status_order'];
                    $this->action->updateAttributes(
                        array($item['product_id']),
                        array('status_order' => $item['best_seller']),
                        0
                    );
                }
            }
        }

        //most_viewed

        $select = $connection->select()
            ->from(
                array('reviewIndexTB' => $this->resource->getTableName('report_viewed_product_index')),
                array(
                    'product_id' => 'product_id',
                    'added_at' => 'added_at',
                    'most_viewed' => 'COUNT(reviewIndexTB.product_id)'
                )
            )
            ->join(
                array('productTB' => $this->resource->getTableName('catalog_product_entity')),
                'productTB.entity_id = reviewIndexTB.product_id'
            )
            ->group('reviewIndexTB.product_id');
        $days=null;
        $dataDB = $connection->fetchAll($select);
        $daysConfig = $this->_helper->getConfig('most_viewed/days');
        if (!empty($daysConfig)) {
            $days =(int)$daysConfig;
        }

        $dates = $this->_date->date();
        $currentTime = $dates->getTimestamp();
        foreach ($dataDB as $item) {
            if (is_null($days) != 1) {
                $dbTime = $item['added_at'];
                $dates = $this->_date->date($dbTime);
                $dbDate = $dates->getTimestamp();
                $difference = ($currentTime) - (60*60*24*$days);
                if ($difference <= $dbDate) {
                    $this->action->updateAttributes(
                        array($item['product_id']),
                        array('most_viewed' => $item['most_viewed']),
                        0
                    );
                } else {
                     $this->action->updateAttributes(
                         array($item['product_id']),
                         array('most_viewed' => ''),
                         0
                     );
                }
            } else {
                if ($item['most_viewed'] == 0 || $item['most_viewed'] == '' || $item['most_viewed'] == null) {
                    $this->action->updateAttributes(
                        array($item['product_id']),
                        array('most_viewed' => ''),
                        0
                    );
                } else {
                    $this->action->updateAttributes(
                        array($item['product_id']),
                        array('most_viewed' => $item['most_viewed']),
                        0
                    );
                }
            }
        }

        $select = $connection->select()
            ->from(
                array('reviewTB' => $this->resource->getTableName('review_entity_summary')),
                array(
                    'product_id' => 'entity_pk_value',
                    'review_count' => 'reviews_count'
                )
            )
            ->join(
                array('productTB' => $this->resource->getTableName('catalog_product_entity')),
                'productTB.entity_id = reviewTB.entity_pk_value'
            )
            ->group('reviewTB.entity_pk_value');

        $dataDB = $connection->fetchAll($select);

        foreach ($dataDB as $item) {
            $this->action->updateAttributes(
                array($item['product_id']),
                array('review_count' => $item['review_count']),
                0
            );
        }

        $select = $connection->select()
            ->from(
                array('stockTB' => $this->resource->getTableName('cataloginventory_stock_item')),
                array(
                    'product_id' => 'product_id',
                    'stockquantity' => 'qty'
                )
            )
            ->join(
                array('productTB' => $this->resource->getTableName('catalog_product_entity')),
                'productTB.entity_id = stockTB.product_id'
            )
            ->group('stockTB.product_id');

        $dataDB = $connection->fetchAll($select);

        foreach ($dataDB as $item) {
            $this->action->updateAttributes(
                array($item['product_id']),
                array('stockquantity' => $item['stockquantity']),
                0
            );
        }

        $select = $connection->select()
            ->from(
                array('stockTB' => $this->resource->getTableName('cataloginventory_stock_item')),
                array(
                    'product_id' => 'product_id',
                    'outOfStock' => 'is_in_stock'
                )
            )
            ->join(
                array('productTB' => $this->resource->getTableName('catalog_product_entity')),
                'productTB.entity_id = stockTB.product_id'
            )
            ->group('stockTB.product_id');

        $dataDB = $connection->fetchAll($select);

        foreach ($dataDB as $item) {
            $this->action->updateAttributes(
                array($item['product_id']),
                array('outOfStock' => $item['outOfStock']),
                0
            );
        }
        
///////////////////////////////////////////////////////////////


        $select = $connection->select()
            ->from(
                array('reviewTB' => $this->resource->getTableName('review_entity_summary')),
                array(
                    'product_id' => 'entity_pk_value',
                    'top_rated' => 'rating_summary'
                )
            )
            ->join(
                array('productTB' => $this->resource->getTableName('catalog_product_entity')),
                'productTB.entity_id = reviewTB.entity_pk_value'
            )
            ->group('reviewTB.entity_pk_value');

        $dataDB = $connection->fetchAll($select);

        foreach ($dataDB as $item) {
            $this->action->updateAttributes(
                array($item['product_id']),
                array('top_rated' => $item['top_rated']),
                0
            );
        }

        $select = $connection->select()
            ->from(
                array('priceTB' => $this->resource->getTableName('catalog_product_index_price')),
                array(
                    'product_id' => 'priceTB.entity_id',
                    'saving' => '((priceTB.price - priceTB.final_price) / priceTB.price)'
                )
            )
            ->join(
                array('productTB' => $this->resource->getTableName('catalog_product_entity')),
                'productTB.entity_id = priceTB.entity_id'
            )
            ->group('priceTB.entity_id');

        $dataDB = $connection->fetchAll($select);

        foreach ($dataDB as $item) {
            $this->action->updateAttributes(
                array($item['product_id']),
                array('saving' => $item['saving']),
                0
            );
        }
        
        
        $select = $connection->select()
            ->from(
                array('imageTB' => $this->resource->getTableName('catalog_product_entity_media_gallery_value_to_entity')),
                array(
                    'product_id' => 'imageTB.entity_id',
                    'imageProduct' => 'ISNULL(imageTB.entity_id)'
                )
            )
            ->join(
                array('productTB' => $this->resource->getTableName('catalog_product_entity')),
                'productTB.entity_id = imageTB.entity_id'
            )
            ->group('imageTB.entity_id');

        $dataDB = $connection->fetchAll($select);

        foreach ($dataDB as $item) {
            if ($item['imageProduct'] == '') {
                $id = $item['product_id'];
                    $result = json_encode($id, true);
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testKhali.log');
                 $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($result);
            }

            if ($item['imageProduct'] == 0) {
                $id = $item['product_id'];
                   $result = json_encode($id, true);
                   $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testImageZero.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($result);
            }

            if ($item['imageProduct'] == null) {
                $id = $item['product_id'];
                   $result = json_encode($id, true);
                   $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testImageNull.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($result);
            }

            $this->action->updateAttributes(
                array($item['product_id']),
                array('imageProduct' => $item['imageProduct']),
                0
            );
        }

        $select = $connection->select()
        ->from(
            array('imageTB' => $this->resource->getTableName('catalog_product_entity_media_gallery_value_to_entity')),
            array(
                'product_id' => 'imageTB.entity_id',
                'imageProduct' => 'COUNT(imageTB.entity_id)'
            )
        )
        ->join(
            array('productTB' => $this->resource->getTableName('catalog_product_entity')),
            'productTB.entity_id = imageTB.entity_id'
        )
        ->group('imageTB.entity_id');
   
        $dataDB = $connection->fetchAll($select);

        foreach ($dataDB as $item) {
            $val = (int)$item['imageProduct'];
            if ($val > 1) {
                $this->action->updateAttributes(
                    array($item['product_id']),
                    array('imageProduct' => '1'),
                    0
                );
            } else {
                $this->action->updateAttributes(
                    array($item['product_id']),
                    array('imageProduct' => $item['imageProduct']),
                    0
                );
            }
        }
        
        foreach (array_keys($this->config->getIndexers()) as $indexerId) {
                    $indexer = $this->indexerFactory->create();
                    $indexer->load($indexerId);
                    $indexer->reindexAll();
        }
    }
      /*
     * Works with a set of entity changed (may be massaction)
     */


    /**
     * Retrieve connection instance
     *
     * @return bool|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->connection) {
            $this->connection = $this->resource->getConnection();
        }

        return $this->connection;
    }
}
