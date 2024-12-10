<?php

namespace DiamondIcq\Ringbuilder\Cron;

class RapnetDownloadDiamonds
{
    protected $curl;
    protected $resource;
    protected $connection;
    protected $date;
    protected $helper;
    protected $logger;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \DiamondIcq\Ringbuilder\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \DiamondIcq\Ringbuilder\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->curl = $curl;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->date =  $date;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    public function getDiamonds($diamond_type = 'White', $page_number = 1, $page_size = 50)
    {
        $requestUrl  = 'https://technet.rapaport.com/HTTP/JSON/RetailFeed/GetDiamonds.aspx';

        $payload = [
            "request" => [
                "header" => [
                    "username" => $this->helper->getRapnetUsername(),
                    "password" => $this->helper->getRapnetPassword()
                ],
                "body" => [
                    "search_type" => $diamond_type,
                    "page_number" => $page_number,
                    "page_size" => $page_size,
                    "sort_by" => "Size",
                    "sort_direction" => "Asc",
                ]
            ]
        ];

        $payload_json = json_encode($payload);

        $this->curl->addHeader("Content-Type", "application/x-www-form-urlencoded");
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOption(CURLOPT_TIMEOUT, 30);

        // post method
        $this->curl->post($requestUrl, $payload_json);

        // output of curl requestt
        $response = $this->curl->getBody();

        $result = json_decode($response);

        if (!isset($result->response->body->diamonds) || $result->response->header->error_code !== 0) {
            $this->logger->debug("Rapnet-GetDiamonds-Response: [[{$response}]]");
        }
        return $result;
    }

    public function execute()
    {
        $search_types = ['White', 'Fancy'];
        foreach ($search_types as $diamond_type) {
            $result = $this->getDiamonds($diamond_type, 1, 1);
            $total_diamonds = 0;
            if (!empty($result->response->body->search_results->total_diamonds_found)) {
                $total_diamonds = $result->response->body->search_results->total_diamonds_found;
                $this->logger->debug("total_diamonds_found: $total_diamonds");
            }
            $page_size = 50;
            $pages = ceil($total_diamonds / $page_size);
            $page = 1;

            $table_name = $this->resource->getTableName('rapnet_diamond');
            $select = $this->connection->select()
                ->from($table_name, "hash")
                ->where('diamond_id = :diamond_id');
            $diamond_fields = array_keys($this->connection->describeTable($table_name));
            $set_fields = [];
            foreach ($diamond_fields as $field_name) {
                $set_fields[$field_name] = "{$field_name} = :{$field_name}";
            }
            $set_sql = "%s {$table_name} SET " . implode(", ", $set_fields);
            $insert_sql = sprintf("{$set_sql}", "INSERT INTO");
            $update_sql = sprintf("{$set_sql} WHERE diamond_id = :diamond_id ", "UPDATE");
            $update2_sql = sprintf(
                "%s {$table_name} SET " .
                    "status = :status, diamond_type = :diamond_type, import_timestamp = :import_timestamp " .
                    "WHERE diamond_id = :diamond_id",
                "UPDATE"
            );

            $import_timestamp = $this->date->date()->format('Y-m-d H:i:s');
            $fail_page_count = 0;
            $fail_count = 0;
            while ($page <= $pages) {
                $result = $this->getDiamonds($diamond_type, $page, $page_size);
                if (empty($result->response->body->diamonds)) {
                    $fail_count++;
                    if ($fail_count <= 3) {
                        continue; // retry to load the same page up to 3 times
                    }
                    $fail_count = 0;
                    $page++;
                    $fail_page_count++;
                    if ($fail_page_count <= 10) {
                        continue; // continue to load next page up to 10 times
                    }
                    return $this; // end cron job
                } else {
                    $diamonds = $result->response->body->diamonds;
                }
                foreach ($diamonds as $idx => $diamond) {
                    // fix to shape value ex: "Cushion Modified" to "Cushion"
                    $_shape = explode(" ", $diamond->shape);
                    $diamond->shape = array_shift($_shape);

                    $select_bind = [':diamond_id' => $diamond->diamond_id];
                    $hash = $this->connection->fetchOne($select, $select_bind);
                    $new_hash = sha1(json_encode($diamond));
                    $diamond->import_timestamp = $import_timestamp;
                    $diamond->diamond_type = $diamond_type;
                    $diamond->hash = $new_hash;

                    $query_bind = [];
                    if ($hash === false) {
                        $sql = $insert_sql;
                        $diamond->status = 1; // new
                    } elseif ($hash == $new_hash) {
                        $sql = $update2_sql;
                        $diamond->status = 2; // no changes
                        $query_bind = [
                            ":diamond_id" => $diamond->diamond_id,
                            ":status" => $diamond->status,
                            ":diamond_type" => $diamond->diamond_type,
                            ":import_timestamp" => $diamond->import_timestamp,
                        ];
                    } else {
                        $sql = $update_sql;
                        $diamond->status = 3; // updated
                    }
                    if (empty($query_bind)) {
                        foreach ($diamond_fields as $field_name) {
                            $query_bind[":{$field_name}"] = (isset($diamond->$field_name)) ? $diamond->$field_name : '';
                        }
                    }
                    $this->connection->query($sql, $query_bind);
                }
                $page++;
            }

            // mark diamonds to be removed from the listing
            $sql = "%s {$table_name} SET status = 0 " .
                "WHERE status > 0 AND diamond_type = :diamond_type AND import_timestamp < :import_timestamp ";
            $sql = sprintf($sql, "UPDATE");
            $bind = [':diamond_type' => $diamond_type, ':import_timestamp' => $import_timestamp];
            $this->connection->query($sql, $bind);
        }

        return $this;
    }
}
