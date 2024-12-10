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
namespace FME\ProductSorting\Cron;

class Run
{

     /**
      * @var \Magento\Indexer\Model\IndexerFactory
      */
    protected $indexerFactory;
    /**
     * @var \Magento\Framework\Indexer\ConfigInterface
     */
    protected $config;
    protected $_logger;
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Framework\Indexer\ConfigInterface $config
    ) {
        $this->_logger = $logger;
        $this->indexerFactory = $indexerFactory;
        $this->config = $config;
    }
    public function execute()
    {
        $aa='cron';

        $result = json_encode($aa, true);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customCron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($result);
        $array = array('catalogsearch_fulltext','catalog_product_price');
        foreach (array_keys($this->config->getIndexers()) as $indexerId) {
                    $indexer = $this->indexerFactory->create();
                    $indexer->load($indexerId);
                    $indexer->reindexAll();
        }

        $this->_logger->info('Bs_Cron has been run successfully');
        return $this;
    }
}
