<?php

namespace Bakery\Contracts;

interface ProductsPurchasedInterface
{
    /**
     * @param int $productId
     * @return int
     */
    public function getPurchasedReceivedTotal(int $productId): int;

    /**
     * @param int $productId
     * @return int
     */
    public function getPurchasedPendingTotal(int $productId): int;

    /**
     * @param int $day
     */
    public function receivePendingStocks(int $day): void;
}
