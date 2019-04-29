<?php

namespace Bakery\Contracts;

use Bakery\Models\Product\Product;

interface OrderProcessorInterface
{
    /**
     * This function receives the path of the json for all the orders of the week,
     * processes all orders for the week,
     * while keeping track of stock levels, units sold and purchased
     * See `orders-sample.json` for example
     *
     * @param string $filePath
     */
    public function processFromJson(string $filePath): void;

    /**
     * @param Product $product
     * @param int $day
     * @param int $newStock
     * @return Product
     */
    public function createOrder(Product $product, int $day, int $newStock): Product;

    /**
     * @param array $order
     * @return array
     */
    public function filterInvalidOrders(array $order): bool;
}
