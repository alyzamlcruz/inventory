<?php
/**
 * Created by Alyza Cruz.
 * User: Lenovo
 * Date: 4/28/2019
 * Time: 2:33 PM
 */

namespace Bakery\Models\Product;

class Product
{
    private const ID = "productId";
    private const SOLD = "sold";
    private const PENDING = "pending";
    private const RECEIVED = "received";
    private const STOCK = "stock";
    private const DATE_ORDERED = "date_ordered";

    public $productId;
    public $sold;
    public $pending;
    public $received;
    public $stock;
    public $dateOrdered;

    /**
     * Product constructor.
     * @param array $productDetails
     */
    public function __construct(array $productDetails)
    {

        $this->init($productDetails);

    }

    private function init(array $productDetails)
    {

        $this->setId($productDetails[0]);
        $this->setSold($productDetails[1]);
        $this->setPending($productDetails[2]);
        $this->setReceived($productDetails[3]);
        $this->setStock($productDetails[4]);
        $this->setDateOrdered($productDetails[5]);

    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->productId;
    }

    /**
     * @param mixed $id
     * @return Product
     */
    public function setId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSold(): int
    {
        return $this->sold;
    }

    /**
     * @param mixed $sold
     * @return Product
     */
    public function setSold($sold)
    {
        $this->sold = $sold;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPending(): int
    {
        return $this->pending;
    }

    /**
     * @param mixed $pending
     * @return Product
     */
    public function setPending($pending)
    {
        $this->pending = $pending;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReceived(): int
    {
        return $this->received;
    }

    /**
     * @param mixed $received
     * @return Product
     */
    public function setReceived($received)
    {
        $this->received = $received;
        return $this;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param mixed $stock
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateOrdered()
    {
        return $this->dateOrdered;
    }

    /**
     * @param mixed $dateOrdered
     * @return Product
     */
    public function setDateOrdered($dateOrdered)
    {
        $this->dateOrdered = $dateOrdered;
        return $this;
    }

}

