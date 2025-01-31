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
declare(strict_types=1);

namespace FME\ProductSorting\Elasticsearch;

use Magento\Elasticsearch\Model\Adapter\FieldsMappingPreprocessorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\AdvancedSearch\Model\Client\ClientInterface;

/**
 * Elasticsearch client
 */
class Elasticsearch implements ClientInterface
{
    /**
     * @var array
     */
    private $clientOptions;

    /**
     * Elasticsearch Client instances
     *
     * @var \Elasticsearch\Client[]
     */
    private $client;

    /**
     * @var bool
     */
    private $pingResult;

    /**
     * @var FieldsMappingPreprocessorInterface[]
     */
    private $fieldsMappingPreprocessors;

    /**
     * Initialize Elasticsearch 7 Client
     *
     * @param array $options
     * @param \Elasticsearch\Client|null $elasticsearchClient
     * @param array $fieldsMappingPreprocessors
     * @throws LocalizedException
     */
    public function __construct(
        $options = array(),
        $elasticsearchClient = null,
        $fieldsMappingPreprocessors = array()
    ) {
        if (empty($options['hostname']) || ((!empty($options['enableAuth']) &&
            ($options['enableAuth'] == 1)) && (empty($options['username']) || empty($options['password'])))) {
            throw new LocalizedException(
                __('The search failed because of a search engine misconfiguration.')
            );
        }

        // phpstan:ignore
        if ($elasticsearchClient instanceof \Elasticsearch\Client) {
            $this->client[getmypid()] = $elasticsearchClient;
        }

        $this->clientOptions = $options;
        $this->fieldsMappingPreprocessors = $fieldsMappingPreprocessors;
    }

    /**
     * Execute suggest query for Elasticsearch 7
     *
     * @param array $query
     * @return array
     */
    public function suggest(array $query) : array
    {
        return $this->getElasticsearchClient()->suggest($query);
    }

    /**
     * Get Elasticsearch 7 Client
     *
     * @return \Elasticsearch\Client
     */
    private function getElasticsearchClient(): \Elasticsearch\Client
    {
        $pid = getmypid();
        if (!isset($this->client[$pid])) {
            $config = $this->buildESConfig($this->clientOptions);
            $this->client[$pid] = \Elasticsearch\ClientBuilder::fromConfig($config, true);
        }

        return $this->client[$pid];
    }

    /**
     * Ping the Elasticsearch 7 client
     *
     * @return bool
     */
    public function ping() : bool
    {
        if ($this->pingResult === null) {
            $this->pingResult = $this->getElasticsearchClient()
            ->ping(array('client' => array('timeout' => $this->clientOptions['timeout'])));
        }

        return $this->pingResult;
    }

    /**
     * Validate connection params for Elasticsearch 7
     *
     * @return bool
     */
    public function testConnection() : bool
    {
        return $this->ping();
    }

    /**
     * Build config for Elasticsearch 7
     *
     * @param array $options
     * @return array
     */
    private function buildESConfig(array $options = array()) : array
    {
        $hostname = preg_replace('/http[s]?:\/\//i', '', $options['hostname']);
        // @codingStandardsIgnoreStart
        $protocol = parse_url($options['hostname'], PHP_URL_SCHEME);
        // @codingStandardsIgnoreEnd
        if (!$protocol) {
            $protocol = 'http';
        }

        $authString = '';
        if (!empty($options['enableAuth']) && (int)$options['enableAuth'] === 1) {
            $authString = "{$options['username']}:{$options['password']}@";
        }

        $portString = '';
        if (!empty($options['port'])) {
            $portString = ':' . $options['port'];
        }

        $host = $protocol . '://' . $authString . $hostname . $portString;

        $options['hosts'] = array($host);

        return $options;
    }

    /**
     * Performs bulk query over Elasticsearch 7  index
     *
     * @param array $query
     * @return void
     */
    public function bulkQuery(array $query)
    {
        $this->getElasticsearchClient()->bulk($query);
    }

    /**
     * Creates an Elasticsearch 7 index.
     *
     * @param string $index
     * @param array $settings
     * @return void
     */
    public function createIndex(string $index, array $settings)
    {
        $this->getElasticsearchClient()->indices()->create(
            array(
                'index' => $index,
                'body' => $settings,
            )
        );
    }

    /**
     * Delete an Elasticsearch 7 index.
     *
     * @param string $index
     * @return void
     */
    public function deleteIndex(string $index)
    {
        $this->getElasticsearchClient()->indices()->delete(array('index' => $index));
    }

