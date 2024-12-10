<?php

namespace Customer\Sell\Api;

use Customer\Sell\Api\Data\SellInterface;

interface SellRepositoryInterface
{
    /**
     * Save sell data submitted by user from frontend
     * @param   \Customer\Sell\Api\Data\SellInterface          $sell
     * @return  \Customer\Sell\Api\Data\SellInterface          $sell
     * @throws  \Magento\Framework\Exception\LocalizedException
     */
    public function save(SellInterface $sell);

    /**
     * Retrieve list of sell data
     * @param   \Customer\Sell\Api\Data\SellInterface          $sell
     * @return  \Customer\Sell\Api\Data\SellInterface          $sell
     * @throws  \Magento\Framework\Exception\LocalizedException
     */
    public function getList();

    /**
     * Get Sell by ID
     * @param  int $id
     * @return  \Customer\Sell\Api\Data\SellInterface
     * @throws  NoSuchEntityException
     */
    public function getSellById($id);
}
