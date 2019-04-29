<?php
/**
 * Created by Alyza Cruz.
 * User: Lenovo
 * Date: 4/28/2019
 * Time: 2:08 PM
 */

namespace Bakery;
require_once "loader.php";

use Bakery\Contracts\InventoryInterface;
use Bakery\Contracts\OrderProcessorInterface;
use Bakery\Contracts\ProductsPurchasedInterface;
use Bakery\Contracts\ProductsSoldInterface;
use Bakery\Models\Products\Products;
use Bakery\Models\Product\Product;

class Bakery implements OrderProcessorInterface, InventoryInterface, ProductsPurchasedInterface, ProductsSoldInterface
{
    const DEFAULT_FILE = "../orders-sample.json";
    /**
     * @var array of product stocks
     */
    private $productStocks = [];

    /**
     * Bakery constructor.
     */
    public function __construct(string $filename = null)
    {
        $orderList = $filename ?: self::DEFAULT_FILE;
        $this->productStocks = $this->initStocks();
        $this->processFromJson($orderList);
        $this->weeklySummary();
    }

    /**
     * @param string $filePath
     */
    public function processFromJson(string $filePath): void
    {
        $batch = $this->parseOrderFromFile($filePath);

        foreach ($batch as $day => $orders) {

            $this->receivePendingStocks($day);
            $this->processOrders($orders, $day);

        }
    }

    /**
     * Gets current stock level of product with given ID
     * @param int $productId
     * @return int
     */
    public function getStockLevel(int $productId): int
    {
        $product = $this->productStocks[$productId];
        return $product->getStock();
    }

    /**
     * Gets current quantity received of product with given ID
     * @param int $productId
     * @return int
     */
    public function getPurchasedReceivedTotal(int $productId): int
    {
        $product = $this->productStocks[$productId];
        return $product->getReceived();
    }

    /**
     * Gets current quantity purchased pending of product with given ID
     * @param int $productId
     * @return int
     */
    public function getPurchasedPendingTotal(int $productId): int
    {
        $product = $this->productStocks[$productId];
        return $product->getPending();
    }

    /**
     * Gets total sold quantity of product with given ID
     * @param int $productId
     * @return int
     */
    public function getSoldTotal(int $productId): int
    {
        $product = $this->productStocks[$productId];
        return $product->getSold();
    }

    /**
     * Creates order for product with depleted stock level
     * without pending order
     * @param Product $product
     * @param int $day
     * @param int $newStock
     * @return Product
     */
    public function createOrder(Product $product, int $day, int $newStock): Product
    {
        $productId = $product->getId();
        $pending = $this->getPurchasedPendingTotal($productId);

        if ($newStock < 10 && $pending == 0) {
            $product->setPending($pending + 20);
            $product->setDateOrdered($day);
        }

        return $product;
    }

    /**
     * Filters invalid orders out
     * @param array $orders
     * @return bool
     */
    public function filterInvalidOrders(array $order): bool
    {

        foreach ($order as $productId => $quantity) {
            $stock = $this->getStockLevel($productId);
            if ($stock < $quantity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse orders from order file.
     * Checks for file existence and content validity.
     * @param string $filePath
     * @return array
     */
    private function parseOrderFromFile(string $filePath): array
    {
        if (! file_exists($filePath)) {
            exit("File does not exist.");
        }

        try{
            $contents = file_get_contents($filePath);
            $orders = json_decode($contents, true);
        } catch (\Exception $e) {
            exit("Error encountered in opening file.");
        }

        if (empty($orders)) {
            exit("Order list empty.");
        }

        return $orders;
    }

    /**
     * Filter invalid orders.
     * Update stock levels and sold quantity.
     * Create order for depleted stocks
     * @param $items
     * @param $day
     */
    private function processOrders(array $orders, int $day): void
    {
        foreach ($orders as $order) {
            if (! $this->filterInvalidOrders($order)) {
                continue;
            }

            foreach ($order as $productId => $quantity) {
                $product = $this->productStocks[$productId];

                $stock = $this->getStockLevel($productId);
                $sold = $this->getSoldTotal($productId);

                $newStock = $stock - $quantity;
                $product->setStock($newStock);
                $product->setSold($sold + $quantity);

                $product = $this->createOrder($product, $day, $newStock);

                $this->productStocks[$productId] = $product;
            }
        }
    }

    /**
     * @return array of initialized stocks per product
     * -id
     * -sold
     * -pending
     * -received
     * -stock
     * -date_ordered
     */
    private function initStocks()
    {
        return [
            Products::BROWNIE => new Product([Products::BROWNIE, 0, 0, 0, 20, 0]),
            Products::LAMINGTON => new Product([Products::LAMINGTON, 0, 0, 0, 20, 0]),
            Products::BLUEBERRY_MUFFIN => new Product([Products::BLUEBERRY_MUFFIN, 0, 0, 0, 20, 0]),
            Products::CROISSANT => new Product([Products::CROISSANT, 0, 0, 0, 20, 0]),
            Products::CHOCOLATE_CAKE => new Product([Products::CHOCOLATE_CAKE, 0, 0, 0, 20, 0])
        ];

    }

    /**
     * Receive stocks that have been ordered two days prior.
     * @param int $day
     */
    public function receivePendingStocks(int $day): void
    {
        array_walk($this->productStocks,
            function($product, $productId) use ($day) {
                $pending = $this->getPurchasedPendingTotal($productId);
                $stock = $this->getStockLevel($productId);

                $dateOrdered = $product->getDateOrdered();
                $received = $product->getReceived();

                if ($pending > 0 && abs($day - $dateOrdered) >= 2) {
                    $product->setStock($stock + $pending);
                    $product->setReceived($received + $pending);
                    $product->setPending(0);
                }

                $this->productStocks[$productId] = $product;
            }
        );
    }

    /**
     * Prints out a summary of the orders made for the whole week.
     */
    private function weeklySummary(): void
    {
        foreach ($this->productStocks as $productId => $product) {
            echo Products::ITEM_NAMES[$productId] . ": sold = " . $this->getSoldTotal($productId)
                . ", pending = " . $this->getPurchasedPendingTotal($productId)
                . ", received = " . $this->getPurchasedReceivedTotal($productId)
                . ", stock = " . $this->getStockLevel($productId) . PHP_EOL;
        }
    }

}

if (php_sapi_name() == 'cli') {
    new Bakery();
}