    /**
     * Check if index is empty.
     *
     * @param string $index
     * @return bool
     */
    public function isEmptyIndex(string $index) : bool
    {
        $stats = $this->getElasticsearchClient()->indices()->stats(array('index' => $index, 'metric' => 'docs'));
        if ($stats['indices'][$index]['primaries']['docs']['count'] ===  0) {
            return true;
        }

        return false;
    }

    /**
     * Updates alias.
     *
     * @param string $alias
     * @param string $newIndex
     * @param string $oldIndex
     * @return void
     */
    public function updateAlias(string $alias, string $newIndex, string $oldIndex = '')
    {
        $params = array(
            'body' => array(
                'actions' => array()
            )
        );
        if ($oldIndex) {
            $params['body']['actions'][] = array('remove' => array('alias' => $alias, 'index' => $oldIndex));
        }

        if ($newIndex) {
            $params['body']['actions'][] = array('add' => array('alias' => $alias, 'index' => $newIndex));
        }

        $this->getElasticsearchClient()->indices()->updateAliases($params);
    }

    /**
     * Checks whether Elasticsearch 7 index exists
     *
     * @param string $index
     * @return bool
     */
    public function indexExists(string $index) : bool
    {
        return $this->getElasticsearchClient()->indices()->exists(array('index' => $index));
    }

    /**
     * Exists alias.
     *
     * @param string $alias
     * @param string $index
     * @return bool
     */
    public function existsAlias(string $alias, string $index = '') : bool
    {
        $params = array('name' => $alias);
        if ($index) {
            $params['index'] = $index;
        }

        return $this->getElasticsearchClient()->indices()->existsAlias($params);
    }

    /**
     * Get alias.
     *
     * @param string $alias
     * @return array
     */
    public function getAlias(string $alias) : array
    {
        return $this->getElasticsearchClient()->indices()->getAlias(array('name' => $alias));
    }

    /**
     * Add mapping to Elasticsearch 7 index
     *
     * @param array $fields
     * @param string $index
     * @param string $entityType
     * @return void
     */
    public function addFieldsMapping(array $fields, string $index, string $entityType)
    {
        $params = array(
            'index' => $index,
            'type' => $entityType,
            'include_type_name' => true,
            'body' => array(
                $entityType => array(
                    'properties' => array(),
                    'dynamic_templates' => array(
                        array(
                            'price_mapping' => array(
                                'match' => 'price_*',
                                'match_mapping_type' => 'string',
                                'mapping' => array(
                                    'type' => 'float',
                                    'store' => true,
                                ),
                            ),
                        ),
                        array(
                            'position_mapping' => array(
                                'match' => 'position_*',
                                'match_mapping_type' => 'string',
                                'mapping' => array(
                                    'type' => 'integer',
                                    'index' => true,
                                ),
                            ),
                        ),
                        array(
                            'string_mapping' => array(
                                'match' => '*',
                                'match_mapping_type' => 'string',
                                'mapping' => array(
                                    'type' => 'text',
                                    'index' => true,
                                    'copy_to' => '_search'
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        foreach ($this->applyFieldsMappingPreprocessors($fields) as $field => $fieldInfo) {
            $params['body'][$entityType]['properties'][$field] = $fieldInfo;
        }

        $this->getElasticsearchClient()->indices()->putMapping($params);
    }

    /**
     * Execute search by $query
     *
     * @param array $query
     * @return array
     */
    public function query(array $query) : array
    {

        
        if (isset($query['body']['sort'])) {
            unset($query['body']['sort']);
        }

        // echo "<pre>";
        // print_r($query);
        // exit;
        return $this->getElasticsearchClient()->search($query);
    }
    
    /**
     * Delete mapping in Elasticsearch 7 index
     *
     * @param string $index
     * @param string $entityType
     * @return void
     */
    public function deleteMapping(string $index, string $entityType)
    {
        $this->getElasticsearchClient()->indices()->deleteMapping(
            array(
                'index' => $index,
                'type' => $entityType,
            )
        );
    }

    /**
     * Apply fields mapping preprocessors
     *
     * @param array $properties
     * @return array
     */
    private function applyFieldsMappingPreprocessors(array $properties): array
    {
        foreach ($this->fieldsMappingPreprocessors as $preprocessor) {
            $properties = $preprocessor->process($properties);
        }

        return $properties;
    }
}
