<?php

namespace Bakery\Models\Products;

class Products
{
    public const BROWNIE = 1;
    public const LAMINGTON = 2;
    public const BLUEBERRY_MUFFIN = 3;
    public const CROISSANT = 4;
    public const CHOCOLATE_CAKE = 5;

    public const ITEM_NAMES = [
        self::BROWNIE => "Brownies         ",
        self::LAMINGTON => "Lamingtons       ",
        self::BLUEBERRY_MUFFIN => "Blueberry Muffins",
        self::CROISSANT => "Croissants       ",
        self::CHOCOLATE_CAKE => "Chocolate Cake   ",
    ];
}
